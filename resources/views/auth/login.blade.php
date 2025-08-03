@extends('layouts.app')

@section('title', 'Connexion - Document Digitizer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex">

    <!-- Left side - Branding and Information -->
    <div class="relative lg:flex flex-col justify-center px-8 lg:px-16 bg-gradient-to-br from-blue-600 to-blue-800 hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('assets/login.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/30 to-blue-800/30"></div>

        <!-- Content -->
        <div class="relative z-10">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-white">Document Digitizer</span>
            </div>

            <!-- Main heading -->
            <h1 class="text-4xl font-bold text-white mb-6 leading-tight">
                Rejoignez la révolution<br>
                <span class="text-blue-200">numérique burkinabè</span>
            </h1>

            <!-- Features -->
            <div class="space-y-6 mb-10">
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">Inscription gratuite</h3>
                        <p class="text-blue-100 leading-relaxed">Créez votre compte en moins de 2 minutes et accédez immédiatement à tous nos services de digitalisation.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">Sécurité maximale</h3>
                        <p class="text-blue-100 leading-relaxed">Vos documents sont protégés par un chiffrement de niveau bancaire et conformes aux normes burkinabè.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">Interface locale</h3>
                        <p class="text-blue-100 leading-relaxed">Conçu spécialement pour les besoins des entreprises et services publics du Burkina Faso.</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">500+</div>
                    <div class="text-blue-200 text-sm">Organisations inscrites</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">10K+</div>
                    <div class="text-blue-200 text-sm">Documents numérisés</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Right side - Login form -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <!-- Mobile logo -->
            <div class="lg:hidden flex justify-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>

            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Se connecter</h2>
                <p class="text-sm text-gray-600 mb-8">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                        Créez-en un gratuitement
                    </a>
                </p>
            </div>
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                            Adresse email
                        </label>
                        <div class="mt-2">
                            <input id="email"
                                   name="email"
                                   type="email"
                                   autocomplete="email"
                                   required
                                   value="{{ old('email') }}"
                                   class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('email') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="votre@email.com">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                            Mot de passe
                        </label>
                        <div class="mt-2">
                            <input id="password"
                                   name="password"
                                   type="password"
                                   autocomplete="current-password"
                                   required
                                   class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('password') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember me and Forgot password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me"
                                   name="remember"
                                   type="checkbox"
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                            <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="text-sm leading-6">
                            <a href="#" class="font-semibold text-blue-600 hover:text-blue-500 transition-colors">
                                Mot de passe oublié ?
                            </a>
                        </div>
                    </div>

                    <!-- Submit button -->
                    <div>
                        <button type="submit"
                                class="flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                            Se connecter
                        </button>
                    </div>
                </form>

                <!-- Additional features -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600 mb-6">En vous connectant, vous bénéficiez de :</p>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Numérisation illimitée avec OCR haute précision</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>Résumés IA automatiques powered by Mistral</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Classification automatique par type de document</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add some interactive animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate elements on page load
        const elements = document.querySelectorAll('.bg-white');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            setTimeout(() => {
                el.style.transition = 'all 0.6s ease-out';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 200);
        });
    });
</script>
@endpush
