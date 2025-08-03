@extends('layouts.app')

@section('title', 'Paramètres - Document Digitizer')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Paramètres
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Configurez vos préférences et paramètres de sécurité
        </p>
    </div>

    <div class="space-y-6">
        <!-- Préférences de notification -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Notifications</h3>
                <p class="mt-1 text-sm text-gray-500">Choisissez comment vous souhaitez être notifié</p>
                <div class="mt-6 space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="email-notifications" name="email-notifications" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" checked>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="email-notifications" class="font-medium text-gray-700">Notifications par email</label>
                            <p class="text-gray-500">Recevez des emails lorsque vos documents sont traités</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="sms-notifications" name="sms-notifications" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="sms-notifications" class="font-medium text-gray-700">Notifications SMS</label>
                            <p class="text-gray-500">Recevez des SMS pour les mises à jour importantes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sécurité -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Sécurité</h3>
                <p class="mt-1 text-sm text-gray-500">Gérez la sécurité de votre compte</p>
                <div class="mt-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Changer le mot de passe</h4>
                        <div class="mt-2 grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="current-password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                                <input type="password" name="current-password" id="current-password" class="mt-1 input-field">
                            </div>
                            <div>
                                <label for="new-password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                                <input type="password" name="new-password" id="new-password" class="mt-1 input-field">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="button" class="btn-primary">
                                Mettre à jour le mot de passe
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-medium text-gray-900">Authentification à deux facteurs</h4>
                        <p class="mt-1 text-sm text-gray-500">Ajoutez une couche de sécurité supplémentaire à votre compte</p>
                        <div class="mt-4">
                            <button type="button" class="btn-secondary">
                                Configurer 2FA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Préférences de numérisation -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Préférences de numérisation</h3>
                <p class="mt-1 text-sm text-gray-500">Configurez vos paramètres par défaut</p>
                <div class="mt-6 space-y-4">
                    <div>
                        <label for="default-document-type" class="block text-sm font-medium text-gray-700">Type de numérisation par défaut</label>
                        <select id="default-document-type" name="default-document-type" class="mt-1 input-field">
                            <option value="recto">Recto uniquement</option>
                            <option value="recto_verso">Recto-Verso</option>
                        </select>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="auto-categorize" name="auto-categorize" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" checked>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="auto-categorize" class="font-medium text-gray-700">Catégorisation automatique</label>
                            <p class="text-gray-500">Classez automatiquement vos documents par type</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="ai-summary" name="ai-summary" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" checked>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="ai-summary" class="font-medium text-gray-700">Résumé IA automatique</label>
                            <p class="text-gray-500">Générer automatiquement un résumé de vos documents</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="button" class="btn-primary">
                        Sauvegarder les préférences
                    </button>
                </div>
            </div>
        </div>

        <!-- Zone de danger -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-red-900">Zone de danger</h3>
                <p class="mt-1 text-sm text-red-600">Actions irréversibles sur votre compte</p>
                <div class="mt-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-red-900">Supprimer le compte</h4>
                        <p class="mt-1 text-sm text-red-600">Cette action supprimera définitivement votre compte et toutes vos données</p>
                        <div class="mt-4">
                            <button type="button" class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Supprimer le compte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
