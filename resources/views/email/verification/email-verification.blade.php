@component('mail::message')
# Verifikasi Email Anda di Komdigi

Halo {{ $userName }},

Terima kasih telah mendaftar di Komdigi. Untuk menyelesaikan pendaftaran Anda dan mengaktifkan akun Anda, silakan klik
tombol di bawah ini untuk memverifikasi alamat email Anda.

@component('mail::button', ['url' => $verificationUrl])
Verifikasi Email
@endcomponent

Jika tombol di atas tidak berfungsi, Anda bisa menyalin dan menempelkan URL berikut di browser Anda:

[{{ $verificationUrl }}]({{ $verificationUrl }})

Tautan verifikasi ini akan kedaluwarsa dalam 60 menit.

Jika Anda tidak merasa mendaftar di Komdigi, abaikan email ini.

Terima kasih,
Tim Komdigi
@endcomponent