@extends('layout.sidebar')
@section('content')

    <!-- [Page specific CSS] start -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
                                <li class="breadcrumb-item">Device Name</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="margin-bottom : -15px">
                        <h5>Data Device Name</h5>
                        <small class="text-muted">Data ini berisi daftar nama perangkat yang akan dimasukkan ke dalam sistem inventaris. Saat ini</small> <small class="" style="color: rgb(8, 0, 255)">
                            {{ count($devices) }}
                            nama perangkat </small><small class="text-muted">telah tercatat.</small>

                        <div class="mt-3">
                            @if (!session()->has('profile_incomplete'))
                                <button type="button" class="btn btn-primary" style="margin-bottom: -10px" data-bs-toggle="modal" data-bs-target="#deviceModal" id="btn-new-device">
                                    <i class="ti ti-plus"></i> New Device Name
                                </button>
                                <button type="button" class="btn btn-danger" style="margin-bottom: -10px" id="btn-bulk-delete" disabled>
                                    <i class="ti ti-trash"></i> Bulk Delete
                                </button>
                            @endif

                          
                            @if (session()->has('profile_incomplete'))
                                <div class="alert alert-primary" style="margin-top: 20px; margin-bottom : -20px">{!! session('profile_incomplete') !!}</div>
                            @endif
                            <div style="clear: both;"></div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="dt-responsive">
                            <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th>Type</th>
                                        <th>Waktu Penambahan</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $highlighted = false; @endphp
                                    @foreach($devices as $device)
                                        @php
                                            $isRecentUpdate = false;
                                            if (!$highlighted && $device->updated_at && $device->updated_at->diffInSeconds(\Carbon\Carbon::now()) <= 30) {
                                                $isRecentUpdate = true;
                                                $highlighted = true;
                                            }
                                        @endphp
                                        <tr class="{{ $isRecentUpdate ? 'highlight-row' : '' }}">
                                            <td><input class="form-check-input device-checkbox" type="checkbox" value="{{ $device->id }}"></td>
                                            <td>{{ $device->brand }}</td>
                                            <td>{{ $device->model }}</td>
                                            <td>{{ $deviceTypeNames[$device->type] ?? 'Tidak Diketahui' }}</td>
                                            <td>
                                                @if($device->created_at)
                                                    {{ $device->created_at->diffInDays() <= 30 ? $device->created_at->diffForHumans(['locale' => 'id']) : $device->created_at->format('d/m/Y') }}
                                                @else <span class="text-muted">Tidak ada informasi waktu</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-primary btn-update-device" data-bs-toggle="modal" data-bs-target="#deviceModal" data-id="{{ $device->id }}">
                                                        <i class="ti ti-pencil"></i> Update
                                                    </button>
                                                    <form class="form-delete-device" action="{{ route('panel.device.destroy', $device->id) }}" method="POST" data-device-id="{{ $device->id }}">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-device" data-device-id="{{ $device->id }}">
                                                            <i class="ti ti-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
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
    <div class="modal fade" id="deviceModal" tabindex="-1" aria-labelledby="deviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="deviceModalLabel">New Device Name</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <form id="deviceForm" action="{{ route('panel.device.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="device_id" id="device_id">
                        <div class="row mb-3">
                            <div class="col-md-6"><label for="brand" class="form-label">Merek</label><input type="text" class="form-control" id="brand" name="brand" placeholder="Contoh: TP-Link"></div>
                            <div class="col-md-6"><label for="model" class="form-label">Model</label><input type="text" class="form-control" id="model" name="model" placeholder="Contoh: WR820N"></div>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select class="form-select" id="type" name="type" style="width: 100%;">
                                <option selected disabled>Select Device Type</option>
                                <option value="router">Router</option>
                                <option value="access_point">Access Point</option>
                                <option value="repeater">Repeater</option>
                                <option value="switch">Switch</option>
                                <option value="modem">Modem</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveDeviceBtn">Save</button></div>
            </div>
        </div>
    </div>


    <!-- [Page Specific JS] start -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            var table = $('#dom-jqry').DataTable({
                "dom": '<"row justify-content-between"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6 text-end"p>>',
                "columnDefs": [{"orderable": false, "targets": 0, "className": 'dt-body-center'}]
            });
            $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

            $('#type').select2({ dropdownParent: $('#deviceModal') });

            $('#btn-new-device').on('click', function () {
                $('#deviceModalLabel').text('New Device Name');
                $('#deviceForm').attr('action', "{{ route('panel.device.store') }}");
                $('#deviceForm')[0].reset();
                $('#device_id').val(''); // Pastikan device_id kosong
                $('#type').val(null).trigger('change');
            });

            $('.btn-update-device').on('click', function () {
                var deviceId = $(this).data('id');
                $('#deviceModalLabel').text('Update Device Name');
                $('#deviceForm').attr('action', "{{ route('panel.device.update') }}");
                $('#device_id').val(deviceId);
                $.ajax({
                    url: '/device/' + deviceId, type: 'GET',
                    success: function (response) {
                        $('#brand').val(response.brand);
                        $('#model').val(response.model);
                        $('#type').val(response.type.toLowerCase().replace(/ /g, '_')).trigger('change');
                    },
                    error: function (xhr) { alert('Failed to fetch device data for update.'); }
                });
            });

            // [MODIFIKASI] Menambahkan SweetAlert pada proses simpan dan update
            $('#saveDeviceBtn').on('click', function (e) {
                e.preventDefault();
                var form = $('#deviceForm');
                var isUpdate = !!$('#device_id').val();

                $.ajax({
                    url: form.attr('action'), 
                    type: 'POST', 
                    data: form.serialize(),
                    success: function () { 
                        $('#deviceModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: isUpdate ? 'Data perangkat berhasil diperbarui.' : 'Data perangkat baru berhasil disimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function (xhr) { 
                        $('#deviceModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.'
                        });
                    }
                });
            });

            // [MODIFIKASI] Mengubah form.submit() menjadi AJAX untuk notifikasi
            $('.btn-delete-device').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Peringatan!',
                    html: "<p>Anda akan menghapus sebuah <b>Data Master</b>.</p><p>Menghapus data ini dapat menyebabkan inkonsistensi pada data inventaris atau data lain yang terhubung. Apakah Anda benar-benar yakin?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Data Ini!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST', // Menggunakan POST karena form memiliki method spoofing @method('DELETE')
                            data: form.serialize(),
                            success: function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: 'Data perangkat telah berhasil dihapus.',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
        Swal.fire({
            icon: 'warning',
            title: 'Gagal!',
            text: xhr.responseJSON.message
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat menghapus data.'
        });
    }
                            }
                       
                       
                        });
                    }
                });
            });

            $('#btn-bulk-delete').on('click', function () {
                var selectedDeviceIds = $('.device-checkbox:checked').map(function () { return $(this).val(); }).get();
                if (selectedDeviceIds.length === 0) return;

                Swal.fire({
                    title: `Hapus ${selectedDeviceIds.length} Data Master?`,
                    html: "<p>Anda akan menghapus beberapa <b>Data Master</b> secara permanen.</p><p>Tindakan ini tidak dapat diurungkan dan dapat memengaruhi data lain. Lanjutkan?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('panel.device.bulkDestroy') }}',
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}', ids: selectedDeviceIds },
                            success: function () {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!', 
                                    text: 'Data yang dipilih telah berhasil dihapus.',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(() => window.location.reload());
                            },
                            error: function (xhr) {
                                if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
        Swal.fire({
            icon: 'warning',
            title: 'Gagal!',
            text: xhr.responseJSON.message
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat menghapus data.'
        });
    }
                            }
                        });
                    }
                });
            });
            
            function updateBulkDeleteButtonState() { $('#btn-bulk-delete').prop('disabled', $('.device-checkbox:checked').length === 0); }
            $('#selectAllCheckbox').on('click', function () { $('.device-checkbox').prop('checked', $(this).prop('checked')); updateBulkDeleteButtonState(); });
            $('.device-checkbox').on('click', function () { updateBulkDeleteButtonState(); $('#selectAllCheckbox').prop('checked', $('.device-checkbox:checked').length === $('.device-checkbox').length); });
        });
    </script>
    <style>
        .highlight-row { background-color: #eaf6ff; }
        .alert-warning { position: relative; }
        #btn-bulk-delete-all-duplicates { position: absolute; bottom: 10px; right: 10px; border-radius: 5px; }
    </style>
@endsection