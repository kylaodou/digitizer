@extends('layouts.app')

@section('title', 'Nouveau Document - Document Digitizer')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Nouveau Document
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Téléversez votre document pour le numériser avec extraction OCR automatique
                </p>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0">
                <a href="{{ route('documents.index') }}" class="btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Pricing Information -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 mb-8 border border-blue-200">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-medium text-blue-900">Tarifs de numérisation</h3>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-blue-700">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span><strong>200 FCFA</strong> par page recto</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span><strong>150 FCFA</strong> par page recto-verso</span>
                    </div>
                </div>
                <p class="mt-2 text-xs text-blue-600">
                    ✓ Extraction OCR incluse ✓ Stockage sécurisé ✓ Formats PDF, JPG, PNG acceptés
                </p>
            </div>
        </div>
    </div>

    <!-- Balance Check -->
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-sm font-medium text-gray-900">Solde actuel</h4>
                <p class="text-lg font-semibold text-gray-700">{{ number_format(Auth::user()->account_balance, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Coût estimé</div>
                <div id="estimated-cost" class="text-lg font-semibold text-blue-600">0 FCFA</div>
            </div>
        </div>
        <div id="balance-warning" class="hidden mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div class="text-sm text-red-800">
                    <strong>Solde insuffisant.</strong> Veuillez recharger votre compte ou réduire le nombre de pages.
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Document Title -->
            <div>
                <label for="title" class="block text-sm font-medium leading-6 text-gray-900">
                    Titre du document <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input type="text"
                           name="title"
                           id="title"
                           required
                           value="{{ old('title') }}"
                           class="input-field @error('title') ring-red-500 focus:ring-red-500 @enderror"
                           placeholder="Ex: Rapport mensuel, Contrat client, etc.">
                </div>
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Document Type -->
            <div>
                <label for="document_type" class="block text-sm font-medium leading-6 text-gray-900">
                    Type de numérisation <span class="text-red-500">*</span>
                </label>
                <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none @error('document_type') border-red-300 @else border-gray-300 @enderror">
                        <input type="radio"
                               name="document_type"
                               value="recto"
                               class="sr-only"
                               {{ old('document_type', 'recto') === 'recto' ? 'checked' : '' }}>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Recto uniquement</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500">200 FCFA par page</span>
                                <span class="mt-2 text-xs text-gray-400">Numérisation d'un seul côté</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-blue-600 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none @error('document_type') border-red-300 @else border-gray-300 @enderror">
                        <input type="radio"
                               name="document_type"
                               value="recto_verso"
                               class="sr-only"
                               {{ old('document_type') === 'recto_verso' ? 'checked' : '' }}>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Recto-Verso</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500">150 FCFA par page</span>
                                <span class="mt-2 text-xs text-gray-400">Numérisation des deux côtés</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-blue-600 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </label>
                </div>
                @error('document_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Page Count -->
            <div>
                <label for="page_count" class="block text-sm font-medium leading-6 text-gray-900">
                    Nombre de pages <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input type="number"
                           name="page_count"
                           id="page_count"
                           min="1"
                           max="100"
                           required
                           value="{{ old('page_count', 1) }}"
                           class="input-field @error('page_count') ring-red-500 focus:ring-red-500 @enderror"
                           placeholder="Nombre de pages à numériser">
                </div>
                <p class="mt-1 text-sm text-gray-500">Maximum 100 pages par document</p>
                @error('page_count')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div>
                <label for="file" class="block text-sm font-medium leading-6 text-gray-900">
                    Fichier à numériser <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <div class="flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10" id="file-drop-zone">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                <label for="file" class="relative cursor-pointer rounded-md bg-white font-semibold text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-600 focus-within:ring-offset-2 hover:text-blue-500">
                                    <span>Sélectionner un fichier</span>
                                    <input id="file"
                                           name="file"
                                           type="file"
                                           class="sr-only"
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           required>
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs leading-5 text-gray-600">PDF, PNG, JPG jusqu'à 10MB</p>
                        </div>
                    </div>
                </div>
                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- File Preview -->
                <div id="file-preview" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p id="file-name" class="text-sm font-medium text-gray-900 truncate"></p>
                            <p id="file-size" class="text-sm text-gray-500"></p>
                        </div>
                        <button type="button" id="remove-file" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            @error('balance')
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreur de traitement</h3>
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            @enderror

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('documents.index') }}" class="btn-secondary">
                    Annuler
                </a>
                <button type="submit"
                        id="submit-btn"
                        class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Téléverser et numériser
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-8 bg-blue-50 rounded-lg p-6 border border-blue-200">
        <h3 class="text-lg font-medium text-blue-900 mb-4">💡 Conseils pour une meilleure numérisation</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
            <div class="space-y-2">
                <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Utilisez une résolution d'au moins 300 DPI pour une meilleure qualité OCR</span>
                </div>
                <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Assurez-vous que le texte est lisible et non flou</span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Évitez les ombres et reflets sur le document</span>
                </div>
                <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Les formats PDF donnent généralement les meilleurs résultats</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file');
        const fileDropZone = document.getElementById('file-drop-zone');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const removeFileBtn = document.getElementById('remove-file');
        const submitBtn = document.getElementById('submit-btn');
        const pageCountInput = document.getElementById('page_count');
        const documentTypeInputs = document.querySelectorAll('input[name="document_type"]');
        const estimatedCostElement = document.getElementById('estimated-cost');
        const balanceWarning = document.getElementById('balance-warning');
        const userBalance = {{ Auth::user()->account_balance }};

        // File handling
        function handleFileSelect(file) {
            if (file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.classList.remove('hidden');
                fileDropZone.classList.add('hidden');
                updateSubmitButton();
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
            fileDropZone.classList.remove('hidden');
            updateSubmitButton();
        });

        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            fileDropZone.classList.add('border-blue-500', 'bg-blue-50');
        }

        function unhighlight(e) {
            fileDropZone.classList.remove('border-blue-500', 'bg-blue-50');
        }

        fileDropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        }

        // Cost calculation
        function calculateCost() {
            const pageCount = parseInt(pageCountInput.value) || 0;
            const documentType = document.querySelector('input[name="document_type"]:checked')?.value || 'recto';
            const ratePerPage = documentType === 'recto_verso' ? 150 : 200;
            const totalCost = pageCount * ratePerPage;

            estimatedCostElement.textContent = new Intl.NumberFormat('fr-FR').format(totalCost) + ' FCFA';

            // Check balance
            if (totalCost > userBalance) {
                balanceWarning.classList.remove('hidden');
            } else {
                balanceWarning.classList.add('hidden');
            }

            updateSubmitButton();
            return totalCost;
        }

        // Update submit button state
        function updateSubmitButton() {
            const hasFile = fileInput.files.length > 0;
            const hasValidPageCount = parseInt(pageCountInput.value) > 0;
            const pageCount = parseInt(pageCountInput.value) || 0;
            const documentType = document.querySelector('input[name="document_type"]:checked')?.value || 'recto';
            const ratePerPage = documentType === 'recto_verso' ? 150 : 200;
            const totalCost = pageCount * ratePerPage;
            const hasSufficientBalance = totalCost <= userBalance;

            if (hasFile && hasValidPageCount && hasSufficientBalance) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Event listeners for cost calculation
        pageCountInput.addEventListener('input', calculateCost);
        documentTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                calculateCost();
                updateDocumentTypeUI();
            });
        });

        // Update document type UI
        function updateDocumentTypeUI() {
            documentTypeInputs.forEach(radio => {
                const label = radio.closest('label');
                const checkIcon = label.querySelector('svg');
                if (radio.checked) {
                    label.classList.add('border-blue-600', 'ring-2', 'ring-blue-600');
                    checkIcon.classList.remove('hidden');
                } else {
                    label.classList.remove('border-blue-600', 'ring-2', 'ring-blue-600');
                    checkIcon.classList.add('hidden');
                }
            });
        }

        // Form submission handling
        document.querySelector('form').addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Traitement en cours...
            `;
        });

        // Initialize
        calculateCost();
        updateDocumentTypeUI();
    });
</script>
@endpush
