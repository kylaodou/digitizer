@extends('layouts.app')

@section('title', 'Inscription - Document Digitizer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 grid lg:grid-cols-2">
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

    <!-- Right side - Registration Form -->
    <div class="flex flex-col justify-center px-6 lg:px-16 py-12">
        <!-- Mobile header -->
        <div class="lg:hidden mb-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Créer votre compte</h2>
            <p class="text-gray-600">
                Ou
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    connectez-vous à votre compte existant
                </a>
            </p>
        </div>

        <!-- Desktop header -->
        <div class="hidden lg:block mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Créer votre compte</h2>
            <p class="text-gray-600">
                Ou
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    connectez-vous à votre compte existant
                </a>
            </p>
        </div>
        <!-- Form container -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                        Nom complet
                    </label>
                    <div class="mt-2">
                        <input id="name"
                               name="name"
                               type="text"
                               autocomplete="name"
                               required
                               value="{{ old('name') }}"
                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('name') ring-red-500 focus:ring-red-500 @enderror"
                               placeholder="Entrez votre nom complet">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                        Adresse e-mail
                    </label>
                    <div class="mt-2">
                        <input id="email"
                               name="email"
                               type="email"
                               autocomplete="email"
                               required
                               value="{{ old('email') }}"
                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('email') ring-red-500 focus:ring-red-500 @enderror"
                               placeholder="Entrez votre adresse e-mail">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">
                            Téléphone
                        </label>
                        <div class="mt-2">
                            <input id="phone"
                                   name="phone"
                                   type="tel"
                                   autocomplete="tel"
                                   value="{{ old('phone') }}"
                                   class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('phone') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="+226 XX XX XX XX">
                        </div>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="user_type" class="block text-sm font-medium leading-6 text-gray-900">
                            Type d'utilisateur
                        </label>
                        <div class="mt-2">
                            <select id="user_type"
                                    name="user_type"
                                    required
                                    class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('user_type') ring-red-500 focus:ring-red-500 @enderror">
                                <option value="">Sélectionner</option>
                                <option value="individual" {{ old('user_type') === 'individual' ? 'selected' : '' }}>Particulier</option>
                                <option value="enterprise" {{ old('user_type') === 'enterprise' ? 'selected' : '' }}>Entreprise</option>
                                <option value="public_service" {{ old('user_type') === 'public_service' ? 'selected' : '' }}>Service Public</option>
                            </select>
                        </div>
                        @error('user_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="organization" class="block text-sm font-medium leading-6 text-gray-900">
                        Organisation <span class="text-gray-400">(optionnel)</span>
                    </label>
                    <div class="mt-2">
                        <input id="organization"
                               name="organization"
                               type="text"
                               autocomplete="organization"
                               value="{{ old('organization') }}"
                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('organization') ring-red-500 focus:ring-red-500 @enderror"
                               placeholder="Nom de votre entreprise ou service">
                    </div>
                    @error('organization')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                        Mot de passe
                    </label>
                    <div class="mt-2">
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="new-password"
                               required
                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors @error('password') ring-red-500 focus:ring-red-500 @enderror"
                               placeholder="Créez un mot de passe sécurisé">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                        Confirmer le mot de passe
                    </label>
                    <div class="mt-2">
                        <input id="password_confirmation"
                               name="password_confirmation"
                               type="password"
                               autocomplete="new-password"
                               required
                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-colors"
                               placeholder="Confirmez votre mot de passe">
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms"
                               name="terms"
                               type="checkbox"
                               required
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="terms" class="text-gray-900">
                            J'accepte les
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                conditions d'utilisation
                            </a>
                            et la
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                politique de confidentialité
                            </a>
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                        Créer mon compte
                    </button>
                </div>
            </form>

            <!-- Additional benefits -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-center text-sm text-gray-600 mb-6">En créant votre compte, vous bénéficiez de :</p>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Accès immédiat à tous nos services</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Protection maximale de vos données</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 11-9.75 9.75M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"></path>
                        </svg>
                        <span>Support technique dédié</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');

        // Real-time password confirmation validation
        function validatePasswordMatch() {
            if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        }

        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);

        // Show/hide organization field based on user type
        const userTypeSelect = document.getElementById('user_type');
        const organizationField = document.getElementById('organization').parentElement;

        function toggleOrganizationField() {
            if (userTypeSelect.value === 'individual') {
                organizationField.style.opacity = '0.5';
                document.getElementById('organization').disabled = true;
            } else {
                organizationField.style.opacity = '1';
                document.getElementById('organization').disabled = false;
            }
        }

        userTypeSelect.addEventListener('change', toggleOrganizationField);
        toggleOrganizationField(); // Initial call
    });
</script>
@endpush
