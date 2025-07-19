@extends('layout.sidebar')

@section('content')
    {{-- Dependensi CSS dan JS --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <style>
        .card-statistic .avtar { width: 50px; height: 50px; font-size: 24px; }
        .list-group-item-action:hover { background-color: #f8f9fa; }
        .table-responsive thead th { white-space: nowrap; }
    </style>

    <div class="pc-container">
        <div class="pc-content">
            {{-- Header --}}
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center"><div class="col-md-12"><div class="page-header-title"><h5 class="m-b-10">Dashboard Operasional</h5></div><ul class="breadcrumb"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item" aria-current="page">Dashboard</li></ul></div></div>
                </div>
            </div>

            {{-- Notifikasi Profil (jika ada) --}}
            @if(session('istemporary') === true || (isset($completionPercentage) && $completionPercentage < 100))
                <div class="row">
                    @if($completionPercentage < 100)
                        <div class="col-xl-12"><div class="card border-primary border-opacity-50 shadow-sm"><div class="card-body"><h4 class="card-title text-primary mb-2">Profil Tidak Lengkap!</h4><p class="card-text text-secondary small">Selesaikan profil untuk pengalaman terbaik. <a href="{{ route('panel.profile') }}" class="text-primary">Lengkapi Sekarang <i class="ti ti-arrow-right"></i></a></p></div></div></div>
                    @endif
                </div>
            @endif

            <!-- Kartu Statistik Utama -->
            <div class="row">
                <div class="col-md-6 col-xl-3"><div class="card card-statistic"><div class="card-body"><div class="d-flex align-items-center"><div class="avtar bg-light-primary text-primary rounded-circle"><i class="ti ti-package"></i></div><div class="ms-3"><p class="mb-0 text-muted">Stok di Gudang</p><h4 class="mb-0">{{ number_format($totalDeviceStock) }}</h4></div></div></div></div></div>
                <div class="col-md-6 col-xl-3"><div class="card card-statistic"><div class="card-body"><div class="d-flex align-items-center"><div class="avtar bg-light-success text-success rounded-circle"><i class="ti ti-stack"></i></div><div class="ms-3"><p class="mb-0 text-muted">Aset Terpasang</p><h4 class="mb-0">{{ number_format($totalDeployedDevices) }}</h4></div></div></div></div></div>
                <div class="col-md-6 col-xl-3"><div class="card card-statistic"><div class="card-body"><div class="d-flex align-items-center"><div class="avtar bg-light-warning text-warning rounded-circle"><i class="ti ti-clock"></i></div><div class="ms-3"><p class="mb-0 text-muted">Transaksi Pending</p><h4 class="mb-0">{{ $pendingTransactionsCount }}</h4></div></div></div></div></div>
                <div class="col-md-6 col-xl-3"><div class="card card-statistic"><div class="card-body"><div class="d-flex align-items-center"><div class="avtar bg-light-danger text-danger rounded-circle"><i class="ti ti-ticket"></i></div><div class="ms-3"><p class="mb-0 text-muted">Tiket Terbuka</p><h4 class="mb-0">{{ $openTicketsCount }}</h4></div></div></div></div></div>
            </div>

            <!-- Grafik -->
            <div class="row">
                <div class="col-md-12 col-xl-8"><div class="card"><div class="card-header"><h5>Aktivitas Transaksi (7 Hari Terakhir)</h5></div><div class="card-body"><canvas id="transactionActivityChart" style="height: 300px; width: 100%;"></canvas></div></div></div>
                <div class="col-md-12 col-xl-4"><div class="card"><div class="card-header"><h5>Distribusi Status Transaksi</h5></div><div class="card-body"><canvas id="transactionStatusChart" style="height: 300px; width: 100%;"></canvas></div></div></div>
            </div>

            <!-- Daftar & Tabel -->
            <div class="row">
                <div class="col-md-12 col-xl-7"><div class="card"><div class="card-header d-flex align-items-center justify-content-between"><h5 class="mb-0">Transaksi Terbaru</h5><a href="#" class="link-primary">Lihat Semua</a></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover table-borderless mb-0"><thead><tr><th>ID Transaksi</th><th>Klien</th><th class="text-center">Tipe</th><th class="text-center">Status</th></tr></thead><tbody>@forelse($recentTransactions as $t)<tr><td><small class="text-muted">{{$t->transaction_id}}</small></td><td>{{$t->client?->profile?->name ?? $t->otherSourceProfile?->name ?? 'N/A'}}</td><td class="text-center">@if($t->transaction_type=='in')<span class="badge bg-light-success text-success">In</span>@else<span class="badge bg-light-danger text-danger">Out</span>@endif</td><td class="text-center">@if($t->instalation_status=='Deployed')<span class="badge bg-success">Deployed</span>@elseif($t->instalation_status=='Pending')<span class="badge bg-warning">Pending</span>@else<span class="badge bg-secondary">{{$t->instalation_status}}</span>@endif</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>@endforelse</tbody></table></div></div></div></div>
                <div class="col-md-12 col-xl-5"><div class="card"><div class="card-header"><h5 class="mb-0">Perlu Tindakan</h5></div><div class="card-body p-0"><div class="list-group list-group-flush"><div class="list-group-item"><div class="d-flex align-items-center justify-content-between"><div><i class="ti ti-file-text text-primary me-2"></i>Surat Siap Diproses</div><span class="badge bg-light-primary rounded-pill">{{$neededLettersCount}}</span></div>@if($neededLetters->count()>0)<ul class="list-unstyled mt-2 mb-0">@foreach($neededLetters as $l)<li class="small text-muted border-top pt-2 mt-2"><a href="#">{{$l->letter_number}}</a> <span class="float-end">{{$l->client?->profile?->institution}}</span></li>@endforeach</ul>@endif</div><div class="list-group-item"><div class="d-flex align-items-center justify-content-between"><div><i class="ti ti-ticket text-danger me-2"></i>Tiket Bantuan Mendesak</div><span class="badge bg-light-danger rounded-pill">{{$openTicketsCount}}</span></div>@if($urgentTickets->count()>0)<ul class="list-unstyled mt-2 mb-0">@foreach($urgentTickets as $ticket)<li class="small text-muted border-top pt-2 mt-2"><a href="#">{{Str::limit($ticket->subject,30)}}</a> <span class="float-end">{{$ticket->user?->profile?->name}}</span></li>@endforeach</ul>@endif</div></div></div></div></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded',function(){const t=@json($transactionChartData),a=@json($transactionStatusDistribution);const e=document.getElementById("transactionActivityChart");e&&new Chart(e,{type:"line",data:{labels:t.labels,datasets:[{label:"Aset Masuk (In)",data:t.inData,borderColor:"rgba(25, 135, 84, 0.8)",backgroundColor:"rgba(25, 135, 84, 0.1)",borderWidth:2,fill:!0,tension:.4},{label:"Aset Keluar (Out)",data:t.outData,borderColor:"rgba(220, 53, 69, 0.8)",backgroundColor:"rgba(220, 53, 69, 0.1)",borderWidth:2,fill:!0,tension:.4}]},options:{responsive:!0,maintainAspectRatio:!1,scales:{y:{beginAtZero:!0,ticks:{stepSize:1}}},plugins:{legend:{position:"top"}}}});const i=document.getElementById("transactionStatusChart");i&&new Chart(i,{type:"doughnut",data:{labels:Object.keys(a),datasets:[{label:"Jumlah Transaksi",data:Object.values(a),backgroundColor:["rgba(255, 193, 7, 0.7)","rgba(25, 135, 84, 0.7)","rgba(13, 110, 253, 0.7)","rgba(108, 117, 125, 0.7)"],borderColor:"#fff",borderWidth:2}]},options:{responsive:!0,maintainAspectRatio:!1,plugins:{legend:{position:"bottom"}}}})});
    </script>
@endsection