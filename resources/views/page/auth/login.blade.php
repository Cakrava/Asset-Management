<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    {{-- @include('layout.icon_tittle') --}}
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<!-- Modifikasi di sini: bg-white untuk mobile, md:bg-gray-100 untuk desktop -->

<body class="bg-white md:bg-gray-100 flex items-center justify-center min-h-screen py-8 md:py-0">

   
    <div
        class="flex flex-col md:flex-row max-w-4xl w-full mx-auto md:bg-white md:rounded-lg md:shadow-lg md:overflow-hidden">

        <!-- Gambar -->
        <!-- Mobile: order-first (top), rounded-t-lg. bg-white jika body tidak putih, atau transparan jika body sudah putih. -->
        <!-- Desktop: order-last (right), md:rounded-r-lg -->
        <!-- Karena body sudah putih di mobile, kita bisa buat bg-transparent di mobile juga untuk div ini -->
        <div
            class="w-full md:w-1/2 order-first md:order-last bg-transparent md:bg-transparent rounded-t-lg md:rounded-t-none">
            <img alt="Mountain view with blue sky and clouds"
                class="w-full h-64 md:h-full object-cover rounded-t-lg md:rounded-t-none md:rounded-r-lg"
                src="{{ asset('asset/image/login.png') }}" />
        </div>

      <div
            class="w-full md:w-1/2 p-8 order-last md:order-first bg-transparent md:bg-transparent rounded-b-lg md:rounded-b-none md:rounded-l-lg">
            <!-- Form Login -->
            <div id="loginForm">
                <!-- Form login ini sendiri tidak perlu bg-white karena parent nya sudah putih atau transparan ke body putih -->
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Login ke Akun</h2>
                <p style="color: gray">Silakan masukkan email dan password Anda untuk masuk ke akun.</p>
                <br>
                <form method="POST" action="{{ route('auth.authenticate') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error!</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (request('message'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Warning!</strong>

                            <p>{{ request('message') }}</p>
                        </div>
                    @endif

                    @if (session('message'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Warning!</strong>
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Success!</strong>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="mb-4 relative">
                        <label class="block text-gray-700">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-envelope text-gray-500"></i>
                            </span>
                            <input id="email" name="email" type="email" required autocomplete="email" autofocus
                                class="w-full pl-10 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('email') border-red-500 @enderror"
                                placeholder="Alamat Email" value="{{ old('email') }}" />
                        </label>
                    </div>

                    <div class="mb-4 relative">
                        <label class="block text-gray-700">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-gray-500"></i>
                            </span>
                            <input id="password" name="password" type="password" required
                                autocomplete="current-password"
                                class="w-full pl-10 pr-10 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('password') border-red-500 @enderror"
                                placeholder="Password" />
                        </label>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                            <i id="password_toggle_login" class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <label class="flex items-center">
                            <input class="form-checkbox text-blue-600" type="checkbox" name="remember" id="remember" />
                            <span class="ml-2 text-gray-700">Ingat saya</span>
                        </label>
                        <a class="text-blue-600 hover:underline text-sm" href="#">Lupa Password? (Coming Soon)</a>
                    </div>

                    <div class="mb-4">
                        <button type="submit"
                            class="w-full bg-[#0EA2BC] text-white py-2 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-[#0EA2BC]">
                            LOGIN
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    // Hapus semua data di localStorage saat halaman dimuat
    window.addEventListener('DOMContentLoaded', function () {
        localStorage.clear();

        const passwordInputLogin = document.querySelector('#password');
        const passwordToggleLogin = document.querySelector('#password_toggle_login');

        if (passwordInputLogin && passwordToggleLogin) { // Cek null
            passwordToggleLogin.addEventListener('click', function () {
                const type = passwordInputLogin.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInputLogin.setAttribute('type', type);
                passwordToggleLogin.classList.toggle('fa-eye');
                passwordToggleLogin.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>