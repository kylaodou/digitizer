@extends('layouts.app')

@section('title', 'Tableau de bord - Document Digitizer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Bonjour, {{ $user->name }} 👋
                </h1>
                <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                        @if($user->organization)
                            - {{ $user->organization }}
                        @endif
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Solde : {{ number_format($user->account_balance, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0">
                <span class="hidden sm:block">
                    <a href="{{ route('documents.create') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Voir mes documents
                    </a>
                </span>

                <span class="ml-3 sm:ml-0">
                    <a href="{{ route('documents.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nouveau document
                    </a>
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Documents -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Documents totaux</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalDocuments }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Documents -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-emerald-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Terminés</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $completedDocuments }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Processing Documents -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-amber-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">En traitement</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $processingDocuments }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Spent -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total dépensé</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($totalSpent, 0, ',', ' ') }} FCFA</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('documents.create') }}" class="relative group bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div>
                    <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-600 ring-4 ring-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </span>
                </div>
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">
                        <span class="absolute inset-0"></span>
                        Téléverser un document
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Numérisez vos documents avec extraction OCR automatique.
                    </p>
                </div>
                <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"/>
                    </svg>
                </span>
            </a>

            <a href="{{ route('invoices.index') }}" class="relative group bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div>
                    <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-600 ring-4 ring-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                </div>
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">
                        <span class="absolute inset-0"></span>
                        Voir mes factures
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Consultez l'historique de vos factures et paiements.
                    </p>
                </div>
                <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"/>
                    </svg>
                </span>
            </a>

            <div class="relative group bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div>
                    <span class="rounded-lg inline-flex p-3 bg-yellow-50 text-yellow-600 ring-4 ring-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </span>
                </div>
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">
                        Recharger le compte
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Ajoutez des fonds pour numériser plus de documents.
                    </p>
                    <button class="mt-4 btn-primary text-xs">Prochainement</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Recent Documents -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Documents récents</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentDocuments as $document)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @php
                                        // Utilisation de la même logique de statut que dans les autres vues
                                        $hasOcrText = !empty($document->ocr_text) && $document->ocr_text !== 'En attente de traitement...';
                                        $hasAiAnalysis = !empty($document->ai_analysis) && $document->ai_analysis !== 'Analyse en cours...';

                                        if ($hasOcrText && $hasAiAnalysis) {
                                            $actualStatus = 'completed';
                                            $statusColor = 'bg-emerald-100 text-emerald-600';
                                            $statusIcon = 'M5 13l4 4L19 7';
                                            $animate = '';
                                        } elseif ($hasOcrText && !$hasAiAnalysis) {
                                            $actualStatus = 'processing';
                                            $statusColor = 'bg-amber-100 text-amber-600';
                                            $statusIcon = 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z';
                                            $animate = 'animate-pulse';
                                        } elseif (!$hasOcrText) {
                                            $actualStatus = 'processing';
                                            $statusColor = 'bg-blue-100 text-blue-600';
                                            $statusIcon = 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15';
                                            $animate = 'animate-spin';
                                        } else {
                                            $actualStatus = 'uploaded';
                                            $statusColor = 'bg-slate-100 text-slate-600';
                                            $statusIcon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                            $animate = '';
                                        }
                                    @endphp

                                    <div class="w-8 h-8 {{ $statusColor }} rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 {{ $animate }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcon }}"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('documents.show', $document) }}" class="block">
                                        <p class="text-sm font-medium text-gray-900 truncate hover:text-blue-600 transition-colors">{{ $document->title }}</p>
                                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                                            <span>{{ $document->page_count }} page{{ $document->page_count > 1 ? 's' : '' }}</span>
                                            <span>•</span>
                                            <span>{{ number_format($document->processing_cost, 0, ',', ' ') }} FCFA</span>
                                            <span>•</span>
                                            @php
                                                // Déterminer le type de fichier
                                                $extension = pathinfo($document->original_filename, PATHINFO_EXTENSION);
                                                $mimeType = $document->mime_type;

                                                if (str_contains($mimeType, 'pdf')) {
                                                    $fileType = 'PDF';
                                                    $fileIcon = 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
                                                    $fileColor = 'text-red-600';
                                                } elseif (str_contains($mimeType, 'image')) {
                                                    $fileType = 'IMAGE';
                                                    $fileIcon = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
                                                    $fileColor = 'text-green-600';
                                                } elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation')) {
                                                    $fileType = 'PPT';
                                                    $fileIcon = 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
                                                    $fileColor = 'text-orange-600';
                                                } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
                                                    $fileType = 'DOC';
                                                    $fileIcon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                                    $fileColor = 'text-blue-600';
                                                } else {
                                                    $fileType = strtoupper($extension ?: 'FILE');
                                                    $fileIcon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                                    $fileColor = 'text-gray-600';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg class="w-3 h-3 mr-1 {{ $fileColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $fileIcon }}"></path>
                                                </svg>
                                                {{ $fileType }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-sm text-gray-500">
                                {{ $document->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun document</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par téléverser votre premier document.</p>
                        <div class="mt-6">
                            <a href="{{ route('documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Nouveau document
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
            @if($recentDocuments->count() > 0)
                <div class="px-6 py-3 bg-gray-50 text-center border-t border-gray-200">
                    <a href="{{ route('documents.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors">
                        Voir tous les documents →
                    </a>
                </div>
            @endif
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Factures récentes</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentInvoices as $invoice)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if($invoice->status === 'paid')
                                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @elseif($invoice->status === 'pending')
                                        <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors">{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $invoice->total_pages }} page{{ $invoice->total_pages > 1 ? 's' : '' }} •
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($invoice->status === 'paid')
                                                bg-emerald-100 text-emerald-800
                                            @elseif($invoice->status === 'pending')
                                                bg-amber-100 text-amber-800
                                            @else
                                                bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($invoice->status === 'paid' ? 'Payée' : ($invoice->status === 'pending' ? 'En attente' : 'Échouée')) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</p>
                                <p class="text-sm text-gray-500">{{ $invoice->issued_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune facture</h3>
                        <p class="mt-1 text-sm text-gray-500">Vos factures apparaîtront ici après traitement.</p>
                    </div>
                @endforelse
            </div>
            @if($recentInvoices->count() > 0)
                <div class="px-6 py-3 bg-gray-50 text-center border-t border-gray-200">
                    <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Voir toutes les factures →
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pricing Information -->
    <div class="mt-8 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-medium text-blue-900">Tarification transparente</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold">200 FCFA</span>
                            <span>par page recto</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold">150 FCFA</span>
                            <span>par page recto-verso</span>
                        </div>
                    </div>
                    <p class="mt-2 text-xs">Extraction OCR incluse • Stockage sécurisé • Factures PDF automatiques</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh processing documents status
    function refreshProcessingStatus() {
        const processingElements = document.querySelectorAll('[data-status="processing"]');
        if (processingElements.length > 0) {
            // In a real app, you would make AJAX calls to check status
            console.log('Checking processing status...');
        }
    }

    // Check every 30 seconds
    setInterval(refreshProcessingStatus, 30000);

    // Animate counters on page load
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('[data-counter]');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            let current = 0;
            const increment = target / 20;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 50);
        });
    });
</script>
@endpush

<!-- Inclure le modal universel -->
@include('components.document-modal')
