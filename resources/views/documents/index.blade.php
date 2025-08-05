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
                    <div class="aspect-w-16 aspect-h-9 mb-4 relative">
                        @php
                            // Utiliser mime_type et file_path pour déterminer le type de fichier
                            $mimeType = $document->mime_type ?? '';
                            $fileExtension = $document->file_path ? strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)) : '';
                            
                            // Détection améliorée des types de fichiers
                            $isImage = str_contains($mimeType, 'image/') || in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'svg']);
                            $isPdf = str_contains($mimeType, 'pdf') || $fileExtension === 'pdf';
                            
                            // Détection complète des documents Office avec MIME types spécifiques
                            $isOffice = 
                                // MIME types spécifiques Microsoft Office
                                str_contains($mimeType, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') ||
                                str_contains($mimeType, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') ||
                                str_contains($mimeType, 'application/vnd.openxmlformats-officedocument.presentationml.presentation') ||
                                str_contains($mimeType, 'application/msword') ||
                                str_contains($mimeType, 'application/vnd.ms-excel') ||
                                str_contains($mimeType, 'application/vnd.ms-powerpoint') ||
                                // MIME types génériques
                                str_contains($mimeType, 'word') || str_contains($mimeType, 'excel') || str_contains($mimeType, 'powerpoint') ||
                                str_contains($mimeType, 'document') || str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'presentation') ||
                                str_contains($mimeType, 'officedocument') || str_contains($mimeType, 'opendocument') ||
                                // Extensions fichier
                                in_array($fileExtension, ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'rtf', 'xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm', 'xlam', 'ppt', 'pptx', 'pptm', 'pot', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'pps', 'odt', 'ods', 'odp']);
                            
                            $isText = str_contains($mimeType, 'text/') || in_array($fileExtension, ['txt', 'csv', 'json', 'xml']);

                            // Logique pour afficher les prévisualisations RÉELLES
                            $hasRealPreview = false;
                            $previewUrl = '';

                            // 1. Priorité au thumbnail généré s'il existe
                            if ($document->thumbnail_path && file_exists(public_path('storage/' . $document->thumbnail_path))) {
                                $hasRealPreview = true;
                                $previewUrl = asset('storage/' . $document->thumbnail_path);
                            }
                            // 2. Pour les images, utiliser le fichier original
                            elseif ($isImage && $document->file_path && file_exists(public_path('storage/' . $document->file_path))) {
                                $hasRealPreview = true;
                                $previewUrl = asset('storage/' . $document->file_path);
                            }
                            // 3. Pour les PDF, essayer de créer un aperçu à la volée (si possible)
                            elseif ($isPdf && $document->file_path && file_exists(public_path('storage/' . $document->file_path))) {
                                // Vérifier s'il y a un thumbnail PDF généré
                                $pdfThumbnailPath = 'thumbnails/' . pathinfo($document->file_path, PATHINFO_FILENAME) . '_thumb.jpg';
                                if (file_exists(public_path('storage/' . $pdfThumbnailPath))) {
                                    $hasRealPreview = true;
                                    $previewUrl = asset('storage/' . $pdfThumbnailPath);
                                }
                            }
                        @endphp

                        <!-- APERÇU UNIVERSEL POUR TOUS LES TYPES -->
                        @if($hasRealPreview)
                            <!-- Véritable document ouvert (IDENTIQUE À LA VUE SHOW) -->
                            @if($isPdf)
                                <!-- Document PDF réel ouvert -->
                                <div class="w-full h-32 rounded-md overflow-hidden bg-white border-2 border-red-200 cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                    <iframe src="{{ asset('storage/' . $document->file_path) }}#toolbar=0&navpanes=0&scrollbar=0"
                                            class="w-full h-full border-none"
                                            style="border: none; transform: scale(0.6); transform-origin: top left; width: 167%; height: 167%;"
                                            title="Aperçu PDF">
                                    </iframe>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                        <span class="text-white text-xs font-medium">📄 PDF</span>
                                    </div>
                                </div>
                            @elseif($isOffice)
                                <!-- Document Office réel ouvert -->
                                <div class="w-full h-32 rounded-md overflow-hidden border-2 border-blue-300 bg-white cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                    @if(isset($document->ocr_text) && $document->ocr_text && !str_contains($document->ocr_text, 'ANALYSE BASIQUE'))
                                        <!-- Contenu réel du document Office -->
                                        <div class="w-full h-full bg-white p-2 overflow-hidden">
                                            <div class="text-xs text-gray-700 leading-tight overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 8; -webkit-box-orient: vertical; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                                {{ strip_tags($document->ocr_text) }}
                                            </div>
                                        </div>
                                    @else
                                        <!-- Document Office en attente -->
                                        <div class="w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                            @if(str_contains($mimeType, 'wordprocessingml') || str_contains($mimeType, 'msword') || in_array($fileExtension, ['doc', 'docx']))
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-blue-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5zm2 3h8v1H6V8zm0 2h8v1H6v-1zm0 2h5v1H6v-1z"/>
                                                    </svg>
                                                    <div class="text-xs font-medium text-blue-700">Word</div>
                                                </div>
                                            @elseif(str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'ms-excel') || in_array($fileExtension, ['xls', 'xlsx']))
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-green-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                                    </svg>
                                                    <div class="text-xs font-medium text-green-700">Excel</div>
                                                </div>
                                            @elseif(str_contains($mimeType, 'presentationml') || str_contains($mimeType, 'ms-powerpoint') || in_array($fileExtension, ['ppt', 'pptx', 'pptm']))
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-orange-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                    </svg>
                                                    <div class="text-xs font-medium text-orange-700">PowerPoint</div>
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-blue-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                    </svg>
                                                    <div class="text-xs font-medium text-blue-700">Office</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                        <span class="text-white text-xs font-medium">📊 OFFICE</span>
                                    </div>
                                </div>
                            @else
                                <!-- Image réelle -->
                                <div class="w-full h-32 rounded-md overflow-hidden bg-gray-100 border-2 border-green-200 cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                    <img src="{{ $previewUrl }}" alt="{{ $document->title }}" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                        <span class="text-white text-xs font-medium">🖼️ IMAGE</span>
                                    </div>
                                </div>
                            @endif
                        @elseif($isImage && $document->file_path && file_exists(public_path('storage/' . $document->file_path)))
                            <!-- Image directe si pas de thumbnail -->
                            <div class="w-full h-32 rounded-md overflow-hidden bg-gray-100 border-2 border-green-200 cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                <img src="{{ asset('storage/' . $document->file_path) }}" alt="{{ $document->title }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                    <span class="text-white text-xs font-medium">🖼️ IMAGE</span>
                                </div>
                            </div>
                        @elseif($isPdf && $document->file_path && file_exists(public_path('storage/' . $document->file_path)))
                            <!-- Aperçu PDF de haute qualité -->
                            <div class="w-full h-32 rounded-md overflow-hidden bg-white border-2 border-red-200 cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                <iframe src="{{ asset('storage/' . $document->file_path) }}#toolbar=0&navpanes=0&scrollbar=0"
                                        class="w-full h-full border-none"
                                        style="border: none; transform: scale(0.8); transform-origin: top left; width: 125%; height: 125%;"
                                        title="Aperçu PDF">
                                </iframe>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                    <span class="text-white text-xs font-medium">📄 PDF</span>
                                </div>
                            </div>
                        @elseif($isOffice)
                            <!-- Document Office réel ouvert en prévisualisation -->
                            <div class="w-full h-32 rounded-md overflow-hidden border-2 border-blue-300 bg-white cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                @if(isset($document->ocr_text) && $document->ocr_text && !str_contains($document->ocr_text, 'ANALYSE BASIQUE'))
                                    <!-- Affichage du contenu réel du document -->
                                    <div class="w-full h-full bg-white p-2 flex flex-col">
                                        <!-- Header avec type de document -->
                                        <div class="flex items-center mb-1">
                                            @if(str_contains($mimeType, 'wordprocessingml') || str_contains($mimeType, 'msword') || in_array($fileExtension, ['doc', 'docx']))
                                                <svg class="w-4 h-4 text-blue-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5zm2 3h8v1H6V8zm0 2h8v1H6v-1zm0 2h5v1H6v-1z"/>
                                                </svg>
                                                <span class="text-xs font-medium text-blue-700">Word</span>
                                            @elseif(str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'ms-excel') || in_array($fileExtension, ['xls', 'xlsx']))
                                                <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                                </svg>
                                                <span class="text-xs font-medium text-green-700">Excel</span>
                                            @elseif(str_contains($mimeType, 'presentationml') || str_contains($mimeType, 'ms-powerpoint') || in_array($fileExtension, ['ppt', 'pptx', 'pptm']))
                                                <svg class="w-4 h-4 text-orange-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <span class="text-xs font-medium text-orange-700">PowerPoint</span>
                                            @else
                                                <svg class="w-4 h-4 text-blue-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <span class="text-xs font-medium text-blue-700">Office</span>
                                            @endif
                                        </div>
                                        
                                        <!-- Contenu réel du document -->
                                        <div class="flex-1 overflow-hidden bg-gray-50 rounded p-1 border">
                                            <div class="text-xs text-gray-700 leading-tight overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical;">
                                                {{ strip_tags($document->ocr_text) }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Aperçu stylisé pour Office sans contenu -->
                                    <div class="w-full h-full relative bg-white/90 border border-gray-200 p-2">
                                        <div class="text-center mb-2">
                                            @if(str_contains($mimeType, 'wordprocessingml') || str_contains($mimeType, 'msword') || in_array($fileExtension, ['doc', 'docx']))
                                                <svg class="w-8 h-8 text-blue-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 2 0 00-2-2H4zm0 2h12v10H4V5zm2 3h8v1H6V8zm0 2h8v1H6v-1zm0 2h5v1H6v-1z"/>
                                                </svg>
                                                <div class="text-xs font-medium text-blue-700">Document Word</div>
                                            @elseif(str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'ms-excel') || in_array($fileExtension, ['xls', 'xlsx']))
                                                <svg class="w-8 h-8 text-green-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                                </svg>
                                                <div class="text-xs font-medium text-green-700">Feuille Excel</div>
                                            @elseif(str_contains($mimeType, 'presentationml') || str_contains($mimeType, 'ms-powerpoint') || in_array($fileExtension, ['ppt', 'pptx', 'pptm']))
                                                <svg class="w-8 h-8 text-orange-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <div class="text-xs font-medium text-orange-700">Présentation PowerPoint</div>
                                            @else
                                                <svg class="w-8 h-8 text-blue-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <div class="text-xs font-medium text-blue-700">Document Office</div>
                                            @endif
                                        </div>

                                        <div class="text-xs text-gray-500 text-center">En cours de traitement...</div>
                                    </div>
                                @endif
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                    <span class="text-white text-xs font-medium">📊 {{ strtoupper($fileExtension) }}</span>
                                </div>
                            </div>
                        @elseif($isText && $document->file_path && file_exists(public_path('storage/' . $document->file_path)))
                            <!-- Aperçu texte avec contenu réel -->
                            <div class="w-full h-32 rounded-md overflow-hidden border-2 border-gray-300 bg-white cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                <div class="p-2 h-full overflow-hidden">
                                    <div class="flex items-center mb-1">
                                        <svg class="w-4 h-4 text-gray-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="text-xs font-medium text-gray-700">Fichier Texte</div>
                                    </div>
                                    @php
                                        $fileContent = file_get_contents(public_path('storage/' . $document->file_path));
                                        $preview = substr($fileContent, 0, 200);
                                    @endphp
                                    <div class="text-xs text-gray-600 font-mono leading-tight whitespace-pre-wrap overflow-hidden">{{ $preview }}{{ strlen($fileContent) > 200 ? '...' : '' }}</div>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                    <span class="text-white text-xs font-medium">📝 {{ strtoupper($fileExtension) }}</span>
                                </div>
                            </div>
                        @else
                            <!-- Aperçu générique pour autres types de fichiers -->
                            <div class="w-full h-32 rounded-md overflow-hidden border-2 border-purple-300 bg-gradient-to-br from-purple-50 to-purple-100 cursor-pointer hover:scale-105 transition-transform relative" onclick="openDocumentModal({{ $document->id }})">
                                <div class="w-full h-full relative bg-white/90 border border-gray-200 flex flex-col items-center justify-center p-4">
                                    <div class="text-center">
                                        <div class="text-3xl mb-2">
                                            @if(str_contains($mimeType, 'video') || in_array($fileExtension, ['mp4', 'avi', 'mov', 'wmv']))
                                                🎥
                                            @elseif(str_contains($mimeType, 'audio') || in_array($fileExtension, ['mp3', 'wav', 'flac', 'ogg']))
                                                🎵
                                            @elseif(str_contains($mimeType, 'archive') || in_array($fileExtension, ['zip', 'rar', '7z', 'tar']))
                                                📦
                                            @else
                                                📄
                                            @endif
                                        </div>
                                        <div class="text-sm font-semibold text-purple-700 mb-1">Fichier {{ strtoupper($fileExtension) }}</div>
                                        <div class="text-xs text-purple-600">{{ $mimeType ?: 'Type non détecté' }}</div>

                                        <!-- Aperçu du contenu OCR si disponible -->
                                        @if(!empty($document->ocr_text) && $document->ocr_text !== 'En attente de traitement...')
                                            <div class="mt-2 bg-white/80 rounded p-1 text-xs text-gray-700 leading-tight max-w-full overflow-hidden">
                                                <div class="font-medium mb-1">Contenu :</div>
                                                <div class="text-xs">{{ Str::limit(strip_tags($document->ocr_text), 60) }}</div>
                                            </div>
                                        @else
                                            <div class="mt-2 text-xs text-gray-500">En attente de traitement...</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                    <span class="text-white text-xs font-medium">📎 {{ strtoupper($fileExtension) }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Document Type Badge -->
                        <div class="absolute top-2 right-2">
                            @php
                                // Utiliser le type de document du modèle au lieu de l'extension
                                $documentTypeText = '';
                                $documentTypeColor = '';

                                // Prioriser ai_document_type s'il existe, sinon utiliser mime_type puis extension
                                if (!empty($document->ai_document_type)) {
                                    $documentTypeText = strtoupper($document->ai_document_type);
                                    $documentTypeColor = 'bg-purple-100 text-purple-800';
                                } elseif (!empty($document->mime_type)) {
                                    // Extraire le type principal du MIME type
                                    $mimeType = $document->mime_type;
                                    if (str_contains($mimeType, 'pdf')) {
                                        $documentTypeText = 'PDF';
                                        $documentTypeColor = 'bg-red-100 text-red-800';
                                    } elseif (str_contains($mimeType, 'image')) {
                                        $documentTypeText = 'IMAGE';
                                        $documentTypeColor = 'bg-green-100 text-green-800';
                                    } elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation')) {
                                        $documentTypeText = 'PPT';
                                        $documentTypeColor = 'bg-orange-100 text-orange-800';
                                    } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
                                        $documentTypeText = 'WORD';
                                        $documentTypeColor = 'bg-blue-100 text-blue-800';
                                    } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) {
                                        $documentTypeText = 'EXCEL';
                                        $documentTypeColor = 'bg-emerald-100 text-emerald-800';
                                    } elseif (str_contains($mimeType, 'text')) {
                                        $documentTypeText = 'TEXTE';
                                        $documentTypeColor = 'bg-gray-100 text-gray-800';
                                    } else {
                                        $documentTypeText = strtoupper(explode('/', $mimeType)[0]);
                                        $documentTypeColor = 'bg-purple-100 text-purple-800';
                                    }
                                } else {
                                    // Fallback sur l'extension de fichier
                                    if ($isPdf) {
                                        $documentTypeText = 'PDF';
                                        $documentTypeColor = 'bg-red-100 text-red-800';
                                    } elseif ($isImage) {
                                        $documentTypeText = strtoupper($fileExtension);
                                        $documentTypeColor = 'bg-green-100 text-green-800';
                                    } elseif (in_array($fileExtension, ['ppt', 'pptx', 'pptm'])) {
                                        $documentTypeText = 'PPT';
                                        $documentTypeColor = 'bg-orange-100 text-orange-800';
                                    } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                        $documentTypeText = 'WORD';
                                        $documentTypeColor = 'bg-blue-100 text-blue-800';
                                    } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                        $documentTypeText = 'EXCEL';
                                        $documentTypeColor = 'bg-emerald-100 text-emerald-800';
                                    } elseif ($isText) {
                                        $documentTypeText = 'TEXTE';
                                        $documentTypeColor = 'bg-gray-100 text-gray-800';
                                    } else {
                                        $documentTypeText = strtoupper($fileExtension);
                                        $documentTypeColor = 'bg-purple-100 text-purple-800';
                                    }
                                }
                            @endphp

                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $documentTypeColor }}">
                                {{ $documentTypeText }}
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
                            <button onclick="openDocumentModal({{ $document->id }})"
                               class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-indigo-200 shadow-sm text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Voir
                            </button>

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

        // Fonctions utilitaires
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleString('fr-FR');
        }

        function getFileType(extension) {
            const types = {
                pdf: 'application/pdf',
                doc: 'application/msword',
                docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                xls: 'application/vnd.ms-excel',
                xlsx: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ppt: 'application/vnd.ms-powerpoint',
                pptx: 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                txt: 'text/plain',
                jpg: 'image/jpeg',
                jpeg: 'image/jpeg',
                png: 'image/png'
            };
            return types[extension] || 'application/octet-stream';
        }
    });

    // Fonction pour ouvrir la page du document
    function openDocumentModal(documentId) {
        // Rediriger vers la page de détail du document
        window.location.href = `/documents/${documentId}`;
    }

    // Fonction pour formater la taille des fichiers (accessible globalement)
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Fonction pour formater les dates (accessible globalement)
    function formatDate(dateString) {
        return new Date(dateString).toLocaleString('fr-FR');
    }
</script>
@endpush

<!-- Inclure le modal universel -->
@include('components.pdf-modal')
