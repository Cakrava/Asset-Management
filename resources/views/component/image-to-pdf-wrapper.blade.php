<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lampiran Surat</title>
    <style>
        /* Hilangkan semua margin dan padding dari halaman PDF */
        @page { margin: 0; }
        body { margin: 0; padding: 0; }
        
        /* Pastikan gambar memenuhi seluruh halaman tanpa distorsi */
        img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* 'contain' memastikan seluruh gambar terlihat */
        }
    </style>
</head>
<body>
    {{-- Tampilkan gambar dari data base64 yang dikirim dari controller --}}
    <img src="{{ $imageSrc }}" alt="Lampiran Surat Tertanda">
</body>
</html>