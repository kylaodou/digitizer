@extends('layouts.guest')

@section('title', 'Accueil - Document Digitizer')

@section('content')
<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900">Document Digitizer</span>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Fonctionnalités</a>
                <a href="#pricing" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Tarifs</a>
                <a href="#testimonials" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Témoignages</a>
                <a href="#contact" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Contact</a>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">
                    Se connecter
                </a>
                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    S'inscrire
                </a>

                <!-- Mobile menu button -->
                <button class="md:hidden p-2 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-gray-100" id="mobile-menu-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                <a href="#features" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Fonctionnalités</a>
                <a href="#pricing" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Tarifs</a>
                <a href="#testimonials" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Témoignages</a>
                <a href="#contact" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Contact</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="relative overflow-hidden from-blue-900 via-blue-800 to-blue-700 pt-16" style="background-image: url('assets/home.png'); background-size: cover;">
    <!-- Background patterns -->
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><path d="m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/></g></g></svg></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <!-- Logo avec animation -->
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-blue-600 rounded-3xl flex items-center justify-center shadow-2xl transform hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- Titre principal -->
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                Document <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-blue-400">Digitizer</span>
            </h1>

            <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto leading-relaxed">
                🇧🇫 <strong>Révolutionnez</strong> la numérisation de vos documents au Burkina Faso avec l'IA.
                <span class="text-blue-200">OCR automatique, classification intelligente et résumés IA.</span>
            </p>

            <!-- Badges de fonctionnalités -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <div class="bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 text-white border border-white/20">
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>IA Mistral Gratuite</span>
                    </span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 text-white border border-white/20">
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Sécurisé à 100%</span>
                    </span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 text-white border border-white/20">
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Traitement Instantané</span>
                    </span>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-16">
                <a href="{{ route('register') }}" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-900 bg-white rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:scale-105 transition-all duration-300">
                    <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity duration-300"></span>
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6z"></path>
                    </svg>
                    Commencer Gratuitement
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white/30 rounded-2xl hover:bg-white/10 backdrop-blur-sm transition-all duration-300">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Se Connecter
                </a>
            </div>

            <!-- Animation de défilement -->
            <div class="animate-bounce">
                <svg class="w-6 h-6 text-white/70 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Section Tarifs avec image -->
<div id="pricing" class="py-24 bg-gradient-to-br from-blue-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Tarification <span class="text-blue-600">Transparente</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Des prix justes et compétitifs pour tous vos besoins de numérisation
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Tarifs -->
            <div class="space-y-8">
                <div class="group bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border border-blue-100 hover:border-blue-300">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Recto Uniquement</h3>
                            <p class="text-gray-600">Numérisation d'un seul côté</p>
                        </div>
                    </div>
                    <div class="flex items-baseline space-x-2 mb-4">
                        <span class="text-5xl font-bold text-blue-600">200</span>
                        <span class="text-xl text-gray-500">FCFA</span>
                        <span class="text-sm text-gray-400">par page</span>
                    </div>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>OCR haute précision inclus</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Résumé IA automatique</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Classification intelligente</span>
                        </li>
                    </ul>
                </div>

                <div class="group bg-gradient-to-r from-blue-600 to-blue-700 rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 text-white">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Recto-Verso</h3>
                            <p class="text-blue-100">Numérisation des deux côtés</p>
                        </div>
                    </div>
                    <div class="flex items-baseline space-x-2 mb-4">
                        <span class="text-5xl font-bold">150</span>
                        <span class="text-xl text-blue-100">FCFA</span>
                        <span class="text-sm text-blue-200">par page</span>
                    </div>
                    <div class="bg-green-400 text-green-900 px-4 py-2 rounded-full text-sm font-semibold mb-4 inline-block">
                        💰 Économie de 25%
                    </div>
                    <ul class="space-y-3 text-blue-100">
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Tout inclus comme recto</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Traitement optimisé</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Idéal pour les contrats</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Image illustration -->
            <div class="relative">
                <div class="relative bg-gradient-to-br from-blue-100 to-blue-200 rounded-3xl p-8 overflow-hidden">
                    <!-- Décoration de fond -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-600/20"></div>

                    <!-- Illustration de documents -->
                    <div class="relative z-10 space-y-6">
                        <div class="flex justify-center space-x-4">
                            <div class="bg-white rounded-2xl p-6 shadow-xl transform rotate-6 hover:rotate-3 transition-transform duration-300">
                                <div class="w-24 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-center mt-3 font-medium text-gray-700">Facture</p>
                            </div>

                            <div class="bg-white rounded-2xl p-6 shadow-xl transform -rotate-6 hover:rotate-0 transition-transform duration-300">
                                <div class="w-24 h-32 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-center mt-3 font-medium text-gray-700">Contrat</p>
                            </div>
                        </div>

                        <!-- Flèche de processus -->
                        <div class="flex justify-center">
                            <div class="bg-blue-600 rounded-full p-4 shadow-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Résultat digitalisé -->
                        <div class="bg-white rounded-2xl p-6 shadow-xl">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">Document Numérisé</h4>
                                    <p class="text-sm text-gray-600">avec IA intégrée</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 bg-gray-200 rounded-full"></div>
                                <div class="h-2 bg-blue-200 rounded-full w-4/5"></div>
                                <div class="h-2 bg-gray-200 rounded-full w-3/5"></div>
                                <div class="flex items-center space-x-2 mt-4">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">✓ OCR</span>
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">🤖 IA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Fonctionnalités IA -->
<div id="features" style="background-image: url('assets/ia.jpg'); background-size: cover; background-position: center;" class="py-24 bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Intelligence Artificielle <span >Avancée</span>
            </h2>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Powered by Mistral AI - L'IA française de pointe pour analyser et comprendre vos documents
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Résumé automatique -->
            <div class="group bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-8 border border-gray-700 hover:border-blue-500 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/20">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Résumé Intelligent</h3>
                <p class="text-gray-300 mb-6">
                    L'IA analyse automatiquement vos documents et génère des résumés pertinents en quelques secondes.
                </p>
                <div class="bg-gray-800 rounded-2xl p-4">
                    <p class="text-sm text-gray-400 mb-2">Exemple de résumé :</p>
                    <p class="text-white text-sm italic">"Ce contrat de bail commercial fixe un loyer mensuel de 85 000 FCFA pour une durée de 3 ans, avec possibilité de renouvellement..."</p>
                </div>
            </div>

            <!-- Classification automatique -->
            <div class="group bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-8 border border-gray-700 hover:border-green-500 transition-all duration-300 hover:shadow-2xl hover:shadow-green-500/20">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Classification Automatique</h3>
                <p class="text-gray-300 mb-6">
                    Vos documents sont automatiquement triés par catégorie : factures, contrats, rapports, etc.
                </p>
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-300">📋 Dossier "Factures" créé automatiquement</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-300">📄 Dossier "Contrats" organisé</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span class="text-sm text-gray-300">📊 Dossier "Rapports" structuré</span>
                    </div>
                </div>
            </div>

            <!-- OCR Avancé -->
            <div class="group bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-8 border border-gray-700 hover:border-yellow-500 transition-all duration-300 hover:shadow-2xl hover:shadow-yellow-500/20">
                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">OCR Ultra-Précis</h3>
                <p class="text-gray-300 mb-6">
                    Extraction de texte avec une précision de 99.8%, même sur des documents de qualité variable.
                </p>
                <div class="bg-gray-800 rounded-2xl p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-green-400 text-sm font-medium">Traitement en cours...</span>
                    </div>
                    <div class="space-y-1">
                        <div class="h-2 bg-gray-700 rounded w-full"></div>
                        <div class="h-2 bg-yellow-400 rounded w-3/4"></div>
                        <div class="h-2 bg-gray-700 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Témoignages -->
<div id="testimonials" class="py-24 bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Ce que disent nos <span class="text-blue-600">clients</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Découvrez comment Document Digitizer transforme la gestion documentaire au Burkina Faso
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Témoignage 1 -->
            <div class="bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border border-blue-100">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">AM</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Aminata Ouédraogo</h4>
                        <p class="text-gray-600 text-sm">Directrice, PME Ouaga</p>
                    </div>
                </div>
                <div class="flex text-yellow-400 mb-4">
                    ⭐⭐⭐⭐⭐
                </div>
                <p class="text-gray-700 italic">
                    "Incredible ! En 2 minutes, tous mes contrats sont numérisés et classés automatiquement. L'IA résume parfaitement les points clés. Un gain de temps extraordinaire !"
                </p>
            </div>

            <!-- Témoignage 2 -->
            <div class="bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border border-green-100">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">IK</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Ibrahim Konaté</h4>
                        <p class="text-gray-600 text-sm">Comptable, Cabinet-Conseil</p>
                    </div>
                </div>
                <div class="flex text-yellow-400 mb-4">
                    ⭐⭐⭐⭐⭐
                </div>
                <p class="text-gray-700 italic">
                    "Plus besoin de trier manuellement les factures ! Le système crée automatiquement un dossier 'Factures' et classe tout. La précision OCR est impressionnante."
                </p>
            </div>

            <!-- Témoignage 3 -->
            <div class="bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border border-purple-100">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">FS</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Fatimata Sawadogo</h4>
                        <p class="text-gray-600 text-sm">Secrétaire Générale, Ministère</p>
                    </div>
                </div>
                <div class="flex text-yellow-400 mb-4">
                    ⭐⭐⭐⭐⭐
                </div>
                <p class="text-gray-700 italic">
                    "La sécurité et la rapidité sont au rendez-vous. Pour l'administration publique, c'est exactement ce qu'il nous fallait. Bravo à l'équipe !"
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Section CTA Final -->
<div class="py-24 bg-gradient-to-r from-blue-900 via-blue-800 to-purple-800 relative overflow-hidden">
    <!-- Patterns de fond -->
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=&quot;100&quot; height=&quot;100&quot; viewBox=&quot;0 0 100 100&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><defs><pattern id=&quot;grid&quot; width=&quot;10&quot; height=&quot;10&quot; patternUnits=&quot;userSpaceOnUse&quot;><path d=&quot;M 10 0 L 0 0 0 10&quot; fill=&quot;none&quot; stroke=&quot;%23ffffff&quot; stroke-width=&quot;0.5&quot; opacity=&quot;0.1&quot;/></pattern></defs><rect width=&quot;100&quot; height=&quot;100&quot; fill=&quot;url(%23grid)&quot;/></svg>');"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
            Prêt à <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-purple-300">révolutionner</span> votre gestion documentaire ?
        </h2>

        <p class="text-xl text-blue-100 mb-12 max-w-3xl mx-auto">
            Rejoignez des centaines d'entreprises burkinabés qui font confiance à Document Digitizer pour leurs besoins de numérisation intelligente.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-6 mb-12">
            <a href="{{ route('register') }}" class="group relative inline-flex items-center justify-center px-10 py-5 text-xl font-bold text-blue-900 bg-white rounded-2xl shadow-2xl hover:shadow-blue-400/25 transform hover:scale-105 transition-all duration-300">
                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity duration-300"></span>
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Commencer Maintenant
                <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium">GRATUIT</span>
            </a>

            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-10 py-5 text-xl font-semibold text-white border-2 border-white/30 rounded-2xl hover:bg-white/10 backdrop-blur-sm transition-all duration-300">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Voir une Démo
            </a>
        </div>

        <!-- Statistiques impressionnantes -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 pt-12 border-t border-white/20">
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">99.8%</div>
                <div class="text-blue-200 text-sm">Précision OCR</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">&lt;2s</div>
                <div class="text-blue-200 text-sm">Temps de traitement</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">500+</div>
                <div class="text-blue-200 text-sm">Entreprises clientes</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">24/7</div>
                <div class="text-blue-200 text-sm">Disponibilité</div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer id="contact" class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Logo et description -->
            <div class="md:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold">Document Digitizer</h3>
                </div>
                <p class="text-gray-300 max-w-md">
                    La solution de numérisation intelligente conçue spécialement pour les entreprises et administrations du Burkina Faso.
                </p>
                <div class="flex space-x-4 mt-6">
                    <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors cursor-pointer">
                        <span class="text-sm">📧</span>
                    </div>
                    <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors cursor-pointer">
                        <span class="text-sm">📱</span>
                    </div>
                    <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors cursor-pointer">
                        <span class="text-sm">🌐</span>
                    </div>
                </div>
            </div>

            <!-- Liens rapides -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Liens Rapides</h4>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="{{ route('login') }}" class="hover:text-blue-400 transition-colors">Se connecter</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-blue-400 transition-colors">S'inscrire</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Tarifs</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Support</h4>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Centre d'aide</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Documentation</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">API</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Statut système</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 text-center">
            <p class="text-gray-400">
                © 2025 Document Digitizer. Fièrement développé au Burkina Faso 🇧🇫 avec ❤️
            </p>
        </div>
    </div>
</footer>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Close mobile menu if open
                document.getElementById('mobile-menu').classList.add('hidden');
            }
        });
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('nav');
        if (window.scrollY > 100) {
            navbar.classList.add('bg-white/98');
            navbar.classList.remove('bg-white/95');
        } else {
            navbar.classList.add('bg-white/95');
            navbar.classList.remove('bg-white/98');
        }
    });
</script>
@endsection
