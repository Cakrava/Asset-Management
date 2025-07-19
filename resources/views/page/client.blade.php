{{-- resources/views/page/client.blade.php --}}
@extends('layout.sidebar')
@section('content')

    <!-- [Page specific CSS] start -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->

    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item">Client</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="margin-bottom : -15px">
                        <h5>Data Client</h5>
                        <small class="text-muted">Data ini berisi daftar client yang terdaftar di sistem. Saat ini</small>
                        <small class="" style="color: rgb(8, 0, 255)">
                            {{ count($clients) }}
                            client </small><small class="text-muted">telah tercatat.</small>

                        <div class="mt-3">
                            @if (!session()->has('profile_incomplete'))
                                <button type="button" class="btn btn-primary" style="margin-bottom: -10px"
                                    data-bs-toggle="modal" data-bs-target="#clientModal" id="btn-new-client">
                                    <i class="ti ti-plus"></i> New Client
                                </button>
                                <button type="button" class="btn btn-danger" style="margin-bottom: -10px" id="btn-bulk-delete" disabled>
                                    <i class="ti ti-trash"></i> Bulk Delete
                                </button>
                            @endif

                            @if (session()->has('warning'))
                                <div class="alert alert-warning" style="margin-top: 20px; position: relative;">
                                    <p>⚠️ <strong>Beberapa Data Client Terlihat Serupa!</strong></p>
                                    {!! session('warning') !!}
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
                        <div class="dt-responsive">
                            <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="selectAllCheckbox">
                                            </div>
                                        </th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Institution</th>
                                        <th>Type</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $highlighted = false;
                                    @endphp
                                    @foreach($clients as $client)
                                        @php
                                            $isRecentUpdate = false;
                                            if (!$highlighted && $client->updated_at && $client->updated_at->diffInSeconds(\Carbon\Carbon::now()) <= 30) {
                                                $isRecentUpdate = true;
                                                $highlighted = true;
                                            }
                                        @endphp
                                        <tr class="{{ $isRecentUpdate ? 'highlight-row' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input client-checkbox" type="checkbox" value="{{ $client->id }}" id="clientCheckbox_{{ $client->id }}">
                                                </div>
                                            </td>
                                            <td>{{ $client->name }}</td>
                                            <td>{{ $client->phone }}</td>
                                            <td>{{ $client->institution }}</td>
                                            <td>{{ $institutionTypeNames[$client->institution_type] ?? ucfirst($client->institution_type) }}</td>
                                            <td>{{ Str::limit($client->address, 10) }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-success" style="border-radius: 5px;" data-reference="{{ $client->reference }}" onclick="openGoogleMapsRoute(this)">
                                                        <i class="ti ti-map"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary btn-update-client" style="border-radius: 5px;" data-bs-toggle="modal" data-bs-target="#clientModal" data-id="{{ $client->id }}">
                                                        <i class="ti ti-pencil"></i>
                                                    </button>
                                                    <form class="form-delete-client d-inline" action="{{ route('panel.client.destroy', $client->id) }}" method="POST" data-client-id="{{ $client->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-client" style="border-radius: 5px;">
                                                            <i class="ti ti-trash"></i>
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
    <div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientModalLabel">New Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- [PERUBAHAN 1] Menambahkan Div Alert yang Tersembunyi --}}
                    <div class="alert alert-warning d-none" role="alert" id="form-validation-alert">
                        <strong>Peringatan:</strong> Harap lengkapi semua data yang wajib diisi.
                    </div>
                    <form id="clientForm" action="{{ route('panel.client.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="client_id" id="client_id">

                        <div class="row" id="user-select-container">
                            <div class="col-12 mb-3">
                                <label for="user_id" class="form-label">User</label>
                                <select class="form-select form-select-sm" id="user_id" name="user_id" required>
                                    <option value="" selected disabled>Pilih User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-7 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control form-control-sm" id="name" name="name" required>
                            </div>
                            <div class="col-5 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control form-control-sm" id="phone" name="phone">
                            </div>
                        </div>
                        
                        <div class="row">
                             <div class="col-7 mb-3">
                                <label for="institution" class="form-label">Institution</label>
                                <input type="text" class="form-control form-control-sm" id="institution" name="institution" required>
                            </div>
                            <div class="col-5 mb-3">
                                <label for="institution_type" class="form-label">Type</label>
                                <select class="form-select form-select-sm" id="institution_type" name="institution_type" required>
                                    <option value="" selected disabled>Pilih</option>
                                    <option value="government">Pemerintahan</option>
                                    <option value="private">Swasta</option>
                                    <option value="non_profit">Nirlaba</option>
                                    <option value="education">Pendidikan</option>
                                    <option value="health">Kesehatan</option>
                                    <option value="finance">Keuangan</option>
                                    <option value="technology">Teknologi</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                        </div>
    
                        <hr class="my-2">
    
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control form-control-sm" id="address" name="address" rows="2" placeholder="Alamat akan terisi otomatis dari peta atau bisa diisi manual..."></textarea>
                        </div>
    
                        <div class="mb-3">
                            <label for="reference" class="form-label">Reference / Coordinates</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm" id="reference" name="reference" placeholder="Contoh: -4.9703, 105.0440">
                                <button class="btn btn-outline-secondary" type="button" id="openMapBtn" title="Pilih lokasi dari peta">
                                    <i class="ti ti-map-pin"></i> Buka Peta
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="refreshReferenceBtn" title="Refresh/Get Current Location">
                                    <i class="ti ti-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="saveClientBtn">Save Client</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openGoogleMapsRoute(button) {
            let reference = button.getAttribute('data-reference');
            if (reference) {
                let url = `https://www.google.com/maps/dir/?api=1&destination=${reference}`;
                window.open(url, '_blank');
            }
        }
    </script>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- [Page Specific JS] start -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

<script>
    $(document).ready(function () {
        const formInputs = $('#clientForm').find('input, select, textarea, button').not('[type="hidden"], #user_id');
        const alertDiv = $('#form-validation-alert');

        function toggleFormInputs(enable) {
            formInputs.prop('disabled', !enable);
        }

        var table = $('#dom-jqry').DataTable({
            "dom": '<"row justify-content-between"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6 text-end"p>>',
            "columnDefs": [{ "orderable": false, "targets": 0, "className": 'dt-body-center' }]
        });

        $('#openMapBtn').on('click', function () {
            window.open('https://www.google.com/maps?hl=id', '_blank');
        });

        $('#btn-new-client').on('click', function () {
            $('#clientModalLabel').text('New Client');
            $('#clientForm').attr('action', "{{ route('panel.client.store') }}").trigger('reset');
            $('#client_id').val('');
            $('#user_id').val('').prop('disabled', false).trigger('change');
            $('#user-select-container').show();
            toggleFormInputs(false);
            alertDiv.addClass('d-none'); 
        });

        $('#user_id').on('change', function() {
            toggleFormInputs(!!$(this).val());
        });

        $('.btn-update-client').on('click', function () {
            var clientId = $(this).data('id');
            $('#clientModalLabel').text('Update Client');
            $('#clientForm').attr('action', "{{ route('panel.client.update') }}");
            $('#client_id').val(clientId);
            
            $('#user-select-container').hide();
            $('#user_id').prop('disabled', true);
            toggleFormInputs(true);
            alertDiv.addClass('d-none');

            $.ajax({
                url: '/client/' + clientId,
                type: 'GET',
                success: function (response) {
                    $('#name').val(response.name);
                    $('#phone').val(response.phone);
                    $('#institution').val(response.institution);
                    $('#institution_type').val(response.institution_type);
                    $('#address').val(response.address);
                    $('#reference').val(response.reference);
                },
                error: function (xhr) { 
                    console.error('Error fetching client data:', xhr); 
                    Swal.fire('Gagal', 'Tidak dapat memuat data client.', 'error');
                }
            });
        });

        // [PERUBAHAN 2] Menambahkan logika validasi
        $('#saveClientBtn').on('click', function (e) {
            e.preventDefault();
            var form = $('#clientForm');
            var isUpdate = !!$('#client_id').val();
            var isValid = true;

            alertDiv.addClass('d-none');
            
            // Lakukan validasi pada input yang memiliki atribut 'required' dan terlihat
            form.find('input[required]:visible, select[required]:visible').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                alertDiv.removeClass('d-none');
                return;
            }

            // Jika valid, lanjutkan dengan AJAX
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    $('#clientModal').modal('hide');
                    Swal.fire({
                        title: 'Sukses!',
                        text: isUpdate ? 'Data client berhasil diperbarui.' : 'Client baru berhasil ditambahkan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function (xhr) {
                    console.error('Error saving data:', xhr);
                    var errorMessage = xhr.responseJSON?.message || 'Gagal menyimpan data. Periksa kembali isian Anda.';
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            });
        });

        $('.btn-delete-client').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Peringatan!',
                html: "<p>Anda akan menghapus sebuah Data Master.<br><br>Menghapus data ini dapat menyebabkan inkonsistensi pada data inventaris atau data lain yang terhubung. Apakah Anda benar-benar yakin?</p>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Data client telah berhasil dihapus.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
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

        function updateBulkDeleteButtonState() {
            var selectedCount = table.rows({ search: 'applied' }).nodes().to$().find('.client-checkbox:checked').length;
            $('#btn-bulk-delete').prop('disabled', selectedCount === 0);
        }

        $('#selectAllCheckbox').on('click', function() {
            var rows = table.rows({ search: 'applied' }).nodes();
            $('input.client-checkbox', rows).prop('checked', this.checked);
            updateBulkDeleteButtonState();
        });

        $('#dom-jqry tbody').on('change', '.client-checkbox', function() {
            updateBulkDeleteButtonState();
            var totalCheckboxes = table.rows({ search: 'applied' }).nodes().to$().find('.client-checkbox').length;
            var checkedCheckboxes = table.rows({ search: 'applied' }).nodes().to$().find('.client-checkbox:checked').length;
            $('#selectAllCheckbox').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        });

        table.on('draw', function() {
            updateBulkDeleteButtonState();
            $('#selectAllCheckbox').prop('checked', false);
        });

        $('#btn-bulk-delete').on('click', function() {
            var selectedClientIds = table.rows({ search: 'applied' }).nodes().to$().find('.client-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            var count = selectedClientIds.length;

            if (count > 0) {
                Swal.fire({
                    title: `Hapus ${count} Data Master?`,
                    html: "<p>Anda akan menghapus beberapa Data Master secara permanen.<br><br>Tindakan ini tidak dapat diurungkan dan dapat memengaruhi data lain. Lanjutkan?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Semua',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('panel.client.bulkDestroy') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: selectedClientIds
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: `${count} data client berhasil dihapus.`,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
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
            }
        });

        $('#refreshReferenceBtn').on('click', function () {
            getAddressFromGeocode();
        });

        async function getAddressFromGeocode() {
            const apiKey = "{{ env('API_GEOCODE') }}";
            const coordinatesInput = $('#reference').val();
            const addressTextarea = $('#address');

            if (!coordinatesInput) {
                addressTextarea.val("Koordinat kosong!");
                return;
            }
            
            const [lat, lon] = coordinatesInput.split(",").map(coord => coord.trim());

            if (!lat || !lon || isNaN(lat) || isNaN(lon)) {
                addressTextarea.val("Koordinat tidak valid!");
                return;
            }

            const url = `https://geocode.maps.co/reverse?lat=${lat}&lon=${lon}&api_key=${apiKey}`;

            try {
                const response = await fetch(url);
                const data = await response.json();
                addressTextarea.val(data.display_name || "Alamat tidak ditemukan.");
            } catch (error) {
                addressTextarea.val("Gagal mengambil data alamat.");
                console.error("Geocoding error:", error);
            }
        }
    });
</script>
    <style>
        .highlight-row {
            background-color: #00a2ff36;
            transition: background-color 0.5s ease-in-out;
        }
        .alert-warning {
            position: relative;
        }
        #btn-bulk-delete-all-duplicates {
            position: absolute;
            bottom: 10px;
            right: 10px;
            border-radius: 5px;
        }
    </style>
@endsection