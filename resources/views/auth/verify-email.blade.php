@extends('layouts.guest')

@section('title', 'Vérification Email - Document Digitizer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
            Vérifiez votre email
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Un lien de vérification a été envoyé à votre adresse email.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl rounded-lg sm:px-10 border border-gray-200">
            @if (session('resent'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">
                                Un nouveau lien de vérification a été envoyé à votre adresse email.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-blue-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>

                <p class="text-gray-600 mb-6">
                    Merci de vous être inscrit ! Avant de continuer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                </p>

                <p class="text-sm text-gray-500 mb-8">
                    Si vous n'avez pas reçu l'email, nous pouvons vous en envoyer un autre.
                </p>

                <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
                    @csrf
                    <button type="submit"
                            class="w-full flex justify-center rounded-lg bg-blue-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                        Renvoyer l'email de vérification
                    </button>
                </form>

                <div class="flex items-center justify-center space-x-4 text-sm">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-500 transition-colors">
                            Se déconnecter
                        </button>
                    </form>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-700 transition-colors">
                        Accéder au tableau de bord
                    </a>
                </div>
            </div>
        </div>

        <!-- Tips Section -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6 border border-blue-200">
            <h3 class="text-sm font-medium text-blue-900 mb-3">💡 Conseils</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start space-x-2">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Vérifiez votre dossier spam/courrier indésirable</span>
                </li>
                <li class="flex items-start space-x-2">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>L'email peut prendre quelques minutes à arriver</span>
                </li>
                <li class="flex items-start space-x-2">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Assurez-vous que l'adresse email est correcte</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
