@extends('layout.sidebar')

@section('content')

    <!-- [Page specific CSS] start -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        .report-container { display: flex; gap: 20px; }
        .report-sidebar { flex: 0 0 280px; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,.05); }
        .report-type-list { list-style: none; padding: 0; margin: 0; }
        .report-type-item { padding: 10px 15px; margin-bottom: 5px; background-color: #f8f9fa; border-radius: 5px; cursor: pointer; transition: all .2s ease-in-out; color: #343a40; font-weight: 500; }
        .report-type-item:hover { background-color: #e2e6ea; }
        .report-type-item.active { background-color: #0d6efd; color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,.1); }
        .report-preview-wrapper { flex-grow: 1; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,.05); display: flex; flex-direction: column; height: calc(100vh - 180px); }
        .report-preview-header-container { background-color: #f8f9fa; border-radius: 8px; margin-bottom: 20px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; gap: 10px; border: 1px solid #dee2e6; }
        
        /* === KUNCI TAMPILAN A4 === */
        .report-preview-content { 
            flex-grow: 1; 
            min-height: 0; 
            background-color: #e0e0e0; /* Warna latar belakang area preview */
            border-radius: 8px; 
            padding: 20px; 
            overflow-y: auto; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            gap: 15px; /* Jarak antar halaman */
            scroll-behavior: smooth; 
        }
        .document-page { 
            background-color: #fff; 
            box-shadow: 0 0 10px rgba(0,0,0,.15); 
            padding: 25mm; 
            box-sizing: border-box; 
            border: 1px solid #ccc; 
            flex-shrink: 0; 
            width: 794px;  /* Lebar A4 dalam pixel (210mm) */
            height: 1123px; /* Tinggi A4 dalam pixel (297mm) */
            overflow: hidden; /* Konten yang berlebih akan dipotong */
            position: relative; /* Diperlukan untuk pengukuran */
        }
        /* === END KUNCI TAMPILAN A4 === */

        .document-page .report-page-header { display: flex; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #333; }
        .document-page .report-page-header .logo { width: 70px; height: auto; margin-right: 20px; }
        .document-page .report-page-header .header-text { flex-grow: 1; text-align: center; }
        .document-page .report-page-header .header-text h3 { margin: 0; font-size: 1.2em; }
        .document-page .report-page-header .header-text p { margin: 0; font-size: .8em; color: #555; }
        .document-page .report-main-title { text-align: center; font-size: 1.4em; font-weight: bold; margin-top: 20px; margin-bottom: 5px; color: #555; }
        .document-page .report-sub-title { text-align: center; font-size: 1.1em; margin-bottom: 30px; color: #555; }
        .modal-body .input-group { margin-bottom: 1rem; }
        .report-table { width: 100%; border-collapse: collapse; background-color: transparent; font-size: 8pt; margin-top: 20px; } /* Ukuran font disesuaikan agar muat lebih banyak */
        .report-table th, .report-table td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: top; }
        .report-table thead th { font-weight: bold; background-color: #f8f9fa; border-bottom: 2px solid #ccc; }
        .report-table ul { list-style: none; padding-left: 0; margin-bottom: 0; }
        .report-table ul li { padding: 2px 0; border-bottom: 1px dotted #eee; }
        .report-table ul li:last-child { border-bottom: none; }
    </style>
    <!-- [Page specific CSS] end -->

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header"><div class="page-block"><div class="row align-items-center"><div class="col-md-12"><ul class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item">Laporan</li></ul></div></div></div></div>

            <div class="report-container">
                <div class="report-sidebar">
                    <h5 class="mb-4">Pilih Laporan</h5>
                    <div class="report-type-list">
                        <div class="report-type-item active" data-report-type="inventory">Inventory</div>
                        <div class="report-type-item" data-report-type="instansi">Instansi (Klien)</div>
                        <div class="report-type-item" data-report-type="other_profile">Other Profile</div>
                        <div class="report-type-item" data-report-type="flow_transaction">Flow Transaction</div>
                        <div class="report-type-item" data-report-type="letter">Letter</div>
                        <div class="report-type-item" data-report-type="deployed_device">Deployed Device</div>
                    </div>
                </div>

                <div class="report-preview-wrapper">
                    <div class="report-preview-header-container">
                        <div>
                            <button type="button" class="btn btn-outline-primary bg-white text-primary border-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                              <i class="ti ti-filter"></i> Filter Laporan
                            </button>
                            
                            <button type="button" class="btn btn-outline-danger d-none" id="mainResetFilterBtn">
                              <i class="ti ti-x"></i> Reset Filter
                            </button>
                          </div>
                          
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="printReportBtn"><i class="ti ti-printer"></i> Cetak</button>
                            <button type="button" class="btn btn-secondary" id="downloadPdfBtn"><i class="ti ti-file-text"></i> PDF</button>
                            <button type="button" class="btn btn-info" id="exportExcelBtn"><i class="ti ti-table"></i> Excel</button>
                        </div>
                    </div>
                    <div class="report-preview-content" id="reportPreviewContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal (Sama seperti sebelumnya) -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group"><span class="input-group-text">Dari</span><input type="text" class="form-control datepicker" id="startDate" placeholder="YYYY-MM-DD"></div>
                    <div class="input-group"><span class="input-group-text">Sampai</span><input type="text" class="form-control datepicker" id="endDate" placeholder="YYYY-MM-DD"></div>
                    <hr>
                    <div id="dynamicFilters">
                        <div class="input-group dynamic-filter d-none" id="flowTransactionTypeFilter"><span class="input-group-text">Tipe</span><select class="form-select" id="transactionTypeSelect"><option value="">Semua</option><option value="in">Masuk</option><option value="out">Keluar</option></select></div>
                        <div class="input-group dynamic-filter d-none" id="flowTransactionStatusFilter"><span class="input-group-text">Status</span><select class="form-select" id="transactionStatusSelect"><option value="">Semua</option><option value="Clear">Clear</option><option value="Pending">Pending</option><option value="Deployed">Deployed</option></select></div>
                        <div class="input-group dynamic-filter d-none" id="instansiTypeFilter"><span class="input-group-text">Tipe</span><select class="form-select" id="instansiTypeSelect"><option value="">Semua</option><option value="government">Pemerintah</option><option value="private">Swasta</option></select></div>
                        <div class="input-group dynamic-filter d-none" id="inventoryConditionFilter"><span class="input-group-text">Kondisi</span><select class="form-select" id="inventoryConditionSelect"><option value="">Semua</option><option value="Baru">Baru</option><option value="Bekas">Bekas</option></select></div>
                        <div class="input-group dynamic-filter d-none" id="inventoryDeviceTypeFilter"><span class="input-group-text">Perangkat</span><select class="form-select" id="inventoryDeviceTypeSelect"><option value="">Semua</option><option value="router">Router</option></select></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning me-auto" id="modalResetFilterBtn">Reset</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="applyFilterBtn">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
    <script>
        // Deklarasi fungsi dan variabel tetap sama
        const logoUrl = "{{ asset('asset/image/icon_title.png') }}";
        function formatDeviceType(typeString) {
            if (!typeString) return '-';
            return typeString.replace(/_/g, ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        $(document).ready(function() {
            $('.datepicker').datepicker({ format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true, language: 'id' });
            let currentSelectedReportType = 'inventory';

            function getFilters() { /* ... FUNGSI SAMA ... */ return { startDate: $('#startDate').val(), endDate: $('#endDate').val(), transactionType: $('#transactionTypeSelect').val(), transactionStatus: $('#transactionStatusSelect').val(), instansiType: $('#instansiTypeSelect').val(), inventoryCondition: $('#inventoryConditionSelect').val(), inventoryDeviceType: $('#inventoryDeviceTypeSelect').val() }; }
            function isFilterActive() { /* ... FUNGSI SAMA ... */ const filters = getFilters(); return Object.values(filters).some(val => val !== '' && val !== null); }
            function checkFilterState() { /* ... FUNGSI SAMA ... */ $('#mainResetFilterBtn').toggleClass('d-none', !isFilterActive()); }
            function resetAllFilters() { /* ... FUNGSI SAMA ... */ $('#startDate, #endDate').val('').datepicker('update'); $('#dynamicFilters select').val(''); checkFilterState(); }
            function updateDynamicFiltersVisibility() { /* ... FUNGSI SAMA ... */ const type = currentSelectedReportType; $('.dynamic-filter').addClass('d-none'); if (type === 'flow_transaction') $('#flowTransactionTypeFilter, #flowTransactionStatusFilter').removeClass('d-none'); else if (type === 'instansi') $('#instansiTypeFilter').removeClass('d-none'); else if (type === 'inventory' || type === 'deployed_device') $('#inventoryConditionFilter, #inventoryDeviceTypeFilter').removeClass('d-none'); }

            function generateReportPreview() {
                // PINDAHKAN SCROLL KE ATAS SETIAP GENERATE LAPORAN
                $('#reportPreviewContent').scrollTop(0); 
                const filters = getFilters();
                $('#reportPreviewContent').html(`<div class="document-page"><div class="text-center p-5"><span class="spinner-border spinner-border-sm"></span> Memuat Laporan...</div></div>`);
                $.ajax({
                    url: '{{ route('reports.generate') }}', method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { report_types: [currentSelectedReportType], filters: filters },
                    success: (response) => renderPaginatedReport(response), // Panggil fungsi paginasi baru
                    error: (xhr) => $('#reportPreviewContent').html(`<div class="document-page"><div class="text-center p-5 text-danger">Gagal Memuat Laporan: ${xhr.responseJSON?.message || 'Error'}</div></div>`)
                });
                checkFilterState();
            }

            // ===================================================================
            // FUNGSI RENDER REPORT BARU DENGAN LOGIKA PAGINASI
            // ===================================================================
            function renderPaginatedReport(data) {
                const previewContent = $('#reportPreviewContent');
                previewContent.empty(); // Kosongkan konten sebelumnya

                const reportSection = data[currentSelectedReportType];

                // Definisikan style font di sini untuk digunakan kembali
                const fontStyle = "font-family: 'Times New Roman', Times, serif;";

                if (!reportSection || !reportSection.data || !reportSection.data.length) {
                    // Terapkan style font juga pada pesan "Tidak Ada Data"
                    previewContent.html(`<div class="document-page" style="${fontStyle}"><div class="text-center p-5">Tidak Ada Data Ditemukan</div></div>`);
                    return;
                }

                // --- 1. Buat Semua HTML yang dibutuhkan ---
                const filters = getFilters();
                let mainHeaderHtml = `<div class="report-page-header"><img src="${logoUrl}" alt="Logo" class="logo"><div class="header-text"><h3>DINAS KOMUNIKASI DAN INFORMASI KOTA PARIAMAN</h3><p>Jl. Jend. Sudirman 25-31, Pd. II, Kec. Pariaman Tengah,<br>Kota Pariaman, Sumatera Barat 25513</p></div></div>`;
                if (currentSelectedReportType === 'letter' && reportSection.data[0]) {
                    mainHeaderHtml += `<div class="report-main-title">BERITA ACARA SERAH TERIMA BARANG</div><div class="report-sub-title">Nomor: ${reportSection.data[0].letter_number || 'N/A'}</div>`;
                } else {
                    mainHeaderHtml += `<div class="report-main-title">LAPORAN ${reportSection.title?.toUpperCase() || 'UMUM'}</div><div class="report-sub-title">Tanggal Cetak: ${new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</div>`;
                }
                if(isFilterActive()) {
                    mainHeaderHtml += `<p style="font-size: smaller; color: #6c757d; text-align:center;">Periode: ${filters.startDate || 'Awal'} s/d ${filters.endDate || 'Akhir'}</p>`;
                }

                let tableHeaderHtml = '<thead>';
                if (currentSelectedReportType === 'inventory') tableHeaderHtml += '<tr><th>No</th><th>Perangkat</th><th>Model</th><th>Stok</th><th>Kondisi</th></tr>';
                else if (currentSelectedReportType === 'instansi') tableHeaderHtml += '<tr><th>No</th><th>Nama Instansi</th><th>Tipe</th><th>Kontak</th><th>Alamat</th></tr>';
                else if (currentSelectedReportType === 'other_profile') tableHeaderHtml += '<tr><th>No</th><th>Nama</th><th>Instansi</th><th>Tipe</th><th>Kontak</th></tr>';
                else if (currentSelectedReportType === 'flow_transaction') tableHeaderHtml += '<tr><th>No</th><th>ID Transaksi</th><th>Tipe</th><th>Klien/Sumber</th><th>Daftar Perangkat</th><th>Status</th><th>Tanggal</th></tr>';
                else if (currentSelectedReportType === 'letter') tableHeaderHtml += '<tr><th>No</th><th>No. Surat</th><th>Perihal</th><th>Klien</th><th>Tanggal</th></tr>';
                else if (currentSelectedReportType === 'deployed_device') tableHeaderHtml += '<tr><th>No</th><th>Penerima</th><th>Daftar Perangkat</th><th>Tanggal Deploy</th><th>Status</th></tr>';
                tableHeaderHtml += '</thead>';

                const tableRows = reportSection.data.map((item, index) => {
                    let rowHtml = `<tr><td>${index + 1}</td>`;
                    if (currentSelectedReportType === 'inventory') {
                        rowHtml += `<td>${item.device?.brand || '-'} (${formatDeviceType(item.device?.type)})</td><td>${item.device?.model || '-'}</td><td>${item.stock}</td><td>${item.condition}</td>`;
                    } else if (currentSelectedReportType === 'instansi') {
                        rowHtml += `<td>${item.institution}</td><td>${item.institution_type}</td><td>${item.phone || '-'}</td><td>${item.address || '-'}</td>`;
                    } else if (currentSelectedReportType === 'other_profile') {
                        rowHtml += `<td>${item.name}</td><td>${item.institution || '-'}</td><td>${item.institution_type || '-'}</td><td>${item.phone || '-'}</td>`;
                    } else if (currentSelectedReportType === 'flow_transaction' || currentSelectedReportType === 'deployed_device') {
                        if (currentSelectedReportType === 'flow_transaction') rowHtml += `<td>${item.transaction_id}</td><td>${item.transaction_type === 'in' ? 'Masuk' : 'Keluar'}</td>`;
                        let clientName = item.client?.profile?.name || item.other_source_profile?.name || '-';
                        rowHtml += `<td>${clientName}</td>`;
                        
                        // Perbaikan: Menambahkan kembali Serial Number (S/N)
                        let devicesList = '<ul>' + (item.details?.map(d => {
                            const device = d.stored_device?.device;
                            const serialNumber = d.stored_device?.serial_number;
                            return `<li>${device?.brand || '-'} ${device?.model || ''} ${serialNumber ? `[S/N: ${serialNumber}]` : ''}</li>`;
                        }).join('') || '<li>-</li>') + '</ul>';

                        rowHtml += `<td>${devicesList}</td><td>${item.instalation_status || item.status || '-'}</td><td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>`;
                    } else if (currentSelectedReportType === 'letter') {
                        rowHtml += `<td>${item.letter_number || '-'}</td><td>${item.subject || '-'}</td><td>${item.client?.profile?.name || '-'}</td><td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>`;
                    }
                    return rowHtml + '</tr>';
                });

                // --- 2. Proses Paginasi ---
                const MAX_HEIGHT = 1123; // Tinggi A4 dalam px
                const PADDING_TOP_BOTTOM = (25 * 3.78) * 2; // Padding 25mm atas & bawah, konversi ke px
                const CONTENT_MAX_HEIGHT = MAX_HEIGHT - PADDING_TOP_BOTTOM;

                let currentPage = 1;
                
                while(tableRows.length > 0) {
                    // PERUBAHAN: Menambahkan inline style untuk font family
                    const $page = $(`<div class="document-page" style="${fontStyle}">`).appendTo(previewContent);
                    let currentHeight = 0;

                    // Tambahkan header hanya di halaman pertama
                    if (currentPage === 1) {
                        const $header = $(mainHeaderHtml).appendTo($page);
                        currentHeight += $header.outerHeight(true);
                    }

                    const $table = $(`<table class="report-table">${tableHeaderHtml}<tbody></tbody></table>`).appendTo($page);
                    const $tbody = $table.find('tbody');
                    currentHeight += $table.find('thead').outerHeight(true);

                    // Tambahkan baris satu per satu sampai halaman penuh
                    while(tableRows.length > 0) {
                        const rowHtml = tableRows[0]; // Ambil baris pertama
                        const $tempRow = $(rowHtml);
                        $tbody.append($tempRow);
                        
                        // Cek tinggi konten di dalam halaman
                        let pageContentHeight = 0;
                        $page.children().each(function() {
                            pageContentHeight += $(this).outerHeight(true);
                        });

                        // Jika tinggi konten melebihi batas, pindahkan baris ini ke halaman berikutnya
                        if (pageContentHeight > CONTENT_MAX_HEIGHT) {
                           $tempRow.remove(); // Hapus baris yang membuat overflow
                           break; // Hentikan penambahan baris di halaman ini
                        }
                        
                        tableRows.shift(); // Hapus baris yang berhasil ditambahkan dari array utama
                    }
                    currentPage++;
                }
            }
            // --- EVENT LISTENERS (Tidak ada perubahan di sini) ---
            $('.report-type-item').on('click', function() { resetAllFilters(); $('.report-type-item').removeClass('active'); $(this).addClass('active'); currentSelectedReportType = $(this).data('report-type'); updateDynamicFiltersVisibility(); generateReportPreview(); });
            $('#applyFilterBtn').on('click', function() { generateReportPreview(); $('#filterModal').modal('hide'); });
            $('#modalResetFilterBtn, #mainResetFilterBtn').on('click', function() { resetAllFilters(); generateReportPreview(); if ($('#filterModal').is(':visible')) { $('#filterModal').modal('hide'); } });
        
            // Di dalam $(document).ready(...)

$('#printReportBtn').on('click', function() {
    const reportType = currentSelectedReportType;
    const filters = getFilters();
    const printButton = $(this);
    
    printButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyiapkan...');

    $.ajax({
        url: '{{ route('reports.printPdf') }}', // Tetap panggil route ini untuk persiapan
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            report_type: reportType,
            filters: filters
        },
        success: function(response) {
            if (response.success) {
                // PERUBAHAN UTAMA DI SINI
                // Tidak ada lagi fetch() dan blob(). Cukup set src iframe ke route baru.
                
                let iframe = document.getElementById('print-iframe');
                if (!iframe) {
                    iframe = document.createElement('iframe');
                    iframe.id = 'print-iframe';
                    iframe.style.cssText = 'position:absolute;width:0;height:0;border:0;';
                    document.body.appendChild(iframe);
                }

                iframe.onload = function() {
                    setTimeout(function() {
                        try {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                        } catch (e) {
                            console.error('Gagal print:', e);
                            alert('Gagal membuka dialog cetak. Pastikan pop-up tidak diblokir.');
                        } finally {
                            // Kembalikan tombol ke keadaan semula
                            printButton.prop('disabled', false).html('<i class="ti ti-printer"></i> Cetak');
                        }
                    }, 500); // Beri waktu 0.5 detik untuk PDF render
                };

                // Arahkan iframe ke route yang menyajikan PDF, bukan file statis
                iframe.src = '{{ route('reports.viewPrintable') }}';

            } else {
                alert('Gagal menyiapkan PDF dari server: ' + (response.message || 'Error tidak diketahui'));
                printButton.prop('disabled', false).html('<i class="ti ti-printer"></i> Cetak');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            alert('Terjadi kesalahan AJAX saat menyiapkan cetak.');
            printButton.prop('disabled', false).html('<i class="ti ti-printer"></i> Cetak');
        }
    });
});
            $('#downloadPdfBtn, #exportExcelBtn').on('click', function() { /* ... FUNGSI SAMA ... */ const isExcel = $(this).is('#exportExcelBtn'); const route = isExcel ? '{{ route('reports.exportExcel') }}' : '{{ route('reports.downloadPdf') }}'; const filters = getFilters(); const params = new URLSearchParams({ report_type: currentSelectedReportType, start_date: filters.startDate, end_date: filters.endDate, transaction_type: filters.transactionType, transaction_status: filters.transactionStatus, instansi_type: filters.instansiType, inventory_condition: filters.inventoryCondition, inventory_device_type: filters.inventoryDeviceType, }); for (const [key, value] of params.entries()) { if (!value) { params.delete(key); } } window.location.href = route + '?' + params.toString(); });

            // Pemuatan awal
            updateDynamicFiltersVisibility();
            generateReportPreview();
        });
    </script>
@endsection