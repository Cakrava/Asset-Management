<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Daftar Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        /* Container Form */
        .form-container {
            transition: transform 0.5s ease-in-out;
        }


        #registerForm {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 2rem;
            background-color: white;
            transition: transform 0.5s ease-in-out, opacity 0.3s ease-in-out;
        }


        /* Image Container */
        .image-container {
            transition: transform 0.5s ease-in-out;
        }


        /* Hapus semua gaya mobile, jadi desain desktop selalu diterapkan */
    </style>
</head>

<body class="bg-white md:bg-white flex items-center justify-center min-h-screen bg-cover bg-center">
    <div
        class="md:bg-white md:rounded-lg md:shadow-lg flex flex-col md:flex-row max-w-4xl w-full mx-auto my-8 overflow-hidden">

        <!-- Container Form Daftar -->
        <div class="w-full md:w-1/2 p-8 bg-white relative form-container">

            <!-- Form Daftar -->
            <div id="registerForm">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Akun Baru</h2>
                <form method="POST" action="{{ route('auth.register.submit') }}">
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

                    <div class="mb-4 relative">
                        <label class="block text-gray-700">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-envelope text-gray-500"></i>
                            </span>
                            <input id="register_email" name="register_email" type="email"
                                class="w-full pl-10 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('register_email') border-red-500 @enderror"
                                placeholder="Alamat Email" required value="{{ old('register_email') }}" />
                        </label>
                        @error('register_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="mb-4 relative">
                        <label class="block text-gray-700">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-gray-500"></i>
                            </span>
                            <input id="register_password" name="register_password" type="password"
                                class="w-full pl-10 pr-10 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('register_password') border-red-500 @enderror"
                                placeholder="Password" required />
                        </label>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                            <i id="password_toggle_register" class="fas fa-eye"></i>
                        </span>

                    </div>

                    <div class="mb-4 relative">
                        <label class="block text-gray-700">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-gray-500"></i>
                            </span>
                            <input id="confirm_password" name="confirm_password" type="password"
                                class="w-full pl-10 pr-10 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('register_password') border-red-500 @enderror"
                                placeholder="Password" required />
                        </label>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                            <i id="password_toggle_confirm" class="fas fa-eye"></i>
                        </span>

                    </div>
                    <div class="mb-4">
                        <button type="submit" id="registerButton"
                            class="w-full bg-[#0EA2BC] text-white py-2 rounded-lg hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-[#0EA2BC]">
                            DAFTAR
                        </button>
                    </div>


                </form>
                <div class="text-center text-gray-700 mt-4">
                    Sudah punya akun?
                    <a href="{{ route('auth.login') }}" class="text-blue-600 hover:underline cursor-pointer">
                        <b>Login di sini</b>
                    </a>
                </div>
            </div>
        </div>

        <!-- Gambar -->
        <div class="w-full md:w-1/2 image-container">
            <img alt="Mountain view with blue sky and clouds" class="w-full h-full object-cover md:rounded-r-lg"
                height="400" src="{{ asset('asset/image/login.png') }}" width="400" />
        </div>
    </div>
</body>

</html>

<script>
    const passwordInputRegister = document.querySelector('#register_password');
    const passwordToggleRegister = document.querySelector('#password_toggle_register');
    const passwordInputConfirm = document.querySelector('#confirm_password');
    const passwordToggleConfirm = document.querySelector('#password_toggle_confirm');


    passwordToggleRegister.addEventListener('click', function () {
        const type = passwordInputRegister.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInputRegister.setAttribute('type', type);
        passwordToggleRegister.classList.toggle('fa-eye');
        passwordToggleRegister.classList.toggle('fa-eye-slash');
    });

    passwordToggleConfirm.addEventListener('click', function () {
        const type = passwordInputConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInputConfirm.setAttribute('type', type);
        passwordToggleConfirm.classList.toggle('fa-eye');
        passwordToggleConfirm.classList.toggle('fa-eye-slash');
    });
</script>