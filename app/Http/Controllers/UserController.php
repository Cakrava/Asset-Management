<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

use Illuminate\Support\Str;
class UserController extends Controller
{
   public function index(){
    $userId = Auth::id();
    $email = Auth::user()->email;
    $profile = Profile::where('user_id', $userId)->first() ?? new Profile();

    $fields = ['name', 'phone', 'institution', 'institution_type', 'address', 'reference', 'image'];
    $isComplete = true;

    foreach ($fields as $field) {
        if (empty($profile->$field)) {
            $isComplete = false;
            break;
        }
    }

    if (!$isComplete) {
        session()->put('profile_incomplete_badge', 'yes');
    } else {
        session()->forget('profile_incomplete_badge');
    }

    return view('page.profil', compact('profile', 'email'));
   }


   public function update(Request $request)
   {
       // Validator Anda sudah sempurna, tidak perlu diubah.
       $validator = Validator::make($request->all(), [
           'name' => 'nullable|string|max:255',
           'phone' => 'nullable|string|max:20',
           'institution' => 'nullable|string|max:255',
           'institution_type' => 'nullable|string|max:255',
           'address' => 'nullable|string',
           'reference' => 'nullable|string',
           'profile_image' => [
               'nullable',
               Rule::when($request->hasFile('profile_image'), [
                   'mimes:jpeg,png,jpg,gif,svg', 'max:2048'
               ]),
               Rule::when(is_string($request->input('profile_image')), [
                   'string',
                   function ($attribute, $value, $fail) {
                       if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $value)) {
                           $fail('Format gambar dari cropper tidak valid.'); return;
                       }
                       $decodedData = base64_decode(last(explode(',', $value)));
                       if ((strlen($decodedData) / 1024) > 2048) {
                           $fail('Ukuran gambar tidak boleh lebih dari 2048 KB.');
                       }
                   },
               ]),
           ],
       ], [
           'profile_image.mimes' => 'File harus berupa JPEG, PNG, JPG atau SVG.',
           'profile_image.max' => 'Gambar tidak boleh lebih dari 2048 KB.',
           // ... pesan error lainnya
       ]);
   
       if ($validator->fails()) {
           return Redirect::back()->withErrors($validator)->withInput();
       }
   
       $user = Auth::user();
       if (!$user) {
           return Redirect::back()->with('error', 'User tidak ditemukan.');
       }
   
       $profile = Profile::firstOrNew(['user_id' => $user->id]);
   
       // Menetapkan semua nilai TEKS dari request ke model
       $profile->user_id = $user->id;
       $profile->name = $request->name;
       $profile->phone = $request->phone;
       $profile->institution = $request->institution;
       $profile->institution_type = $request->institution_type;
       $profile->reference = $request->reference;
       $profile->address = $request->address;
   
       $newImagePath = null; // Variabel untuk menampung path gambar baru
       $newImageName = null; // Variabel untuk nama file baru untuk session
   
       // --- Logika pemrosesan gambar ---
       if ($request->hasFile('profile_image')) {
           // Skenario 1: Input adalah FILE
           $image = $request->file('profile_image');
           $newImageName = time() . '.' . $image->getClientOriginalExtension();
           $newImagePath = $image->storeAs('profile_images', $newImageName, 'public');
   
       } else if ($request->filled('profile_image') && is_string($request->input('profile_image'))) {
           // Skenario 2: Input adalah STRING BASE64
           $imageData = $request->input('profile_image');
           @list($type, $imageData) = explode(';', $imageData);
           @list(, $imageData) = explode(',', $imageData);
           $imageData = base64_decode($imageData);
   
           $newImageName = Str::random(40) . '.jpg';
           $newImagePath = 'profile_images/' . $newImageName;
   
           Storage::disk('public')->put($newImagePath, $imageData);
       }
       

       
       // [LOGIKA TERPUSAT] Jika ada gambar baru yang berhasil diproses
       if ($newImagePath) {
           $oldImagePath = $profile->image; // Ambil path lama dari model
           $profile->image = $newImagePath; // Tetapkan path BARU ke model
           Session::put('image_profile_name', $newImageName);
   
           // Hapus file lama JIKA ada dan BUKAN gambar default
           if ($oldImagePath && $oldImagePath !== 'asset/image/profile.png' && Storage::disk('public')->exists($oldImagePath)) {
               Storage::disk('public')->delete($oldImagePath);
           }
       } else {
           Session::forget('image_profile_name');
       }
   
       // Bagian ini ke bawah tidak perlu diubah sama sekali
       if ($profile->save()) {
           $profile->fresh();
   
           $requiredFields = [
               'name', 'phone', 'institution', 'institution_type', 'reference', 'address',
           ];
           $totalFields = count($requiredFields);
           $filledFields = 0;
           foreach ($requiredFields as $field) {
               if (!empty($profile->$field)) {
                   $filledFields++;
               }
           }
           $completionPercentage = ($totalFields > 0) ? round(($filledFields / $totalFields) * 100) : 0;
   
           if ($completionPercentage < 100) {
               session()->put('profile_incomplete', 'Kelengkapan profil mu hanya ' . $completionPercentage . '%.<br> <a href="' . route('panel.profile') . '" style="color: green;">lengkapi</a>.');
           } else {
               session()->forget('profile_incomplete');
           }
           return Redirect::back()->with('success', 'Profile berhasil diperbarui.');
       } else {
           return Redirect::back()->with('error', 'Gagal menyimpan profile.');
       }
   }
   

   public function changePassword(Request $request)
    {
        // Validasi input form
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8', // 'confirmed' memastikan new_password cocok dengan confirm_password
            'confirm_password' => 'required|string|same:new_password', // confirm_password tetap dibutuhkan karena rule 'confirmed'
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'confirm_password.same' => 'Konfirmasi password baru tidak cocok.',
            'confirm_password.required' => 'Konfirmasi password baru wajib diisi.', // Pesan error untuk confirm_password jika diperlukan
        ]);

        
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        if (!$user) {
            return Redirect::back()->with('error', 'User tidak ditemukan.'); // Handle jika user tidak login (seharusnya tidak terjadi jika halaman ini hanya untuk user login)
        }

        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');

        // Verifikasi password lama dengan password di database
        if (!Hash::check($currentPassword, $user->password)) {
            return Redirect::back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
        }

        // Pastikan password baru tidak sama dengan password lama
        if (Hash::check($newPassword, $user->password)) {
            return Redirect::back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama.'])->withInput();
        }

        // Hash password baru dan update di database
        $hashedNewPassword = Hash::make($newPassword);

        $user->password = $hashedNewPassword; // Update kolom password
        // Tidak perlu update kolom confirm_password, karena confirm_password hanya untuk validasi

        if ($user->save()) {
            return Redirect::back()->with('success', 'Password berhasil diubah.');
        } else {
            return Redirect::back()->with('error', 'Gagal mengubah password.');
        }
    }



    public function accountSettings()
    {
        $userId  = Auth::id();
    
        $users = User::findOrFail($userId);
    
        $allUsers = User::where('id', '!=', $userId)
            ->whereColumn('created_at', '=', 'updated_at') // hanya yang belum pernah diedit
            ->get();
    
        return view('page.account_settings', compact('users', 'allUsers'));
    }
    

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'email' => 'required|email',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus terdiri dari 8 karakter.',
            'password_confirmation.same' => 'Konfirmasi password tidak cocok.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        $user->save();
        session()->put('istemporary', false);

        return back()->with('success', 'Pengaturan akun berhasil diperbarui.');
    }


    public function manageAccount()
    {
        $users = User::all();
        return view('page.manage_account', compact('users'));
    }


    public function generateAccount(Request $request)
    {
        // Karena form sekarang hanya membuat satu jenis akun (user/instansi),
        // kita bisa mengatur nilai default secara langsung tanpa perlu kondisi.
        $default_password = 'defaultnewaccount';
        $default_email_suffix = '@institution.com';
        $default_role = 'user';
    
        // Membuat email unik secara acak
        $generated_email = Str::random(4) . $default_email_suffix;
        
        // Melakukan hash pada password untuk keamanan
        $hashedPassword = Hash::make($default_password);

        // Membuat user baru di database
        $newUser = User::create([
            'email' => $generated_email,
            'password' => $hashedPassword,
            'confirm_password' => $hashedPassword,
            'role' => $default_role,
            'status_verification' => 'unverivied',
        ]);
    
        if ($newUser) {
            // Jika berhasil, kembalikan dengan pesan sukses yang menyertakan kredensial
            return back()->with('success', 'Akun berhasil dibuat. Email: ' . $generated_email . ' Password: ' . $default_password);
        } else {
            // Jika gagal, kembalikan dengan pesan error
            return back()->with('error', 'Gagal membuat akun.');
        }
    }
    public function generateAdministrator()
    {
        $generated_password = 'defaultnewadministrator';
        $generated_email = Str::random(8) . '@administrator.com'; // You can customize email generation

        $newUser = User::create([
            'email' => $generated_email,
            'password' => Hash::make($generated_password),
            'confirm_password' => Hash::make($generated_password), // Consider if confirm_password is needed for new users
            'role' => 'admin', // Default role
       
        ]);

        if($newUser){
            return back()->with('success', 'Account generated successfully. Email: '.$generated_email.' Password: '.$generated_password);
        }else{
            return back()->with('error', 'Failed to generate account.');
        }

    }
    public function deleteUser(User $user)
    {
        if (Auth::user()->id == $user->id) {
            return back()->with('error', 'You cannot delete your own account from manage account. Please use delete my account feature.');
        }
    
        $user->status = 'deleted';
        $user->save();
    
        return back()->with('success', 'User deleted successfully.');
    }
    public function destroyMyAccount(Request $request)
    {
        $user = Auth::user();
    
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirmation' => 'required|same:confirmation_text',
            'confirmation_text' => 'required|in:delete-account-' . $user->email,
        ], [
            'confirmation.same' => 'Teks konfirmasi tidak sama',
            'confirmation_text.in' => 'Teks konfirmasi harus terisi.',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }
    
        $user->status = 'deleted';
        $user->save();
    
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->route('auth.login')->with('success', 'Your account has been deleted.');
    }
        

}
