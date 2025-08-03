@extends('layouts.app')

@section('title', 'Mes Documents - Document Digitizer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Mes Documents
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Gérez vos documents numérisés et suivez leur progression
                </p>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0">
                <a href="{{ route('documents.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouveau document
                </a>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" id="search" placeholder="Nom du document..." class="input-field">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="status" class="input-field">
                    <option value="">Tous les statuts</option>
                    <option value="uploaded">Téléversé</option>
                    <option value="processing">En traitement</option>
                    <option value="completed">Terminé</option>
                    <option value="failed">Échoué</option>
                </select>
            </div>
            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="document_type" class="input-field">
                    <option value="">Tous les types</option>
                    <option value="recto">Recto</option>
                    <option value="recto_verso">Recto-Verso</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2 transition-colors">
                    Filtrer
                </button>
                <button type="button" class="text-sm text-indigo-600 hover:text-indigo-700 transition-colors">
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    @if($documents->count() > 0)
        <!-- Documents Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($documents as $document)
                <div class="card hover:shadow-lg transition-shadow duration-200">
                    <!-- Document Thumbnail -->
                    <div class="aspect-w-16 aspect-h-9 mb-4">
                        @if($document->thumbnail_path)
                            <img src="{{ $document->thumbnail_url }}" alt="{{ $document->title }}" class="w-full h-32 object-cover rounded-md bg-gray-100">
                        @else
                            <div class="w-full h-32 bg-gray-100 rounded-md flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            @php
                                // Utilisation de la même logique de statut que dans show.blade.php
                                $hasOcrText = !empty($document->ocr_text) && $document->ocr_text !== 'En attente de traitement...';
                                $hasAiAnalysis = !empty($document->ai_analysis) && $document->ai_analysis !== 'Analyse en cours...';

                                if ($hasOcrText && $hasAiAnalysis) {
                                    $actualStatus = 'completed';
                                    $statusText = 'Terminé';
                                    $statusColor = 'bg-emerald-100 text-emerald-800';
                                    $statusIcon = 'M5 13l4 4L19 7';
                                } elseif ($hasOcrText && !$hasAiAnalysis) {
                                    $actualStatus = 'processing';
                                    $statusText = 'Analyse IA';
                                    $statusColor = 'bg-amber-100 text-amber-800';
                                    $statusIcon = 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z';
                                } elseif (!$hasOcrText) {
                                    $actualStatus = 'processing';
                                    $statusText = 'Extraction';
                                    $statusColor = 'bg-blue-100 text-blue-800';
                                    $statusIcon = 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15';
                                } else {
                                    $actualStatus = 'uploaded';
                                    $statusText = 'Téléversé';
                                    $statusColor = 'bg-slate-100 text-slate-800';
                                    $statusIcon = 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12';
                                }
                            @endphp

                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                <svg class="w-3 h-3 mr-1 @if($actualStatus === 'processing') animate-spin @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcon }}"></path>
                                </svg>
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    <!-- Document Info -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1 truncate" title="{{ $document->title }}">
                            {{ $document->title }}
                        </h3>

                        <div class="space-y-1 text-sm text-gray-500 mb-4">
                            <div class="flex items-center justify-between">
                                <span>{{ $document->page_count }} page{{ $document->page_count > 1 ? 's' : '' }}</span>
                                <span class="font-medium text-gray-900">{{ number_format($document->processing_cost, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ ucfirst(str_replace('_', '-', $document->document_type)) }}</span>
                                <span>{{ number_format($document->file_size / 1024, 0) }} KB</span>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $document->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('documents.show', $document) }}"
                               class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-indigo-200 shadow-sm text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Voir
                            </a>

                            @php
                                $hasOcrText = !empty($document->ocr_text) && $document->ocr_text !== 'En attente de traitement...';
                                $hasAiAnalysis = !empty($document->ai_analysis) && $document->ai_analysis !== 'Analyse en cours...';
                                $isCompleted = $hasOcrText && $hasAiAnalysis;
                            @endphp

                            @if($isCompleted)
                                <a href="{{ route('documents.download', $document) }}"
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                   title="Télécharger le document traité">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $documents->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-6 text-xl font-medium text-gray-900">Aucun document trouvé</h3>
            <p class="mt-2 text-gray-500 max-w-md mx-auto">
                Vous n'avez pas encore téléversé de documents. Commencez par numériser votre premier document.
            </p>
            <div class="mt-8">
                <a href="{{ route('documents.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Téléverser mon premier document
                </a>
            </div>
        </div>
    @endif

    <!-- Quick Stats -->
    @if($documents->count() > 0)
        <div class="mt-12 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg p-6 border border-indigo-100">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques rapides</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $completedCount = $documents->filter(function($doc) {
                        $hasOcrText = !empty($doc->ocr_text) && $doc->ocr_text !== 'En attente de traitement...';
                        $hasAiAnalysis = !empty($doc->ai_analysis) && $doc->ai_analysis !== 'Analyse en cours...';
                        return $hasOcrText && $hasAiAnalysis;
                    })->count();

                    $processingCount = $documents->filter(function($doc) {
                        $hasOcrText = !empty($doc->ocr_text) && $doc->ocr_text !== 'En attente de traitement...';
                        $hasAiAnalysis = !empty($doc->ai_analysis) && $doc->ai_analysis !== 'Analyse en cours...';
                        return !($hasOcrText && $hasAiAnalysis);
                    })->count();
                @endphp

                <div class="text-center bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-emerald-600">{{ $completedCount }}</div>
                    <div class="text-sm text-gray-500">Terminés</div>
                </div>
                <div class="text-center bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-amber-600">{{ $processingCount }}</div>
                    <div class="text-sm text-gray-500">En traitement</div>
                </div>
                <div class="text-center bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-blue-600">{{ $documents->sum('page_count') }}</div>
                    <div class="text-sm text-gray-500">Pages totales</div>
                </div>
                <div class="text-center bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-indigo-600">{{ number_format($documents->sum('processing_cost'), 0) }}</div>
                    <div class="text-sm text-gray-500">FCFA dépensés</div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Live search functionality
        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('status');
        const typeFilter = document.getElementById('document_type');

        function filterDocuments() {
            const cards = document.querySelectorAll('.card');
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const typeValue = typeFilter.value;

            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const matchesSearch = !searchTerm || title.includes(searchTerm);
                const matchesStatus = !statusValue || card.dataset.status === statusValue;
                const matchesType = !typeValue || card.dataset.type === typeValue;

                if (matchesSearch && matchesStatus && matchesType) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterDocuments);
        statusFilter.addEventListener('change', filterDocuments);
        typeFilter.addEventListener('change', filterDocuments);

        // Auto-refresh processing documents
        function refreshProcessingDocuments() {
            const processingCards = document.querySelectorAll('[data-status="processing"]');
            if (processingCards.length > 0) {
                // In a real application, you would make AJAX calls here
                console.log('Refreshing processing documents...');
            }
        }

        setInterval(refreshProcessingDocuments, 10000); // Every 10 seconds
    });
</script>
@endpush
