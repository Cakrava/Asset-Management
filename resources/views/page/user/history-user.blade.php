@extends('layout.sidebar')

@section('content')

    {{-- Loader Preloader --}}



    @include('component.loader')
    {{-- bagian view data --}}
    <div class="pc-container">
        <div class="pc-content">
            {{-- Header route hanya tampil di desktop --}}
            <div class="page-header d-none d-md-block">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('histories.show') }}">History</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mt-4">
                <h3 class="d-none d-md-block">Riwayat Aktivitas Anda</h3>
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Tabel untuk desktop --}}
                <div class="d-none d-md-block">
                    @if(isset($histories) && count($histories) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-3">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Data Riwayat</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($histories->sortByDesc('created_at')->values() as $index => $history)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $history->history_data }}</td>
                                            <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d-m-Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mt-3">
                            Belum ada riwayat aktivitas.
                        </div>
                    @endif
                </div>

                {{-- Flatlist untuk mobile --}}
                <div class="d-block d-md-none">
                    @include('layout.bottom-navigation')

                    @if(isset($histories) && count($histories) > 0)
                        <ul class="list-group">
                            @foreach($histories->sortByDesc('created_at')->values() as $index => $history)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold mb-1" style="font-size: 0.95rem;">{{ $history->history_data }}</div>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($history->created_at)->format('d-m-Y H:i') }}</small>
                                        </div>
                                        <br>
                                        <br>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info mt-3">
                            Belum ada riwayat aktivitas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Flatlist mobile style */
        @media (max-width: 767.98px) {
            .list-group-item {
                border-radius: 10px;
                margin-bottom: 10px;
                border: 1px solid #e3e3e3;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            }
        }
    </style>

@endsection