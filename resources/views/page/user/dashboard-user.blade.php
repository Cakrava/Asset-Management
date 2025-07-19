@extends('layout.sidebar')

@section('content')

<style>
/* Gaya untuk progress tracker (TIDAK BERUBAH) */
.progress-tracker{display:flex;align-items:flex-start;justify-content:space-between;position:relative;width:100%}.progress-tracker::before{content:'';position:absolute;top:20px;left:10%;right:10%;height:4px;background-color:#e9ecef;z-index:1}.progress-tracker .step{display:flex;flex-direction:column;align-items:center;text-align:center;width:180px;z-index:2;position:relative}.progress-tracker .step-icon{width:40px;height:40px;border-radius:50%;background-color:#e9ecef;color:#adb5bd;display:flex;align-items:center;justify-content:center;font-size:1.2rem;border:4px solid #e9ecef;transition:all .3s ease}.progress-tracker .step-label{margin-top:10px;font-weight:500;color:#6c757d;font-size:.9rem}.progress-tracker .step-sublabel{font-size:.8rem;color:#adb5bd}.progress-tracker .step.step-active .step-icon{background-color:#fff;border-color:var(--bs-primary);color:var(--bs-primary)}.progress-tracker .step.step-active .step-label{color:var(--bs-primary);font-weight:600}.progress-tracker .step.step-completed .step-icon{background-color:var(--bs-success);border-color:var(--bs-success);color:#fff}.progress-tracker .step.step-completed .step-label{color:#212529}.progress-tracker .step.step-completed .step-sublabel{color:#6c757d}

/* Gaya untuk responsivitas (BARU) */
@media (max-width: 767.98px) {
    /* Mengatur kartu statistik menjadi 2 kolom di perangkat seluler */
    .stat-card {
        flex: 0 0 auto;
        width: 50%;
    }
    .page-header .breadcrumb {
        justify-content: flex-start;
    }
     .card-body.d-flex.align-items-center {
        flex-direction: column;
        text-align: center;
    }
    .card-body.d-flex.align-items-center .ms-3 {
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
     .card-body.d-flex.align-items-center .ms-auto {
        margin-left: 0 !important;
        margin-top: 1rem;
    }
}
</style>
@include('layout.bottom-navigation')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title"><h5 class="m-b-10">Dashboard</h5></div>
                        <ul class="breadcrumb"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item" aria-current="page">Dashboard</li></ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">

            <!-- [ Kartu Notifikasi Profil & Akun Sementara ] start -->
            @if(session('istemporary') === true)
                <div class="col-12">
                    <div class="card border-warning border-opacity-50 shadow-sm mb-4">
                        <div class="card-body d-flex align-items-center">
                            <i class="ti ti-alert-triangle text-warning me-3" style="font-size: 2.5rem;"></i>
                            <div>
                                <h5 class="card-title text-warning mb-1">Peringatan: Akun Sementara!</h5>
                                <p class="card-text text-secondary mb-0 small">Untuk keamanan, silakan ganti password dan email Anda.</p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            @endif

            @if($completionPercentage < 100)
                <div class="col-12">
                    <div class="card border-primary border-opacity-50 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-2">Profil Anda Belum Lengkap!</h5>
                            <p class="card-text text-secondary small">Lengkapi profil untuk membuka semua fitur dan mempermudah proses.</p>
                            <div class="progress mb-2 bg-light" style="height: 5px;">
                                <div class="progress-bar progress-bar-animated bg-primary" role="progressbar" style="width: {{ $completionPercentage }}%;" aria-valuenow="{{ $completionPercentage }}"></div>
                            </div>
                            <p class="card-text small text-muted mb-0">Kurang <strong>{{ round(100 - $completionPercentage) }}%</strong> lagi. <a href="{{ route('panel.profile') }}" class="text-primary fw-bold">Lengkapi Sekarang <i class="fas fa-arrow-right ms-1"></i></a></p>
                        </div>
                    </div>
                </div>
            @endif
            <!-- [ Kartu Notifikasi ] end -->

            {{-- Cek apakah ada aktivitas sama sekali (termasuk tiket) --}}
            @if($ongoingLetters->isEmpty() && $allClosedLetters->isEmpty() && $deployedDeviceDetails->isEmpty() && $allUserTickets->isEmpty())
                <!-- [ Tampilan Selamat Datang (Jika tidak ada aktivitas) ] start -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center p-lg-5">
                            <i class="ti ti-folders text-muted" style="font-size: 5rem;"></i>
                            <h4 class="mt-4">Selamat Datang di Dashboard Anda!</h4>
                            <p class="text-muted">Saat ini belum ada aktivitas (surat, tiket, perangkat) yang tercatat. <br> Mulai dengan membuat pengajuan baru.</p>
                            <a href="#" class="btn btn-primary mt-3"><i class="ti ti-plus me-1"></i> Buat Pengajuan Baru</a>
                        </div>
                    </div>
                </div>
                <!-- [ Tampilan Selamat Datang ] end -->
            @else
                <!-- [ Kartu Statistik ] start -->
                 <div class="col-6 col-md-6 col-lg-3 stat-card">
                    <div class="card shadow-sm"><div class="card-body d-flex align-items-center">
                        <div class="avtar avtar-lg bg-light-primary text-primary rounded-circle"><i class="ti ti-clock"></i></div>
                        <div class="ms-3"><h4 class="mb-0">{{ count($ongoingLetters) }}</h4><p class="mb-0 text-muted small">Proses Surat</p></div>
                    </div></div>
                </div>
                <div class="col-6 col-md-6 col-lg-3 stat-card">
                    <div class="card shadow-sm"><div class="card-body d-flex align-items-center">
                        <div class="avtar avtar-lg bg-light-warning text-warning rounded-circle"><i class="ti ti-ticket"></i></div>
                        <div class="ms-3"><h4 class="mb-0">{{ $pendingTicketsCount }}</h4><p class="mb-0 text-muted small">Tiket Pending</p></div>
                    </div></div>
                </div>
                <div class="col-6 col-md-6 col-lg-3 stat-card">
                    <div class="card shadow-sm"><div class="card-body d-flex align-items-center">
                        <div class="avtar avtar-lg bg-light-success text-success rounded-circle"><i class="ti ti-device-desktop-analytics"></i></div>
                        <div class="ms-3"><h4 class="mb-0">{{ $deployedDeviceDetails->sum('quantity') }}</h4><p class="mb-0 text-muted small">Unit Terpasang</p></div>
                    </div></div>
                </div>
                <div class="col-6 col-md-6 col-lg-3 stat-card">
                    <div class="card shadow-sm"><div class="card-body d-flex align-items-center">
                        <div class="avtar avtar-lg bg-light-info text-info rounded-circle"><i class="ti ti-history"></i></div>
                        <div class="ms-3"><h4 class="mb-0">{{ count($allClosedLetters) }}</h4><p class="mb-0 text-muted small">Total Riwayat</p></div>
                    </div></div>
                </div>
                <!-- [ Kartu Statistik ] end -->

                <!-- [ Kolom Utama (Kiri) - Aktivitas ] start -->
                <div class="col-xl-8">
                    <!-- [ Proses Surat Berlangsung ] start -->
                    @if(!$ongoingLetters->isEmpty())
                    <div class="card shadow-sm mt-4">
                        <div class="card-header"><h5 class="mb-0">Proses Surat yang Sedang Berlangsung</h5></div>
                        <div class="card-body p-2">
                            <div class="accordion" id="ongoingLettersAccordion">
                                @foreach ($ongoingLetters as $letter)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-{{$letter->id}}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{$letter->id}}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                                <span class="fw-bold text-dark">Surat: {{ $letter->letter_number }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{$letter->id}}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#ongoingLettersAccordion">
                                            <div class="accordion-body py-4">
                                                <div class="progress-tracker">
                                                    <div class="step {{ $letter->status == 'Needed' ? 'step-active' : 'step-completed' }}">
                                                        <div class="step-icon"><i class="{{ $letter->status == 'Needed' ? 'ti ti-hourglass-high' : 'ti ti-check' }}"></i></div>
                                                        <div class="step-label">Dalam Transaksi</div><div class="step-sublabel">Pembuatan Dokumen</div>
                                                    </div>
                                                    <div class="step {{ $letter->status == 'Open' ? 'step-active' : 'step-completed' }}">
                                                        <div class="step-icon"><i class="{{ $letter->status == 'Open' ? 'ti ti-edit' : 'ti ti-check' }}"></i></div>
                                                        <div class="step-label">Menunggu TTD</div><div class="step-sublabel">Proses Serah Terima</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- [ Proses Surat Berlangsung ] end -->

                    <!-- [ Daftar Tiket Terbaru ] start -->
                    <div class="card shadow-sm mt-4">
                         <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tiket Terbaru</h5>
                            @if(count($allUserTickets) > 3)
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#allTicketsModal">
                                    Lihat Semua
                                </button>
                            @endif
                         </div>
                         <div class="card-body p-0">
                             <div class="table-responsive">
                                 <table class="table table-hover mb-0">
                                     <thead class="table-light"><tr><th>ID Tiket</th><th>Subjek</th><th>Status</th><th class="text-end">Pembaruan</th></tr></thead>
                                     <tbody>
                                         @forelse ($recentUserTickets as $ticket)
                                             <tr class="align-middle">
                                                <td><span class="fw-bold">#{{ $ticket->id }}</span></td>
                                                <td>{{ $ticket->subject ?? 'Tidak ada subjek' }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = ['pending'=>'badge bg-light-warning text-warning','completed'=>'badge bg-light-success text-success','rejected'=>'badge bg-light-danger text-danger','canceled'=>'badge bg-light-secondary text-secondary',][$ticket->status] ?? 'badge bg-light-info text-info';
                                                    @endphp
                                                    <span class="{{ $statusClass }}">{{ ucfirst($ticket->status) }}</span>
                                                    @if($ticket->status == 'completed')
                                                        <small class="d-block text-muted mt-1">Surat telah diterbitkan.</small>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ $ticket->updated_at->diffForHumans() }}</td>
                                             </tr>
                                         @empty
                                             <tr><td colspan="4" class="text-center py-4"><p class="text-muted mb-0">Anda belum memiliki tiket.</p></td></tr>
                                         @endforelse
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                    </div>
                    <!-- [ Daftar Tiket Terbaru ] end -->

                    <!-- [ Riwayat Surat Terbaru ] start -->
                     <div class="card shadow-sm mt-4">
                         <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Riwayat Surat Terbaru</h5>
                            @if(count($allClosedLetters) > 5)
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#allLettersModal">
                                    Lihat Semua
                                </button>
                            @endif
                         </div>
                         <div class="card-body p-0">
                             <div class="table-responsive">
                                 <table class="table table-hover mb-0">
                                     <thead class="table-light"><tr><th>Nomor Surat</th><th>Tanggal Selesai</th><th class="text-center">Aksi</th></tr></thead>
                                     <tbody>
                                         @forelse ($recentClosedLetters as $letter)
                                             <tr class="align-middle">
                                                <td><span class="fw-bold">{{ $letter->letter_number }}</span></td>
                                                <td>{{ $letter->updated_at->translatedFormat('d F Y') }}</td>
                                                <td class="text-center">
                                                    @if($letter->pdf_path && Storage::disk('public')->exists($letter->pdf_path))
                                                        <a href="{{ route('panel.letter.view_signed_archive', $letter->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="ti ti-download me-1"></i>PDF</a>
                                                    @else
                                                        <span class="badge bg-light-secondary text-secondary">Tidak Tersedia</span>
                                                    @endif
                                                </td>
                                             </tr>
                                         @empty
                                             <tr><td colspan="3" class="text-center py-4"><p class="text-muted mb-0">Belum ada riwayat surat yang selesai.</p></td></tr>
                                         @endforelse
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                    <!-- [ Riwayat Surat Terbaru ] end -->
                </div>
                <!-- [ Kolom Utama (Kiri) ] end -->

                <!-- [ Kolom Samping (Kanan) - Info Statis ] start -->
                <div class="col-xl-4">
                     <div class="card shadow-sm mt-4">
                         <div class="card-header"><h5 class="mb-0">Perangkat Terpasang</h5></div>
                         <div class="card-body p-0">
                            @if(!$deployedDeviceDetails->isEmpty())
                             <div class="table-responsive">
                                 <table class="table table-hover mb-0">
                                     <thead class="table-light"><tr><th>Perangkat</th><th class="text-end">Jumlah</th></tr></thead>
                                     <tbody>
                                         @foreach ($deployedDeviceDetails as $detail)
                                             <tr class="align-middle">
                                                <td>
                                                    <span class="fw-bold">{{ $detail->storedDevice->device->brand ?? 'N/A' }}</span>
                                                    <small class="text-muted d-block">{{ $detail->storedDevice->device->model ?? '' }}</small>
                                                </td>
                                                <td class="text-end fw-bold">{{ $detail->quantity }} <span class="text-muted small">Unit</span></td>
                                             </tr>
                                         @endforeach
                                     </tbody>
                                 </table>
                             </div>
                             @else
                                <div class="text-center py-5"><p class="text-muted mb-0">Belum ada perangkat terpasang.</p></div>
                             @endif
                         </div>
                     </div>
                </div>
                <!-- [ Kolom Samping (Kanan) ] end -->
            @endif
        </div>
    </div>
</div>

<!-- ======================================================================================= -->
<!--                                  MODALS (TANPA PARTIAL)                                 -->
<!-- ======================================================================================= -->

<!-- Modal untuk Semua Tiket -->
<div class="modal fade" id="allTicketsModal" tabindex="-1" aria-labelledby="allTicketsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="allTicketsModalLabel">Daftar Lengkap Tiket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
             <table class="table table-hover mb-0">
                 <thead class="table-light"><tr><th>ID Tiket</th><th>Subjek</th><th>Status</th><th class="text-end">Pembaruan</th></tr></thead>
                 <tbody>
                     @forelse ($allUserTickets as $ticket)
                        <tr class="align-middle">
                           <td><span class="fw-bold">#{{ $ticket->id }}</span></td>
                           <td>{{ $ticket->subject ?? 'Tidak ada subjek' }}</td>
                           <td>
                               @php
                                   $statusClass = ['pending'=>'badge bg-light-warning text-warning','completed'=>'badge bg-light-success text-success','rejected'=>'badge bg-light-danger text-danger','canceled'=>'badge bg-light-secondary text-secondary',][$ticket->status] ?? 'badge bg-light-info text-info';
                               @endphp
                               <span class="{{ $statusClass }}">{{ ucfirst($ticket->status) }}</span>
                               @if($ticket->status == 'completed')
                                   <small class="d-block text-muted mt-1">Surat telah diterbitkan.</small>
                               @endif
                           </td>
                           <td class="text-end">{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                     @empty
                         <tr><td colspan="4" class="text-center py-5"><p class="text-muted mb-0">Tidak ada tiket untuk ditampilkan.</p></td></tr>
                     @endforelse
                 </tbody>
             </table>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal untuk Semua Riwayat Surat -->
<div class="modal fade" id="allLettersModal" tabindex="-1" aria-labelledby="allLettersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="allLettersModalLabel">Riwayat Lengkap Surat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Nomor Surat</th><th>Tanggal Selesai</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($allClosedLetters as $letter)
                        <tr class="align-middle">
                           <td><span class="fw-bold">{{ $letter->letter_number }}</span></td>
                           <td>{{ $letter->updated_at->translatedFormat('d F Y') }}</td>
                           <td class="text-center">
                               @if($letter->pdf_path && Storage::disk('public')->exists($letter->pdf_path))
                                   <a href="{{ route('panel.letter.view_signed_archive', $letter->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="ti ti-download me-1"></i>PDF</a>
                               @else
                                   <span class="badge bg-light-secondary text-secondary">Tidak Tersedia</span>
                               @endif
                           </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-5"><p class="text-muted mb-0">Tidak ada riwayat surat untuk ditampilkan.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>

@endsection