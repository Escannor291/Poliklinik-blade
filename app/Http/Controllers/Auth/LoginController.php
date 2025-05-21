<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Datapasien;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
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
        
        return redirect('/');
    }

    /**
     * Register a new user account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validateRegister($request);

        $user = new \App\Models\User();
        $user->nama_user = $request->nama_user;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->no_telepon = $request->no_telepon;
        $user->roles = 'pasien';
        $user->save();

        // Create initial patient data
        $datapasien = new \App\Models\Datapasien();
        $datapasien->user_id = $user->id;
        $datapasien->nama_pasien = $user->nama_user;
        $datapasien->email = $user->username;
        $datapasien->no_telp = $user->no_telepon;
        $datapasien->save();
        
        // Log the user in automatically
        Auth::login($user);

        return redirect()->route('dashboard-pasien')->with('success', 'Registrasi berhasil! Selamat datang di Sistem Pendaftaran RS Fachri.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function validateRegister(Request $request)
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
    }
}