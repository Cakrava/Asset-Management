@extends('layout.sidebar')
@section('content')

    <!-- [Page specific CSS] start -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- [MODIFIKASI] Menambahkan CSS SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- [Page specific CSS] end -->

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item">Stored Device</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="margin-bottom : -15px">
                        <h5>Data Stored Device</h5>
                        <small class="text-muted">Data ini berisi daftar perangkat yang disimpan di gudang, sejauh ini sebanyak</small>
                        <small style="color: blue"> {{ $storedDevices->count() }} data perangkat</small>
                        <small class="text-muted"> tersimpan</small>

                        <div class="mt-3">
                            @if (!session()->has('profile_incomplete') && auth()->user()->role === 'admin')
                            <button type="button" class="btn btn-primary" style="margin-bottom: -10px" id="btn-new-stored-device">
                                <i class="ti ti-plus"></i> New Stored Device
                            </button>
                            <button type="button" class="btn btn-danger" style="margin-bottom: -10px" id="btn-bulk-delete" disabled>
                                <i class="ti ti-trash"></i> Bulk Delete
                            </button>
                            @endif

                            @if (session()->has('warning-stored-device'))
                                <div class="alert alert-warning" style="margin-top: 20px; position: relative;">
                                    <p>⚠️ <strong>Peringatan!</strong></p>
                                    {{ session('warning-stored-device') }}
                                </div>
                            @endif
                            @if (session()->has('success'))
                                <div class="alert alert-success" style="margin-top: 20px; margin-bottom : -20px">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session()->has('profile_incomplete'))
                                <div class="alert alert-primary" style="margin-top: 20px; margin-bottom : -20px">
                                    {!! session('profile_incomplete') !!}
                                </div>
                            @endif
                            <div style="clear: both;"></div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="filter-navigation filter-v1 mb-4">
                            <button type="button" class="filter-btn active" data-filter="all">All</button>
                            <button type="button" class="filter-btn" data-filter="useable">Useable</button>
                            <button type="button" class="filter-btn" data-filter="damaged">Damaged</button>
                        </div>
                        
                        <style>
                            .filter-v1 .filter-btn { background: none; border: none; padding: 8px 15px; border-radius: 0; color: #495057; cursor: pointer; transition: color 0.3s ease; position: relative; }
                            .filter-v1 .filter-btn:hover { color: #0EA2BC; }
                            .filter-v1 .filter-btn.active { color: #0EA2BC; }
                            .filter-v1 .filter-btn.active::after { content: ''; position: absolute; bottom: -8px; left: 0; width: 100%; height: 2px; background-color: #0EA2BC; border-radius: 2px 2px 0 0; }
                        </style>

                        <div class="dt-responsive">
                            <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                                        <th>Device Brand</th>
                                        <th>Device Model</th>
                                        <th>Device Type</th>
                                        <th>Stock</th>
                                        <th>Activity</th>
                                        <th>Condition</th>
                                        <th>Last Changed</th>
                                        @if (auth()->user()->role === 'admin')
                                        <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $highlighted = false; @endphp
                                    @foreach($storedDevices as $storedDevice)
                                        @php
                                            $isRecentUpdate = false;
                                            if (!$highlighted && $storedDevice->updated_at && $storedDevice->updated_at->diffInSeconds(\Carbon\Carbon::now()) <= 30) {
                                                $isRecentUpdate = true;
                                                $highlighted = true;
                                            }
                                        @endphp
                                        <tr class="{{ $isRecentUpdate ? 'highlight-row' : '' }}">
                                            <td><input class="form-check-input stored-device-checkbox" type="checkbox" value="{{ $storedDevice->id }}"></td>
                                            <td>{{ $storedDevice->device->brand ?? 'N/A' }}</td>
                                            <td>{{ $storedDevice->device->model ?? 'N/A' }}</td>
                                            <td>{{ Str::of($storedDevice->device->type ?? 'N/A')->replace('_', ' ')->title() }}</td>
                                            <td>{{ $storedDevice->stock }}</td>
                                            <td>
                                                @if($storedDevice->previous_stock !== null)
                                                    @if($storedDevice->stock > $storedDevice->previous_stock) <span style="color: green;"><i class="ti ti-arrow-up"></i> Up</span>
                                                    @elseif($storedDevice->stock < $storedDevice->previous_stock) <span style="color: red;"><i class="ti ti-arrow-down"></i> Down</span>
                                                    @else <span style="color: grey;"><i class="ti ti-circle"></i> Stable</span>
                                                    @endif
                                                @else <span style="color: grey;"><i class="ti ti-circle"></i> New</span>
                                                @endif
                                            </td>
                                            @php
                                                $conditionColor = match($storedDevice->condition) { 'Baru' => '#578FCA', 'Bekas' => 'orange', 'Rusak' => '#ff4d4f', default => '#999' };
                                            @endphp
                                            <td>
                                                <div style="padding: 4px 8px; border-radius: 6px; text-align: center; font-size: 13px; font-weight: 500; background-color: #fff; color: {{ $conditionColor }}; border: 1px solid {{ $conditionColor }}; display: inline-block; min-width: 70px;">
                                                    {{ $storedDevice->condition }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($storedDevice->updated_at)
                                                    {{ $storedDevice->updated_at->diffInDays() <= 30 ? $storedDevice->updated_at->diffForHumans(['locale' => 'id']) : $storedDevice->updated_at->format('d/m/Y') }}
                                                @else <span class="text-muted">Tidak pernah diubah</span>
                                                @endif
                                            </td>
                                            @if (auth()->user()->role === 'admin')
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-info btn-update-stored-device" data-id="{{ $storedDevice->id }}">
                                                        <i class="ti ti-refresh"></i> Restock
                                                    </button>
                                                    <form class="form-delete-stored-device" action="{{ route('panel.stored-device.destroy', $storedDevice->id) }}" method="POST" data-stored-device-id="{{ $storedDevice->id }}">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-stored-device" data-stored-device-id="{{ $storedDevice->id }}">
                                                            <i class="ti ti-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->

    <!-- [MODIFIKASI] Menghapus HTML untuk #warningConfirmationModal -->

    <div class="modal fade" id="storedDeviceModal" tabindex="-1" aria-labelledby="storedDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="storedDeviceModalLabel">New Stored Device</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <form id="storedDeviceForm" action="{{ route('panel.stored-device.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="device_id" class="form-label">Device Name</label>
                            <select class="form-select" id="device_id" name="device_id" style="width: 100%;">
                                <option selected disabled>Select Device Name</option>
                                @foreach($devices as $device)
                                <option value="{{ $device->id }}">{{ $device->brand }} - {{ $device->model }} ({{ $deviceTypeNames[$device->type] ?? 'Unknown' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3"><label for="device_id_readonly" class="form-label">Device ID</label><input type="text" class="form-control" id="device_id_readonly" readonly></div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label for="stock" class="form-label">Stock</label><input type="number" class="form-control" id="stock" name="stock" value="1" min="1"></div>
                            <div class="col-md-6">
                                <label for="condition" class="form-label">Condition</label>
                                <select class="form-select" id="condition" name="condition">
                                    <option selected disabled>Select Condition</option>
                                    <option value="Baru">Baru</option><option value="Bekas">Bekas</option><option value="Rusak">Rusak</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveStoredDeviceBtn">Save</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="restockModalLabel">Restock Device</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <form id="restockForm" action="{{ route('panel.stored-device.update') }}" method="POST">
                        @csrf @method('POST')
                        <input type="hidden" name="stored_id" id="stored_id">
                        <div class="mb-3"><label for="stock" class="form-label">Stock</label><input type="number" class="form-control" id="stock" name="stock" value="1" min="1"></div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveRestockBtn">Save</button></div>
            </div>
        </div>
    </div>
    
    <!-- End Modals -->

    <!-- [Page Specific JS] start -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- [MODIFIKASI] Menambahkan script SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            var table = $('#dom-jqry').DataTable({
                "dom": '<"row justify-content-between"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6 text-end"p>>',
                "columnDefs": [{"orderable": false, "targets": 0, "className": 'dt-body-center'}]
            });

            $('#device_id').select2({ dropdownParent: $('#storedDeviceModal') });
            $('#device_id').on('change', function () { $('#device_id_readonly').val($(this).val()); });

            const warningOptions = {
                title: 'Peringatan!',
                html: "<p>Sangat tidak direkomendasikan untuk mengubah data secara manual tanpa melalui proses event.</p><p>Apakah Anda yakin ingin melanjutkan?</p>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            };

            // [MODIFIKASI] Event untuk tombol "New Stored Device"
            $('#btn-new-stored-device').on('click', function () {
                Swal.fire(warningOptions).then((result) => {
                    if (result.isConfirmed) {
                        $('#storedDeviceModal').modal('show');
                    }
                });
            });

            // [MODIFIKASI] Event untuk tombol "Update/Restock"
            $('.btn-update-stored-device').on('click', function () {
                var storedDeviceId = $(this).data('id');
                // Pre-fill data sebelum menampilkan konfirmasi
                $('#restockModalLabel').text('Restock Device');
                $('#restockForm').attr('action', "{{ route('panel.stored-device.update') }}");
                $('#restockForm')[0].reset();
                $('#stored_id').val(storedDeviceId);

                $.ajax({
                    url: '/stored-device/' + storedDeviceId, type: 'GET',
                    success: function (response) { $('#restockModal #stock').val(response.stock); },
                    error: function (xhr) { console.error('Error fetching stored device data:', xhr); }
                });

                Swal.fire(warningOptions).then((result) => {
                    if (result.isConfirmed) {
                        $('#restockModal').modal('show');
                        $('#restockModal').on('shown.bs.modal', function () { $(this).find('#stock').focus(); });
                    }
                });
            });

            // [MODIFIKASI] Event untuk tombol "Delete" per baris
            $('.btn-delete-stored-device').on('click', function (e) {
                e.preventDefault();
                var storedDeviceId = $(this).data('stored-device-id');
                var form = $('.form-delete-stored-device[data-stored-device-id="' + storedDeviceId + '"]');

                Swal.fire({
                    title: 'Anda Yakin?',
                    html: "<p>Data perangkat ini akan dihapus permanen.</p><p><b>Peringatan:</b> Tindakan ini sangat tidak direkomendasikan.</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // [MODIFIKASI] Event untuk tombol "Bulk Delete"
            $('#btn-bulk-delete').on('click', function () {
                var selectedIds = $('.stored-device-checkbox:checked').map(function() { return $(this).val(); }).get();
                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: `Hapus ${selectedIds.length} Perangkat?`,
                    html: "<p>Semua perangkat yang dipilih akan dihapus permanen.</p><p><b>Peringatan:</b> Tindakan ini sangat tidak direkomendasikan.</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('panel.stored-device.bulkDestroy') }}', type: 'POST',
                            data: { _token: '{{ csrf_token() }}', ids: selectedIds },
                            success: function () { window.location.reload(); },
                            error: function (xhr) {
                                console.error('Error during bulk delete:', xhr);
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        });
                    }
                });
            });
            
            // --- Logika yang tidak berubah ---
            $('#saveStoredDeviceBtn').on('click', function (e) {
                e.preventDefault();
                var form = $('#storedDeviceForm');
                $.ajax({
                    url: form.attr('action'), type: 'POST', data: form.serialize(),
                    success: function () { $('#storedDeviceModal').modal('hide'); window.location.reload(); },
                    error: function (xhr) { alert(xhr.responseJSON?.message || 'Error saving data.'); }
                });
            });

            function submitRestockForm() {
                var form = $('#restockForm');
                $.ajax({
                    url: form.attr('action'), type: 'POST', data: form.serialize(),
                    success: function () { $('#restockModal').modal('hide'); window.location.reload(); },
                    error: function (xhr) { alert(xhr.responseJSON?.message || 'Error saving data.'); }
                });
            }
            $('#saveRestockBtn').on('click', submitRestockForm);
            $('#restockModal #stock').on('keypress', function (e) { if (e.which === 13) { e.preventDefault(); submitRestockForm(); } });

            function updateBulkDeleteButtonState() { $('#btn-bulk-delete').prop('disabled', $('.stored-device-checkbox:checked').length === 0); }
            $('#selectAllCheckbox').on('click', function () { $('.stored-device-checkbox').prop('checked', $(this).prop('checked')); updateBulkDeleteButtonState(); });
            $('.stored-device-checkbox').on('click', function () { updateBulkDeleteButtonState(); $('#selectAllCheckbox').prop('checked', $('.stored-device-checkbox:checked').length === $('.stored-device-checkbox').length); });

            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active'); $(this).addClass('active');
                var filterValue = $(this).data('filter');
                var conditionColumnIndex = 6; // Indeks kolom 'Condition' (mulai dari 0)
                
                if (filterValue === 'useable') {
                    table.column(conditionColumnIndex).search('Baru|Bekas', true, false).draw();
                } else if (filterValue === 'damaged') {
                    table.column(conditionColumnIndex).search('Rusak', true, false).draw();
                } else { // 'all'
                    table.column(conditionColumnIndex).search('').draw();
                }
            });
        });
    </script>
    <style>
        .highlight-row { background-color: #eaf6ff; }
    </style>
@endsection