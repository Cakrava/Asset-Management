<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeUnit\FunctionUnit;

class AuthController extends Controller
{

    //login section
    public function login(){
        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            return redirect()->route('panel.dashboard'); // Arahkan ke dashboard jika sudah login
        }
        return view('page.auth.login');

        
    }
    public function logout(){
        Auth::logout();
        Session::flush();
        return redirect()->route('front.index');
    }

    public function authenticate(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
    
        $user = User::where('email', $validatedData['email'])->first();
    
        if (!$user) {
            return redirect()->route('auth.login')
                ->withErrors(['email' => 'Email tidak terdaftar atau salah.'])
                ->withInput();
        }
    
        if (Hash::check($validatedData['password'], $user->password)) {
            // Jika password benar, langsung loginkan pengguna
            Auth::login($user);
            Session::put('email', $user->email);
            Session::put('role', $user->role);
    
            return redirect()->route('panel.dashboard');
    
        } else {
            // Jika password salah
            return redirect()->route('auth.login')
                ->withErrors(['password' => 'Password salah.'])
                ->withInput();
        }
    }
    

//register section
    public function register(){
    return view('page.auth.register');
    }
    
public function saveregister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'register_email' => 'required|email|unique:users,email',
            'register_password' => 'required|min:8',
            'confirm_password' => 'required|same:register_password', // Validasi dengan 'same'
        ], [
            'register_email.required' => 'Email wajib diisi.',
            'register_email.email' => 'Email tidak valid.',
            'register_email.unique' => 'Email sudah terdaftar.', // Memastikan email unik
            'register_password.required' => 'Password wajib diisi.',
            'register_password.min' => 'Password minimal 8 karakter.',
            'confirm_password.required' => 'Konfirmasi password wajib diisi.',
            'confirm_password.same' => 'Konfirmasi password tidak sesuai.',
        ]);
        

        if ($validator->fails()) {
            return redirect()->route('auth.register')->withErrors($validator)->withInput();
        }

        $user = User::create([
            'email' => $request->register_email,
            'password' => Hash::make($request->register_password),
            'confirm_password' => Hash::make($request->register_password),

            'role' => 'user',
        ]);

        return redirect()->route('auth.login')->with('success', 'Registrasi berhasil! Silakan login.'); // Redirect ke halaman login dengan pesan sukses
    }


   




}
