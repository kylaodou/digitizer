<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    public function home()
    {
        return view('welcome');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Log successful login
            AuditLog::logAction('user_login', Auth::user());

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Connexion réussie ! Bienvenue.');
        }

        // Log failed login attempt
        AuditLog::logAction('failed_login_attempt', null, null, [
            'email' => $request->email,
            'ip_address' => $request->ip(),
        ]);

        return back()
            ->withErrors(['email' => 'Les informations de connexion sont incorrectes.'])
            ->withInput($request->only('email'));
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'organization' => ['nullable', 'string', 'max:255'],
            'user_type' => ['required', 'in:individual,enterprise,public_service'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'organization' => $request->organization,
            'user_type' => $request->user_type,
        ]);

        // Log user registration
        AuditLog::logAction('user_registered', $user, null, [
            'user_type' => $request->user_type,
            'organization' => $request->organization,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Compte créé avec succès ! Bienvenue sur notre plateforme.');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        // Log logout
        AuditLog::logAction('user_logout', Auth::user());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Déconnexion réussie.');
    }

    /**
     * Show the dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get user statistics with correct logic
        $totalDocuments = $user->documents()->count();

        // Utiliser la même logique que dans les vues pour calculer les statuts
        $documents = $user->documents()->get();

        $completedDocuments = $documents->filter(function($doc) {
            $hasOcrText = !empty($doc->ocr_text) && $doc->ocr_text !== 'En attente de traitement...';
            $hasAiAnalysis = !empty($doc->ai_analysis) && $doc->ai_analysis !== 'Analyse en cours...';
            return $hasOcrText && $hasAiAnalysis;
        })->count();

        $processingDocuments = $documents->filter(function($doc) {
            $hasOcrText = !empty($doc->ocr_text) && $doc->ocr_text !== 'En attente de traitement...';
            $hasAiAnalysis = !empty($doc->ai_analysis) && $doc->ai_analysis !== 'Analyse en cours...';
            return !($hasOcrText && $hasAiAnalysis);
        })->count();

        $totalSpent = $user->invoices()->where('status', 'paid')->sum('total_amount');

        // Get recent documents
        $recentDocuments = $user->documents()
            ->latest()
            ->take(5)
            ->get();

        // Get recent invoices
        $recentInvoices = $user->invoices()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'totalDocuments',
            'completedDocuments',
            'processingDocuments',
            'totalSpent',
            'recentDocuments',
            'recentInvoices'
        ));
    }

    /**
     * Handle email verification.
     */
    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            return redirect()->route('login')->with('error', 'Utilisateur introuvable.');
        }

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Lien de vérification invalide.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('success', 'Email déjà vérifié.');
        }

        if ($user->markEmailAsVerified()) {
            // Log the email verification
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'email_verified',
                'description' => 'Email address verified',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Email vérifié avec succès !');
        }

        return redirect()->route('verification.notice')->with('error', 'Erreur lors de la vérification.');
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('success', 'Email déjà vérifié.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
