@extends('layout.sidebar')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- [Page specific CSS] start -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .client-search-list .list-group-item { cursor: pointer; }
        .client-search-list .list-group-item:hover { background-color: #f8f9fa; }
        .action-input-group { display: flex; align-items: center; justify-content: flex-end; gap: 8px; }
        .action-input-group .form-control { width: 60px; text-align: center; }
        .condition-choice-text { cursor: pointer; font-weight: bold; padding: 5px; border-radius: 4px; }
        .condition-choice-text:hover { background-color: #e9ecef; text-decoration: underline; }
        .btn-add-deployed-to-cart:disabled {
            background-color: #6c757d; border-color: #6c757d; color: #fff; cursor: not-allowed; opacity: 0.65;
        }

        /* [MODIFIKASI] STYLING UNTUK TOMBOL FLOATING REVOKED */
        .float-revoked-btn {
            position: fixed;
            width: auto; /* Lebar otomatis */
            padding: 10px 20px;
            height: 48px;
            bottom: 40px;
            right: 40px;
            background-color: #dc3545; /* Merah */
            color: #fff;
            border-radius: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.25);
            z-index: 1040;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .float-revoked-btn:hover {
            background-color: #bb2d3b;
            color: #fff;
        }
    </style>
    <!-- [Page specific CSS] end -->

    <div class="pc-container">
        <div class="pc-content">
            {{-- Header dan Breadcrumb --}}
            <div class="page-header">
                <div class="page-block"><div class="row align-items-center"><div class="col-md-12"><ul class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item">Riwayat Alur Aset</li></ul></div></div></div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="margin-bottom : -15px">
                        <h5>Data Alur Aset</h5>
                        <small class="text-muted">Total transaksi aktif yang tercatat sebanyak</small><small style="color: blue"> {{ $transactions->count() }} kali</small>
                        <div class="mt-3">
                           

                            @auth
                            @if (Auth::user()->role === 'admin')
                            <button type="button" class="btn btn-primary" style="margin-bottom: -10px" data-bs-toggle="modal" data-bs-target="#newFlowModal">
                                <i class="ti ti-plus"></i> New Flow
                            </button>
                            @endif
                        @endauth
                        </div>
                        @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" style="margin-top: 20px; margin-bottom : -20px">
                            {{ session('success') }}
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        @if($letters && $letters->count() > 0)
                        <div class="alert alert-info alert-dismissible fade show" style="margin-top: 20px; margin-bottom: -20px">
                            <p>Anda memiliki surat dalam status <strong>Needed</strong> sebanyak <strong>{{ $letters->count() }}</strong> surat</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                    </div>

                    <div class="card-body">
                        {{-- Filter Tabel Utama --}}
                        <div class="filter-navigation filter-v1 mb-4">
                            <button type="button" class="filter-btn active" data-filter="">All</button>
                            <button type="button" class="filter-btn" data-filter="In">In</button>
                            <button type="button" class="filter-btn" data-filter="Out">Out</button>
                        </div>
                        <style>.filter-v1 .filter-btn{background:0 0;border:none;padding:8px 15px;color:#495057;cursor:pointer;position:relative}.filter-v1 .filter-btn:hover{color:#0ea2bc}.filter-v1 .filter-btn.active{color:#0ea2bc;font-weight:700}.filter-v1 .filter-btn.active::after{content:'';position:absolute;bottom:-2px;left:8px;right:8px;height:2px;background-color:#0ea2bc}</style>
                        
                        {{-- Tabel Utama (Tanpa Revoked) --}}
                        <div class="dt-responsive">
                            <table id="transaction-table" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Nama</th>
                                        <th>Instansi</th>
                                        <th>Activity</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_id}}</td>
                                            <td>{{ $transaction->client?->profile?->name ?? $transaction->otherSourceProfile?->name ?? 'Internal' }}</td>
                                            <td>{{ $transaction->client?->profile?->institution ?? $transaction->otherSourceProfile?->institution ?? '-' }}</td>
                                            <td>
                                                @if($transaction->transaction_type == 'in')
                                                    <span style="color: green; font-weight: 500;">In <i class="ti ti-arrow-down"></i></span>
                                                @else
                                                    <span style="color: red; font-weight: 500;">Out <i class="ti ti-arrow-up"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $statusText = ucwords($transaction->instalation_status); @endphp
                                                @if($transaction->instalation_status == 'Intake')
                                                    <span style="display: inline-block; min-width: 95px; font-size: 13px; padding: 4px 9px; text-align: center; border-radius: 5px; font-weight: 500; background-color: #ffffff; color: #0d6efd; border: 1.5px solid #0d6efd;">{{ $statusText }}</span>
                                                @elseif($transaction->instalation_status == 'Pending')
                                                    <span style="display: inline-block; min-width: 95px; font-size: 13px; padding: 4px 9px; text-align: center; border-radius: 5px; font-weight: 500; background-color: #ffffff; color: #ffc107; border: 1.5px solid #ffc107;">{{ $statusText }}</span>
                                                @elseif($transaction->instalation_status == 'Deployed')
                                                    <span style="display: inline-block; min-width: 95px; font-size: 13px; padding: 4px 9px; text-align: center; border-radius: 5px; font-weight: 500; background-color: #ffffff; color: #198754; border: 1.5px solid #198754;">{{ $statusText }}</span>
                                                @else
                                                    <span style="display: inline-block; min-width: 95px; font-size: 13px; padding: 4px 9px; text-align: center; border-radius: 5px; font-weight: 500; background-color: #ffffff; color: #6c757d; border: 1.5px solid #6c757d;">{{ $statusText ?? 'N/A' }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info btn-details" data-bs-toggle="modal" data-bs-target="#transactionDetailModal"
                                                    data-transaction-id="#{{ $transaction->transaction_id ?? $transaction->id }}"
                                                    data-client_name="{{ $transaction->client?->profile?->name ?? $transaction->otherSourceProfile?->name ?? 'N/A' }}"
                                                    data-client_phone="{{ $transaction->client?->profile?->phone ?? $transaction->otherSourceProfile?->phone ?? 'N/A' }}"
                                                    data-institution="{{ $transaction->client?->profile?->institution ?? $transaction->otherSourceProfile?->institution ?? 'N/A' }}"
                                                    data-address="{{ $transaction->client?->profile?->address ?? $transaction->otherSourceProfile?->address ?? 'N/A' }}"
                                                    data-status="{{ $transaction->instalation_status ?? 'Pending' }}"
                                                    data-date="{{ $transaction->created_at->format('d F Y, H:i') }}"
                                                    @auth
                                                    @if (Auth::user()->role === 'admin' && $transaction->instalation_status === 'Pending' && isset($tokenLinks[$transaction->transaction_id]['url']))
                                                        data-url="{{ $tokenLinks[$transaction->transaction_id]['url'] }}"
                                                    @endif
                                                @endauth
                                                
                                                    data-details='{!! json_encode($transaction->details->map(function($d) { return ['device' => ($d->storedDevice?->device?->brand ?? 'N/A') . ' ' . ($d->storedDevice?->device?->model ?? ''), 'quantity' => $d->quantity, 'condition' => $d->storedDevice?->condition ?? 'N/A']; })) !!}'>
                                                    <i class="ti ti-eye"></i>
                                                </button>
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

    {{-- [MODIFIKASI] TOMBOL FLOATING UNTUK REVOKED --}}
    @if($revokedTransactions->count() > 0)
        <div class="float-revoked-btn" data-bs-toggle="offcanvas" data-bs-target="#revokedSidebar" aria-controls="revokedSidebar">
            <i class="ti ti-trash-x"></i>
            <span>Revoked</span>
            <span class="badge bg-light text-danger rounded-pill">{{ $revokedTransactions->count() }}</span>
        </div>
    @endif

    {{-- [MODIFIKASI] SIDEBAR (OFFCANVAS) UNTUK TRANSAKSI REVOKED --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="revokedSidebar" aria-labelledby="revokedSidebarLabel" style="width: 60%;">
        <div class="offcanvas-header">
            <h5 id="revokedSidebarLabel"><i class="ti ti-trash-x me-2 text-danger"></i>Riwayat Transaksi Dibatalkan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="dt-responsive">
                <table id="revoked-transaction-table" class="table table-striped table-bordered nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Tanggal Dibatalkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revokedTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_id }}</td>
                                <td>{{ $transaction->client?->profile?->name ?? $transaction->otherSourceProfile?->name ?? 'Internal' }}</td>
                                <td>{{ $transaction->client?->profile?->institution ?? $transaction->otherSourceProfile?->institution ?? '-' }}</td>
                                <td>{{ $transaction->updated_at->format('d M Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info btn-details" data-bs-toggle="modal" data-bs-target="#transactionDetailModal"
                                        data-transaction-id="#{{ $transaction->transaction_id ?? $transaction->id }}"
                                        data-client_name="{{ $transaction->client?->profile?->name ?? $transaction->otherSourceProfile?->name ?? 'N/A' }}"
                                        data-client_phone="{{ $transaction->client?->profile?->phone ?? $transaction->otherSourceProfile?->phone ?? 'N/A' }}"
                                        data-institution="{{ $transaction->client?->profile?->institution ?? $transaction->otherSourceProfile?->institution ?? 'N/A' }}"
                                        data-address="{{ $transaction->client?->profile?->address ?? $transaction->otherSourceProfile?->address ?? 'N/A' }}"
                                        data-status="Revoked" {{-- Set status manual --}}
                                        data-date="{{ $transaction->created_at->format('d F Y, H:i') }}"
                                        data-url="" {{-- URL tidak relevan untuk revoked --}}
                                        data-details='{!! json_encode($transaction->details->map(function($d) { return ['device' => ($d->storedDevice?->device?->brand ?? 'N/A') . ' ' . ($d->storedDevice?->device?->model ?? ''), 'quantity' => $d->quantity, 'condition' => $d->storedDevice?->condition ?? 'N/A']; })) !!}'>
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Transaction Detail Modal --}}
    <div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionDetailModalLabel">Detail Transaksi: <span id="modal-transaction-id" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-url-section" class="mt-3 d-none">
                        
                        <h6 class="mb-1">URL Penyelesaian</h6>
                        <div class="input-group mb-2">
                            <input type="text" id="modal-url" class="form-control" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('#modal-url')">Salin</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Klien</h6>
                            <hr class="mt-0 mb-2">
                            <p class="mb-1"><strong>Nama:</strong> <span id="modal-client-name"></span></p>
                            <p class="mb-1"><strong>Telepon:</strong> <span id="modal-client-phone"></span></p>
                            <p class="mb-1"><strong>Instansi:</strong> <span id="modal-institution"></span></p>
                            <p class="mb-1"><strong>Alamat:</strong> <span id="modal-address"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Transaksi</h6>
                            <hr class="mt-0 mb-2">
                            <p class="mb-1"><strong>Status:</strong> <span id="modal-status"></span></p>
                            <p class="mb-1"><strong>Tanggal Transaksi:</strong> <span id="modal-date"></span></p>
                        </div>
                    </div>
                    <h6 class="mt-3">Detail Perangkat</h6>
                    <hr class="mt-0 mb-2">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Perangkat</th>
                                <th>Kondisi</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="modal-device-details"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- New Flow Modal --}}
    <div class="modal fade" id="newFlowModal" tabindex="-1" aria-labelledby="newFlowModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newFlowModalLabel">New Transaction Flow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="newFlowTab" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-tab-pane" type="button" role="tab">Input Manual</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="letter-tab" data-bs-toggle="tab" data-bs-target="#letter-tab-pane" type="button" role="tab">Ambil dari Surat</button></li>
                    </ul>
                    <div class="tab-content" id="newFlowTabContent">
                        {{-- Manual Input Tab Pane --}}
                        <div class="tab-pane fade show active" id="manual-tab-pane" role="tabpanel">
                            <form id="manual-transaction-form">
                                <input type="hidden" id="transaction_source" name="transaction_source" value="0">
                                <input type="hidden" id="is_other_source_client" name="is_other_source_client" value="0">
                                <div class="row">
                                    <div class="col-md-5 border-end">
                                        <div class="alert alert-warning" id="warning-for-klien" style="display: none" >Lengkapi data klien!</div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Informasi Klien</h6>
                                            <div class="form-check form-switch" id="from-other-source-container">
                                                <input class="form-check-input" type="checkbox" id="from-other-source-check">
                                                <label class="form-check-label" for="from-other-source-check" style="font-size: 0.8rem;">Sumber Lain</label>
                                            </div>
                                        </div>
                                        <div id="client-details-view">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="manual-client-name" class="form-label mb-0">Nama Klien</label>
                                                <button type="button" class="btn btn-sm btn-light" id="btn-open-client-search"><i class="ti ti-search"></i></button>
                                            </div>
                                            <div class="position-relative">
                                                <input type="text" id="manual-client-name" class="form-control mb-1">
                                                <div id="other-source-suggestions-list" class="list-group position-absolute w-100" style="z-index: 1060; display: none; max-height: 200px; overflow-y: auto;"></div>
                                            </div>
                                            <label for="manual-client-phone" class="form-label">Telepon</label>
                                            <input type="text" id="manual-client-phone" class="form-control bg-light mb-3" readonly>
                                            <div class="mb-3">
                                                <label for="manual-client-institution-type" class="form-label">Tipe Instansi</label>
                                                <select id="manual-client-institution-type" class="form-select bg-light" disabled>
                                                    <option value="" selected>Pilih Tipe</option>
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
                                            <label for="manual-client-institution" class="form-label">Nama Instansi</label>
                                            <input type="text" id="manual-client-institution" class="form-control bg-light" readonly>
                                        </div>
                                        <div id="client-search-view" style="display: none;">
                                            <div class="input-group mb-3">
                                                <input type="text" id="client-search-input" class="form-control" placeholder="Ketik nama klien atau instansi...">
                                                <button class="btn btn-outline-secondary" type="button" id="btn-close-client-search"><i class="ti ti-x"></i></button>
                                            </div>
                                            <div id="client-search-list" class="list-group client-search-list" style="max-height: 250px; overflow-y: auto;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <h6 class="mb-3">Detail Transaksi & Perangkat</h6>
                                        <div class="alert alert-warning" id="warning-for-surat" style="display: none;">Transaksi Flow Out sangat disarankan melalui tahapan pembuatan Surat Serah Terima</div>
                                        <div class="row mb-3">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <label for="transaction_id" class="form-label">Transaction ID</label>
                                                <input type="text" class="form-control bg-light" id="transaction_id" readonly>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="flow-type" class="form-label">Flow Transaction</label>
                                                <select class="form-select" id="flow-type">
                                                    <option value="in">In (Aset Masuk)</option>
                                                    <option value="out">Out (Aset Keluar)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="device-select" class="form-label">Perangkat</label>
                                            <select id="device-select" class="form-control" style="width: 100%;"></select>
                                        </div>
                                        <div class="row g-2 align-items-end">
                                            <div class="col-sm-6">
                                                <label for="device-condition" class="form-label">Kondisi</label>
                                                <input type="text" id="device-condition" class="form-control bg-light" readonly>
                                                <select id="device-condition-select" class="form-select" style="display: none;">
                                                    <option value="Baru">Baru</option>
                                                    <option value="Bekas">Bekas</option>
                                                    <option value="Rusak">Rusak</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="device-quantity" class="form-label">Jumlah</label>
                                                <input type="number" id="device-quantity" class="form-control" min="1" value="1">
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-primary w-100" id="btn-add-to-cart" title="Tambah ke keranjang"><i class="ti ti-plus"></i></button>
                                            </div>
                                        </div>
                                        <div id="deployed-devices-container" class="mt-4" style="display: none;">
                                            <hr>
                                            <h6 class="mb-2"><i class="ti ti-history me-2"></i>Deployed Units</h6>
                                            <p style="font-size: small;color : green">Perangkat Teralokasi</p>
                                            <div class="table-responsive" style="max-height: 150px; overflow-y: auto;">
                                                <table class="table table-sm table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><small>Perangkat</small></th>
                                                            <th class="text-center"><small>Kondisi</small></th>
                                                            <th class="text-center"><small>Estimasi Sisa</small></th>
                                                            <th class="text-end"><small>Aksi</small></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="deployed-devices-tbody"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="letter-tab-pane" role="tabpanel">
                            <div id="letter-client-detail-container" class="card mb-3" style="display: none;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <img id="letter-client-image" src="" class="rounded-circle me-3" alt="Client" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h5 class="card-title mb-0" id="letter-client-name"></h5>
                                            <p class="card-text mb-0" id="letter-client-institution"></p>
                                            <small class="text-muted" id="letter-client-phone"></small>
                                        </div>
                                    </div>
                                    <hr>
                                    <p class="card-text mb-0"><small id="letter-client-address"></small></p>
                                    <div class="text-end mt-2"><a href="#" id="btn-change-letter">Ubah Surat</a></div>
                                </div>
                            </div>
                            <div id="letter-selection-container">
                                <div class="list-group" style="max-height: 350px; overflow-y: auto;">
                                    @forelse ($letters as $letter)
                                        <a href="#" class="list-group-item list-group-item-action letter-item"
                                            data-letter-id="{{ $letter->id }}"
                                            data-client-id="{{ $letter->client->id }}"
                                            data-details='{!! json_encode($letter->details->map(function($d) { return ['id' => $d->storedDevice->id, 'name' => ($d->storedDevice?->device?->brand ?? 'N/A') . ' ' . ($d->storedDevice?->device?->model ?? ''), 'condition' => $d->storedDevice?->condition ?? 'N/A', 'quantity' => $d->quantity, 'source' => 'letter']; })) !!}'
                                            data-client-profile='{!! json_encode($letter->client->profile) !!}'>
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $letter->client?->profile?->institution}}</h6>
                                                <small>{{ $letter->created_at->format('d M Y') }}</small>
                                            </div>
                                            <p class="mb-1 small">Nomor :{{ $letter->letter_number }}</p>
                                            <p class="mb-1 small">Tujuan: {{ $letter->client?->profile?->name ?? 'N/A' }}</p>
                                            <small class="text-muted">Jumlah Item: {{ $letter->details->count() }}</small>
                                        </a>
                                    @empty
                                        <div class="list-group-item text-center text-muted">Tidak ada surat perintah yang tersedia.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cart-container" style="display: none;">
                        <hr>
                        <h6 class="mb-3"><i class="ti ti-shopping-cart me-2"></i>Keranjang Transaksi</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Perangkat</th>
                                        <th>Kondisi</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-process-transaction" disabled>Proses Transaksi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- [Page Specific JS] start -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        const ASSET_URL = "{{ asset('') }}";
        const allMasterDevices = @json($devices);
        const allStoredDevices = @json($storedDevices);
        const allUsers = @json($users);
        const CSRF_TOKEN = "{{ csrf_token() }}";

        function copyToClipboard(selector) {
        // 1. Dapatkan semua elemen yang dibutuhkan dari modal
        const urlInput = document.querySelector(selector);
        const transactionIdSpan = document.getElementById('modal-transaction-id');
        const clientNameSpan = document.getElementById('modal-client-name');
        const institutionSpan = document.getElementById('modal-institution');
        const dateSpan = document.getElementById('modal-date');
        const deviceDetailsTbody = document.getElementById('modal-device-details');

        // 2. Validasi dan ambil data teks dari setiap elemen
        if (!urlInput || !transactionIdSpan || !clientNameSpan || !institutionSpan || !dateSpan || !deviceDetailsTbody) {
            toastr.error('Gagal memuat detail untuk disalin.');
            return;
        }

        const url = urlInput.value;
        if (!url) {
            toastr.info('Tidak ada URL untuk dibagikan.');
            return;
        }

        const transactionId = transactionIdSpan.innerText.trim();
        const clientName = clientNameSpan.innerText.trim();
        const institution = institutionSpan.innerText.trim();
        const date = dateSpan.innerText.trim();

        // 3. Ambil detail perangkat dari tabel dan format menjadi daftar
        let deviceList = [];
        const deviceRows = deviceDetailsTbody.querySelectorAll('tr');
        deviceRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            // Pastikan itu adalah baris data, bukan pesan "tidak ada data"
            if (cells.length === 3) {
                const deviceName = cells[0].innerText.trim();
                const condition = cells[1].innerText.trim();
                const quantity = cells[2].innerText.trim();
                deviceList.push(`- ${quantity}x ${deviceName} (${condition})`);
            }
        });
        // Gabungkan daftar menjadi satu string, atau tampilkan pesan jika kosong
        const deviceText = deviceList.length > 0 ? deviceList.join('\n') : 'Tidak ada detail perangkat.';

        // 4. Buat format pesan yang rapi menggunakan template literal
        const messageToCopy = `
✨ *Ringkasan Transaksi* ✨
---------------------------------
*ID Transaksi*: ${transactionId}
*Tanggal*: ${date}

*Klien*:
- *Nama*: ${clientName}
- *Instansi*: ${institution}

*Detail Perangkat*:
${deviceText}
---------------------------------

Silakan lanjutkan proses penyelesaian berdasarkan Nomor Surat melalui tautan berikut:
👉 ${url}
        `.trim(); // .trim() untuk menghapus spasi kosong di awal dan akhir

        // 5. Salin pesan ke clipboard dan berikan feedback
        navigator.clipboard.writeText(messageToCopy).then(() => {
            toastr.success('Detail transaksi & tautan berhasil disalin!');
            
            // Beri feedback visual pada tombol
            const copyButton = document.querySelector(`button[onclick="copyToClipboard('${selector}')"]`);
            if (copyButton) {
                const originalHtml = copyButton.innerHTML;
                copyButton.innerHTML = `<i class="ti ti-check me-2"></i> Berhasil Disalin`;
                copyButton.classList.add('btn-success');
                copyButton.classList.remove('btn-primary');
                
                setTimeout(() => {
                    copyButton.innerHTML = originalHtml;
                    copyButton.classList.add('btn-primary');
                    copyButton.classList.remove('btn-success');
                }, 3000);
            }
        }).catch(err => {
            console.error('Gagal menyalin ke clipboard: ', err);
            toastr.error('Gagal menyalin pesan. Coba lagi.');
        });
    }
    // =======================================================================
    // AKHIR DARI PERUBAHAN
    // =======================================================================



        $(document).ready(function () {
            // Inisialisasi Tabel Utama
            var transactionTable = $('#transaction-table').DataTable({"dom": '<"row justify-content-between"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6 text-end"p>>'});

            // Inisialisasi Tabel Revoked di dalam Sidebar
            var revokedTransactionTable;
            $('#revokedSidebar').on('shown.bs.offcanvas', function () {
                if (!$.fn.DataTable.isDataTable('#revoked-transaction-table')) {
                    revokedTransactionTable = $('#revoked-transaction-table').DataTable({
                        "responsive": true,
                        "dom": '<"row justify-content-between"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6 text-end"p>>'
                    });
                }
            });

            let transactionCart = [], currentFlowType = 'in', activeTab = 'manual', selectedClient = null, selectedLetter = null;
            const deviceSelect = $('#device-select').select2({ dropdownParent: $('#newFlowModal'), placeholder: 'Cari perangkat...' });

            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                var filterValue = $(this).data('filter');
                transactionTable.column(3).search(filterValue).draw();
            });
            
            $('#transactionDetailModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); var modal = $(this);
                modal.find('#modal-transaction-id').text(button.data('transaction-id')); 
                modal.find('#modal-client-name').text(button.data('client_name')); 
                modal.find('#modal-client-phone').text(button.data('client_phone')); 
                modal.find('#modal-institution').text(button.data('institution')); 
                modal.find('#modal-address').text(button.data('address')); 
                modal.find('#modal-date').text(button.data('date'));
                
                const url = button.data('url');
                const urlInput = document.getElementById('modal-url');
                const urlSection = document.getElementById('modal-url-section');
                if (button.data('status') === 'Pending' && url) {
                    urlInput.value = url;
                    urlSection.classList.remove('d-none');
                } else {
                    urlInput.value = '';
                    urlSection.classList.add('d-none');
                }

                var status = button.data('status');
                if (status) { 
                    var statusBadge = '';
                    if (status === 'Deployed') { statusBadge = '<span class="badge bg-success">Deployed</span>'; }
                    else if (status === 'Intake') { statusBadge = '<span class="badge bg-primary">Intake</span>'; }
                    else if (status === 'Pending') { statusBadge = '<span class="badge bg-warning text-dark">Pending</span>'; }
                    else if (status === 'Revoked') { statusBadge = '<span class="badge bg-danger">Revoked</span>'; }
                    else { statusBadge = `<span class="badge bg-primary">${status}</span>`; }
                    modal.find('#modal-status').parent().show(); 
                    modal.find('#modal-status').html(statusBadge); 
                } else { 
                    modal.find('#modal-status').parent().hide(); 
                }

                var detailsContainer = modal.find('#modal-device-details'); detailsContainer.empty();
                var detailsData; try { detailsData = JSON.parse(button.attr('data-details')); } catch (e) { console.error("Gagal parsing JSON:", e); detailsData = []; }
                if (Array.isArray(detailsData) && detailsData.length > 0) { 
                    detailsData.forEach(function(item) { 
                        var row = `<tr><td>${item.device || 'N/A'}</td><td><span class="badge bg-light-secondary">${item.condition || 'N/A'}</span></td><td class="text-end">${item.quantity || '0'}</td></tr>`; 
                        detailsContainer.append(row); 
                    }); 
                } else { 
                    var noDataRow = '<tr><td colspan="3" class="text-center text-muted">Tidak ada detail perangkat.</td></tr>'; 
                    detailsContainer.append(noDataRow); 
                }
            });

            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            const searchOtherSource = debounce(function(query) {
                const suggestionList = $('#other-source-suggestions-list');
                if (query.length < 2) { suggestionList.empty().hide(); return; }
                $.ajax({
                    url: '{{ route('api.otherSource.search') }}', type: 'GET', data: { query: query },
                    success: function(profiles) {
                        suggestionList.empty().hide();
                        if (profiles.length > 0) {
                            profiles.forEach(function(profile) {
                                const item = $(`<a href="#" class="list-group-item list-group-item-action suggestion-item"></a>`);
                                item.html(`<div class="fw-bold">${profile.name}</div><small class="text-muted">${profile.institution || 'Tanpa Instansi'}</small>`);
                                item.data('profile', profile); 
                                suggestionList.append(item);
                            });
                            suggestionList.show();
                        }
                    }
                });
            }, 300);

            $('#manual-client-name').on('keyup', function() { if ($('#from-other-source-check').is(':checked')) { searchOtherSource($(this).val()); } });
            $('#other-source-suggestions-list').on('click', '.suggestion-item', function(e) {
                e.preventDefault();
                const profile = $(this).data('profile');
                $('#manual-client-name').val(profile.name);
                $('#manual-client-phone').val(profile.phone);
                $('#manual-client-institution').val(profile.institution);
                $('#manual-client-institution-type').val(profile.institution_type).trigger('change');
                $('#other-source-suggestions-list').empty().hide();
            });

            $(document).on('click', function(e) { if (!$(e.target).closest('#manual-client-name, #other-source-suggestions-list').length) { $('#other-source-suggestions-list').empty().hide(); } });

            function updateInputLockStates() {
                const hasDeployedItems = transactionCart.some(item => item.source === 'deployed');
                const hasManualItems = transactionCart.some(item => item.source === 'manual');
                const manualInputs = $('#device-select, #device-condition-select, #device-condition, #device-quantity, #btn-add-to-cart');
                manualInputs.prop('disabled', hasDeployedItems);
                if (hasDeployedItems) { $('#device-select').next('.select2-container').addClass('select2-container--disabled'); } else { $('#device-select').next('.select2-container').removeClass('select2-container--disabled'); }
                $('#deployed-devices-tbody .btn-add-deployed-to-cart').prop('disabled', hasManualItems);
            }

            function updateDeployedDeviceRowsUI() {
                $('#deployed-devices-tbody tr').each(function() {
                    const row = $(this);
                    const storedDeviceId = row.data('stored-device-id');
                    const button = row.find('.btn-add-deployed-to-cart');
                    const originalStock = parseInt(row.data('original-stock'));
                    const estimationBadge = row.find('.estimation-badge');
                    if (button.length > 0) {
                        let inCartFromThisSource = 0;
                        transactionCart.forEach(item => { if (item.source === 'deployed' && item.id === storedDeviceId) { inCartFromThisSource += item.quantity; } });
                        const remainingStock = originalStock - inCartFromThisSource;
                        estimationBadge.text(remainingStock);
                        button.prop('disabled', remainingStock <= 0);
                    }
                });
            }

            function renderCart() {
                const cartTbody = $('#cart-items-tbody');
                cartTbody.empty();
                updateDeployedDeviceRowsUI();
                if (transactionCart.length === 0) {
                    $('#cart-container').hide();
                    $('#btn-process-transaction').prop('disabled', true);
                    if (activeTab === 'letter') { $('#letter-client-detail-container').hide(); $('#letter-selection-container').show(); }
                } else {
                    $('#cart-container').show();
                    $('#btn-process-transaction').prop('disabled', false);
                    const aggregatedCart = {};
                    transactionCart.forEach((item, index) => {
                        if (item.source === 'letter') { aggregatedCart[`letter-${index}`] = { ...item, originalIndices: [index] }; return; }
                        const key = `${item.name}-${item.condition}`;
                        if (!aggregatedCart[key]) { aggregatedCart[key] = { name: item.name, condition: item.condition, quantity: 0, source: item.source, originalIndices: [] }; }
                        aggregatedCart[key].quantity += item.quantity;
                        aggregatedCart[key].originalIndices.push(index);
                    });
                    for (const key in aggregatedCart) {
                        const item = aggregatedCart[key];
                        const deleteButton = (item.source !== 'letter') ? `<button type="button" class="btn btn-sm btn-danger btn-delete-cart-group" data-indices='${JSON.stringify(item.originalIndices)}' title="Hapus"><i class="ti ti-x"></i></button>` : '';
                        const row = `<tr><td>${item.name}</td><td><span class="badge bg-light-secondary">${item.condition}</span></td><td class="text-end">${item.quantity}</td><td class="text-center">${deleteButton}</td></tr>`;
                        cartTbody.append(row);
                    }
                }
                updateInputLockStates();
            }

            function showClientDetailsView() { $('#client-search-view').hide(); $('#client-details-view').show(); }
            function showClientSearchView() { $('#client-details-view').hide(); $('#client-search-view').show(); $('#client-search-input').val('').focus(); filterClientList(''); }
            function clearClientAndDeviceData() { 
                transactionCart = []; renderCart(); $('#deployed-devices-tbody').empty(); $('#deployed-devices-container').hide(); 
                $('#manual-client-name, #manual-client-phone, #manual-client-institution').val(''); 
                $('#manual-client-institution-type').val('').trigger('change'); 
                selectedClient = null; selectedLetter = null; showClientDetailsView(); 
                $('#manual-client-name, #manual-client-phone, #manual-client-institution, #manual-client-institution-type').addClass('bg-light'); 
            }
            function resetModal() { clearClientAndDeviceData(); activeTab = 'manual'; $('#newFlowModal .nav-tabs button[data-bs-target="#manual-tab-pane"]').tab('show'); $('#from-other-source-check').prop('checked', false).trigger('change'); $('#flow-type').val('in').trigger('change'); generateTransactionId(); } 
            function generateTransactionId() {
                const d = new Date();
                const ts = `${d.getFullYear()}${String(d.getMonth()+1).padStart(2,'0')}${String(d.getDate()).padStart(2,'0')}-${d.getHours()}${d.getMinutes()}${d.getSeconds()}${String(d.getMilliseconds()).padStart(3,'0')}`;
                const rc = Math.random().toString(36).substring(2,6).toUpperCase();
                $('#transaction_id').val(`TRX-${ts}-${rc}`);
            }
            function updateDeviceOptions(flow) { 
                deviceSelect.empty(); let options = '<option></option>'; 
                if (flow === 'in') { allMasterDevices.forEach(d => { options += `<option value="${d.id}" data-name="${d.brand} - ${d.model}">${d.brand} - ${d.model}</option>`; }); } 
                else { allStoredDevices.forEach(s => { options += `<option value="${s.id}" data-name="${s.device.brand} - ${s.device.model}" data-condition="${s.condition}">${s.device.brand} - ${s.device.model} (Stok: ${s.stock}, Kondisi: ${s.condition})</option>`; }); } 
                deviceSelect.html(options).trigger('change'); 
            }
            function populateClientSearchList() { const listContainer = $('#client-search-list'); listContainer.empty(); allUsers.forEach(user => { if(user.profile) { const item = $(`<a href="#" class="list-group-item list-group-item-action client-list-item"><div class="fw-bold">${user.profile.name}</div><small class="text-muted">${user.profile.institution || 'Tanpa Instansi'}</small></a>`); const userData = { id: user.id, name: user.profile.name, phone: user.profile.phone, institution: user.profile.institution, institution_type: user.profile.institution_type, address: user.profile.address, image: user.profile.image }; item.data('user', userData); listContainer.append(item); } }); }
            function filterClientList(query) { const lowerCaseQuery = query.toLowerCase(); $('.client-list-item').each(function() { const itemText = $(this).text().toLowerCase(); $(this).toggle(itemText.includes(lowerCaseQuery)); }); }
            function fetchAndRenderDeployedDevices(userId) {
                if (!userId) { $('#deployed-devices-container').hide(); return; }
                $('#deployed-devices-container').show(); $('#deployed-devices-tbody').html('<tr><td colspan="4" class="text-center text-muted"><i>Mencari data...</i></td></tr>');
                $.ajax({
                    url: `{{ url('/api/get-deployed-devices') }}/${userId}`, type: 'GET',
                    success: function(response) {
                        const tbody = $('#deployed-devices-tbody'); tbody.empty();
                        if (response.devices && response.devices.length > 0) {
                            response.devices.forEach(item => {
                                const sourceId = `${item.stored_device_id}-${item.condition.replace(/\s+/g, '-')}`; 
                                const originalButtonHTML = `<button type="button" class="btn btn-sm btn-success btn-add-deployed-to-cart" title="Tambah"><i class="ti ti-stack"></i></button>`;
                                const actionCellHTML = currentFlowType === 'in' ? `<div class="action-cell" data-original-html='${originalButtonHTML}'>${originalButtonHTML}</div>` : ''; 
                                const row = $(`<tr data-source-id="${sourceId}" data-original-stock="${item.quantity}" data-device-name="${item.name}" data-stored-device-id="${item.stored_device_id}" data-condition="${item.condition}"><td><small>${item.name}</small></td><td class="text-center"><small><span class="badge bg-secondary">${item.condition}</span></small></td><td class="text-center"><small class="badge bg-light-info estimation-badge">${item.quantity}</small></td><td class="text-end">${actionCellHTML}</td></tr>`);
                                tbody.append(row);
                            });
                        } else { tbody.html('<tr><td colspan="4" class="text-center text-muted"><small>Tidak ada riwayat perangkat terpasang.</small></td></tr>'); }
                        renderCart();
                    },
                    error: function() { $('#deployed-devices-tbody').html('<tr><td colspan="4" class="text-center text-danger"><small>Gagal memuat data.</small></td></tr>');}
                });
            }
            
            $('#newFlowModal').on('show.bs.modal', resetModal);
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) { activeTab = $(e.target).data('bs-target') === '#manual-tab-pane' ? 'manual' : 'letter'; clearClientAndDeviceData(); generateTransactionId(); });
            populateClientSearchList();
            deviceSelect.on('select2:select', function (e) { if (currentFlowType === 'out') { $('#device-condition').val($(e.params.data.element).data('condition')); } });
            $('#flow-type').on('change', function() { clearClientAndDeviceData(); generateTransactionId(); currentFlowType = $(this).val(); updateDeviceOptions(currentFlowType); $('#warning-for-surat').toggle(currentFlowType === 'out'); $('#device-condition').toggle(currentFlowType !== 'in'); $('#device-condition-select').toggle(currentFlowType === 'in'); deviceSelect.val(null).trigger('change'); if(selectedClient && !$('#from-other-source-check').is(':checked')) { fetchAndRenderDeployedDevices(selectedClient.id); } else { $('#deployed-devices-container').hide(); } });
            $('#from-other-source-check').on('change', function() { const isOtherSource = $(this).is(':checked'); $('#is_other_source_client').val(isOtherSource ? '1' : '0'); clearClientAndDeviceData(); $('#btn-open-client-search').prop('disabled', isOtherSource); $('#manual-client-name, #manual-client-phone, #manual-client-institution, #manual-client-institution-type').prop('readonly', !isOtherSource).prop('disabled', !isOtherSource).toggleClass('bg-light', !isOtherSource); $('#deployed-devices-container').hide(); });
            $('#btn-open-client-search').on('click', showClientSearchView);
            $('#btn-close-client-search').on('click', showClientDetailsView);
            $('#client-search-input').on('keyup', function() { filterClientList($(this).val()); });
            $('#client-search-list').on('click', '.client-list-item', function(e) { e.preventDefault(); const userData = $(this).data('user'); if (userData) { selectedClient = userData; $('#manual-client-name').val(userData.name); $('#manual-client-phone').val(userData.phone); $('#manual-client-institution').val(userData.institution); $('#manual-client-institution-type').val(userData.institution_type).trigger('change'); fetchAndRenderDeployedDevices(userData.id); showClientDetailsView(); } });
            $('#deployed-devices-tbody').on('click', '.btn-add-deployed-to-cart', function() { const actionCell = $(this).closest('.action-cell'); const row = $(this).closest('tr'); $('.action-cell[data-active="true"]').each(function() { $(this).html($(this).data('original-html')).removeAttr('data-active'); }); actionCell.attr('data-active', 'true'); const maxQty = parseInt(row.find('.estimation-badge').text()); if (isNaN(maxQty) || maxQty <= 0) { toastr.error('Stok habis.'); actionCell.html(actionCell.data('original-html')).removeAttr('data-active'); return; } const inputHTML = `<div class="action-input-group"><input type="number" class="form-control form-control-sm deployed-qty-input" value="1" min="1" max="${maxQty}"><button class="btn btn-sm btn-primary btn-confirm-qty"><i class="ti ti-check"></i></button><button class="btn btn-sm btn-danger btn-cancel-action"><i class="ti ti-x"></i></button></div>`; actionCell.html(inputHTML).find('.deployed-qty-input').focus(); });
            $('#deployed-devices-tbody').on('click', '.btn-cancel-action', function() { const actionCell = $(this).closest('.action-cell'); actionCell.html(actionCell.data('original-html')).removeAttr('data-active'); });
            $('#deployed-devices-tbody').on('click', '.btn-confirm-qty', function() { const actionCell = $(this).closest('.action-cell'); const input = actionCell.find('.deployed-qty-input'); const qty = parseInt(input.val()); const maxQty = parseInt(input.attr('max')); if (isNaN(qty) || qty <= 0 || qty > maxQty) { toastr.error(`Jumlah tidak valid (1-${maxQty}).`); return; } actionCell.data('quantity', qty); let conditionHTML = `<div class="action-input-group"><span class="me-2 small">Rusak?</span><span class="condition-choice-text text-danger" data-condition="Rusak">Ya</span><span class="condition-choice-text text-secondary ms-2" data-condition="Bekas">Tidak</span></div>`; actionCell.html(conditionHTML); });
            $('#deployed-devices-tbody').on('click', '.condition-choice-text', function() { const actionCell = $(this).closest('.action-cell'); const row = actionCell.closest('tr'); const id = row.data('stored-device-id'); const name = row.data('device-name'); const qty = actionCell.data('quantity'); const condition = $(this).data('condition'); const originalHtml = actionCell.data('original-html'); actionCell.html(originalHtml).removeAttr('data-active'); transactionCart.push({ id, name, condition, quantity: qty, source: 'deployed' }); renderCart(); });
            $('#btn-add-to-cart').on('click', () => { const deviceId = deviceSelect.val(); const qty = parseInt($('#device-quantity').val()); const cond = (currentFlowType === 'in') ? $('#device-condition-select').val() : $('#device-condition').val(); if (!deviceId || !qty || !cond) { toastr.error('Harap lengkapi semua field perangkat!'); return; } const deviceName = deviceSelect.find('option:selected').data('name'); if (currentFlowType === 'out') { const storedDevice = allStoredDevices.find(s => s.id == deviceId); if (storedDevice && qty > storedDevice.stock) { toastr.error(`Stok tidak cukup. Tersedia: ${storedDevice.stock}`); return; } } transactionCart.push({ id: deviceId, name: deviceName, condition: cond, quantity: qty, source: 'manual' }); renderCart(); deviceSelect.val(null).trigger('change'); $('#device-condition').val(''); $('#device-condition-select').val('Baru'); $('#device-quantity').val(1); });
            $('#cart-items-tbody').on('click', '.btn-delete-cart-group', function() { const indices = $(this).data('indices'); indices.sort((a, b) => b - a).forEach(index => { transactionCart.splice(index, 1); }); renderCart(); });
            $('.letter-item').on('click', function(e) { e.preventDefault(); let details, clientProfile; try { details = $(this).data('details'); clientProfile = $(this).data('client-profile'); selectedLetter = $(this).data('letter-id'); selectedClient = { id: $(this).data('client-id') }; } catch(err) { toastr.error('Data surat tidak valid.'); return; } details.forEach(item => item.source = 'letter'); transactionCart = details; const imageUrl = clientProfile && clientProfile.image ? `${ASSET_URL}storage/${clientProfile.image}` : `${ASSET_URL}/asset/image/profile.png`; $('#letter-client-image').attr('src', imageUrl); $('#letter-client-name').text(clientProfile.name); $('#letter-client-institution').text(clientProfile.institution || 'Instansi tidak ada'); $('#letter-client-phone').text(clientProfile.phone); $('#letter-client-address').text(clientProfile.address); $('#letter-selection-container').hide(); $('#letter-client-detail-container').show(); renderCart(); });
            $('#btn-change-letter').on('click', function(e) { e.preventDefault(); transactionCart = []; selectedLetter = null; selectedClient = null; renderCart(); });
            $('#btn-process-transaction').on('click', function() { 
                $(this).prop('disabled', true).text('Processing...');
                let postData = { _token: CSRF_TOKEN, transaction_id: $('#transaction_id').val(), transaction_cart: transactionCart };
                let url = '';
                if (activeTab === 'letter') {
                    if (!selectedClient || !selectedClient.id || !selectedLetter) { toastr.error('Harap pilih surat.'); $(this).prop('disabled', false).text('Proses Transaksi'); return; }
                    postData.client_id = selectedClient.id;
                    postData.letter_id = selectedLetter;
                    url = '{{ route('admin.transaction.processFromLetter') }}';
                } else {
                    const isOtherSource = $('#is_other_source_client').val() === '1';
                    postData.flow_type = $('#flow-type').val();
                    if (isOtherSource) {
                        const otherSourceProfile = { name: $('#manual-client-name').val(), phone: $('#manual-client-phone').val(), institution: $('#manual-client-institution').val(), institution_type: $('#manual-client-institution-type').val() };
                        if (!otherSourceProfile.name) { $('#warning-for-klien').show(); $(this).prop('disabled', false).text('Proses Transaksi'); return; }
                        postData.other_source_profile = otherSourceProfile;
                        url = '{{ route('admin.transaction.processManualOtherSource') }}';
                    } else {
                        if (!selectedClient || !selectedClient.id) { toastr.error('Harap pilih klien.'); $(this).prop('disabled', false).text('Proses Transaksi'); return; }
                        postData.client_id = selectedClient.id;
                        url = transactionCart.some(item => item.source === 'deployed') ? '{{ route('admin.transaction.processManualDeployed') }}' : '{{ route('admin.transaction.processManualSelectedClient') }}';
                    }
                }
                $.ajax({
                    url: url, type: 'POST', data: postData,
                    success: function() { $('#newFlowModal').modal('hide'); Swal.fire({ icon: 'success', title: 'Transaksi Berhasil', zIndex: 2000 }); setTimeout(() => location.reload(), 1000); },
                    error: function(xhr) { let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.'; Swal.fire({ icon: 'error', title: 'Transaksi Gagal', text: msg, zIndex: 2000 }); },
                    complete: () => { $(this).prop('disabled', false).text('Proses Transaksi'); }
                });
            });

            generateTransactionId();
            updateDeviceOptions(currentFlowType);
        });
    </script>
@endsection