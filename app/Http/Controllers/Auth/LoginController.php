<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return $this->authenticated($request, $user);
        }

        return redirect()->back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->roles == 'admin') {
            return redirect('/dashboard-admin');
        } elseif ($user->roles == 'petugas') {
            return redirect('/dashboard-petugas');
        } elseif ($user->roles == 'pasien') {
            return redirect('/dashboard-pasien');
        }
    }

    public function register(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('Register attempt with data:', $request->except(['password', 'password_confirmation']));
        
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'nama_user' => 'required|string|min:3|max:255',
            'username' => 'required|string|email|max:255|unique:user',  // Changed to 'user' table
            'password' => 'required|string|min:6|confirmed',
            'no_telepon' => 'required|string|min:10|max:13|regex:/^[0-9]+$/',
        ], [
            'nama_user.required' => 'Nama tidak boleh kosong',
            'nama_user.min' => 'Nama minimal 3 karakter',
            'username.required' => 'Email tidak boleh kosong',
            'username.email' => 'Format email tidak valid',
            'username.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'no_telepon.required' => 'Nomor telepon tidak boleh kosong',
            'no_telepon.min' => 'Nomor telepon minimal 10 digit',
            'no_telepon.max' => 'Nomor telepon maksimal 13 digit',
            'no_telepon.regex' => 'Nomor telepon hanya boleh angka',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            Log::error('Registration validation failed:', ['errors' => $validator->errors()->toArray()]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Create user
            $user = User::create([
                'nama_user' => $request->nama_user,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'no_telepon' => $request->no_telepon,
                'roles' => 'pasien', // Default role is pasien
            ]);

            DB::commit();
            
            Log::info('User successfully created with ID: ' . $user->id);

            // Redirect to login with success message
            return redirect()->route('login')
                ->with('success', 'Registrasi berhasil! Silakan login.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the exception for debugging
            Log::error('Error during registration:', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan pada sistem: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}