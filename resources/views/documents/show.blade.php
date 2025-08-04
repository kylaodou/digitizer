@extends('layouts.app')

@section('title', 'Détails du document - Document Digitizer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $document->name ?? 'Document' }}</h1>
                        <p class="text-gray-600 mt-1">
                            Ajouté le {{ isset($document->created_at) ? $document->created_at->format('d/m/Y à H:i') : 'Date inconnue' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ isset($document->status) && $document->status === 'processed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ isset($document->status) && $document->status === 'processed' ? 'Traité' : 'En cours' }}
                    </span>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Télécharger
                        </button>
                        <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Document Viewer -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Aperçu du document</h2>

                    <!-- Document Preview Area -->
                    <div class="bg-gray-50 rounded-xl p-8 text-center min-h-96 flex items-center justify-center">
                        @if(isset($document->file_path))
                            @php
                                $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                $mimeType = $document->mime_type ?? '';
                                $isImage = str_contains($mimeType, 'image/') || in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'svg']);
                                $isPdf = str_contains($mimeType, 'pdf') || strtolower($extension) === 'pdf';

                                // Détection complète des formats Office et bureautiques
                                $officeExtensions = [
                                    // Microsoft Word
                                    'doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'rtf',
                                    // Microsoft Excel
                                    'xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm', 'xlam', 'csv',
                                    // Microsoft PowerPoint
                                    'ppt', 'pptx', 'pptm', 'pot', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'pps',
                                    // Microsoft Access
                                    'mdb', 'accdb', 'accde', 'accdt', 'accdr',
                                    // Microsoft Visio
                                    'vsd', 'vsdx', 'vsdm', 'vss', 'vssx', 'vssm', 'vst', 'vstx', 'vstm',
                                    // Microsoft Project
                                    'mpp', 'mpt',
                                    // LibreOffice/OpenOffice
                                    'odt', 'ott', 'ods', 'ots', 'odp', 'otp', 'odg', 'otg', 'odf', 'odb', 'odc', 'odm',
                                    // Autres formats bureautiques
                                    'pages', 'numbers', 'key', 'epub', 'mobi'
                                ];

                                $officeMimeTypes = [
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.ms-word.document.macroEnabled.12',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-excel.sheet.macroEnabled.12',
                                    'application/vnd.ms-powerpoint',
                                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                    'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
                                    'application/vnd.oasis.opendocument.text',
                                    'application/vnd.oasis.opendocument.spreadsheet',
                                    'application/vnd.oasis.opendocument.presentation',
                                    'application/rtf'
                                ];

                                $isOffice = in_array(strtolower($extension), $officeExtensions) ||
                                           str_contains($mimeType, 'word') || str_contains($mimeType, 'excel') || str_contains($mimeType, 'powerpoint') ||
                                           str_contains($mimeType, 'document') || str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'presentation') ||
                                           str_contains($mimeType, 'opendocument') || str_contains($mimeType, 'officedocument') ||
                                           collect($officeMimeTypes)->contains(fn($mime) => str_contains($mimeType, $mime));

                                $isText = str_contains($mimeType, 'text/') || in_array(strtolower($extension), ['txt', 'csv', 'json', 'xml', 'md', 'log', 'ini', 'cfg', 'conf']);

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
                                <!-- Véritable prévisualisation avec image -->
                                <div class="max-w-full max-h-96">
                                    <img src="{{ $previewUrl }}" alt="Aperçu du document" class="max-w-full max-h-96 rounded-lg shadow-md border">
                                    <div class="mt-4 text-sm text-gray-600">
                                        @if($isPdf)
                                            📄 Aperçu de la première page (PDF)
                                        @elseif($isOffice)
                                            📊 Aperçu généré (Document Office)
                                        @elseif($isImage)
                                            🖼️ Image originale
                                        @elseif($isText)
                                            📝 Aperçu du contenu textuel
                                        @else
                                            📎 Aperçu du fichier {{ strtoupper($extension) }}
                                        @endif
                                    </div>
                                </div>
                            @elseif($isImage)
                                <!-- Image directe si pas de thumbnail -->
                                <img src="{{ asset('storage/' . $document->file_path) }}"
                                     alt="Document"
                                     class="max-w-full max-h-96 rounded-lg shadow-md">
                            @elseif($isPdf)
                                <!-- Aperçu PDF avec iframe -->
                                <div class="w-full max-w-2xl">
                                    <div class="bg-white rounded-lg shadow-lg p-4 mb-4">
                                        <iframe
                                            src="{{ asset('storage/' . $document->file_path) }}#toolbar=0&navpanes=0&scrollbar=0"
                                            class="w-full h-96 rounded border"
                                            style="border: none;"
                                            title="Aperçu PDF">
                                        </iframe>
                                    </div>

                                    <div class="flex justify-center space-x-3">
                                        <button
                                            onclick="openPdfModal('{{ asset('storage/' . $document->file_path) }}', {{ json_encode([
                                                'filename' => $document->name ?? 'document.pdf',
                                                'file_size' => isset($document->file_size) ? number_format($document->file_size / 1024, 2) . ' KB' : 'N/A',
                                                'mime_type' => $document->mime_type ?? 'application/pdf',
                                                'created_at' => isset($document->created_at) ? $document->created_at->format('d/m/Y à H:i') : 'N/A',
                                                'status' => $document->status ?? 'uploaded',
                                                'ocr_text' => $document->ocr_text ?? null,
                                                'ai_summary' => $document->ai_summary ?? null,
                                                'ai_classification' => $document->ai_classification ?? null
                                            ]) }})"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Aperçu complet
                                        </button>

                                        <a href="{{ asset('storage/' . $document->file_path) }}"
                                           download="{{ $document->name ?? 'document.pdf' }}"
                                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Télécharger
                                        </a>
                                    </div>
                                </div>
                            @elseif($isOffice)
                                <!-- Aperçu stylisé pour Office -->
                                <div class="max-w-lg mx-auto">
                                    <div class="w-full h-64 rounded-lg border-2 border-dashed border-blue-300 bg-gradient-to-br from-blue-50 to-blue-100 flex flex-col items-center justify-center p-8">
                                        <div class="text-center">
                                            @php
                                                // Détection détaillée des types de documents Office
                                                $isPowerPoint = str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation') ||
                                                               in_array(strtolower($extension), ['ppt', 'pptx', 'pptm', 'pot', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'pps']) ||
                                                               str_contains($mimeType, 'presentationml');

                                                $isWord = str_contains($mimeType, 'word') || str_contains($mimeType, 'wordprocessingml') ||
                                                         in_array(strtolower($extension), ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'rtf']) ||
                                                         str_contains($mimeType, 'msword');

                                                $isExcel = str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet') ||
                                                          in_array(strtolower($extension), ['xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm', 'xlam']) ||
                                                          str_contains($mimeType, 'spreadsheetml');

                                                $isLibreOffice = in_array(strtolower($extension), ['odt', 'ott', 'ods', 'ots', 'odp', 'otp', 'odg', 'otg']) ||
                                                                str_contains($mimeType, 'opendocument');

                                                $isOtherOffice = in_array(strtolower($extension), ['pages', 'numbers', 'key', 'vsd', 'vsdx', 'mpp']) ||
                                                                str_contains($mimeType, 'visio') || str_contains($mimeType, 'project');
                                            @endphp

                                            @if($isPowerPoint)
                                                <svg class="w-16 h-16 text-orange-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <h3 class="text-xl font-semibold text-orange-700 mb-2">
                                                    @if(in_array(strtolower($extension), ['ppsx', 'ppsm', 'pps']))
                                                        Diaporama PowerPoint
                                                    @else
                                                        Présentation PowerPoint
                                                    @endif
                                                </h3>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">{{ strtoupper($extension) }}</span>
                                            @elseif($isWord)
                                                <svg class="w-16 h-16 text-blue-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5zm2 3h8v1H6V8zm0 2h8v1H6v-1zm0 2h5v1H6v-1z"/>
                                                </svg>
                                                <h3 class="text-xl font-semibold text-blue-700 mb-2">
                                                    @if(strtolower($extension) === 'rtf')
                                                        Document RTF
                                                    @elseif(in_array(strtolower($extension), ['dot', 'dotx', 'dotm']))
                                                        Modèle Word
                                                    @else
                                                        Document Word
                                                    @endif
                                                </h3>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">{{ strtoupper($extension) }}</span>
                                            @elseif($isExcel)
                                                <svg class="w-16 h-16 text-green-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                                </svg>
                                                <h3 class="text-xl font-semibold text-green-700 mb-2">
                                                    @if(in_array(strtolower($extension), ['xlt', 'xltx', 'xltm']))
                                                        Modèle Excel
                                                    @elseif(strtolower($extension) === 'xlam')
                                                        Complément Excel
                                                    @else
                                                        Feuille de calcul Excel
                                                    @endif
                                                </h3>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">{{ strtoupper($extension) }}</span>
                                            @elseif($isLibreOffice)
                                                <svg class="w-16 h-16 text-purple-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <h3 class="text-xl font-semibold text-purple-700 mb-2">
                                                    @if(in_array(strtolower($extension), ['odt', 'ott']))
                                                        Document LibreOffice Writer
                                                    @elseif(in_array(strtolower($extension), ['ods', 'ots']))
                                                        Tableur LibreOffice Calc
                                                    @elseif(in_array(strtolower($extension), ['odp', 'otp']))
                                                        Présentation LibreOffice Impress
                                                    @else
                                                        Document LibreOffice
                                                    @endif
                                                </h3>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">{{ strtoupper($extension) }}</span>
                                            @elseif($isOtherOffice)
                                                <svg class="w-16 h-16 text-indigo-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <h3 class="text-xl font-semibold text-indigo-700 mb-2">
                                                    @if(strtolower($extension) === 'pages')
                                                        Document Pages (Apple)
                                                    @elseif(strtolower($extension) === 'numbers')
                                                        Tableur Numbers (Apple)
                                                    @elseif(strtolower($extension) === 'key')
                                                        Présentation Keynote (Apple)
                                                    @elseif(in_array(strtolower($extension), ['vsd', 'vsdx']))
                                                        Diagramme Visio
                                                    @elseif(strtolower($extension) === 'mpp')
                                                        Projet Microsoft Project
                                                    @else
                                                        Document spécialisé
                                                    @endif
                                                </h3>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{ strtoupper($extension) }}</span>
                                            @else
                                                <svg class="w-16 h-16 text-blue-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                                                </svg>
                                                <h3 class="text-xl font-semibold text-blue-700 mb-2">Document Office</h3>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">{{ strtoupper($extension) }}</span>
                                            @endif
                                        </div>

                                        <!-- OCR Preview si disponible -->
                                        @if(!empty($document->ocr_text) && $document->ocr_text !== 'En attente de traitement...')
                                            <div class="mt-4 max-w-sm">
                                                <div class="bg-white/80 rounded-lg p-3 text-xs text-gray-700 text-left">
                                                    <div class="font-medium mb-1">Aperçu du contenu :</div>
                                                    <div class="text-xs leading-tight">
                                                        {{ Str::limit(strip_tags($document->ocr_text), 150) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ asset('storage/' . $document->file_path) }}"
                                           download="{{ $document->name ?? 'document.' . $extension }}"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Télécharger
                                        </a>
                                    </div>
                                </div>
                            @elseif($isText)
                                <!-- Aperçu texte avec contenu réel -->
                                <div class="max-w-2xl mx-auto">
                                    <div class="bg-white rounded-lg border shadow-sm p-6 text-left">
                                        <div class="flex items-center mb-4">
                                            <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900">Fichier Texte</h3>
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ strtoupper($extension) }}</span>
                                        </div>

                                        @if($document->file_path && file_exists(public_path('storage/' . $document->file_path)))
                                            @php
                                                $fileContent = file_get_contents(public_path('storage/' . $document->file_path));
                                                $preview = substr($fileContent, 0, 1000);
                                            @endphp
                                            <div class="bg-gray-50 rounded border p-4">
                                                <pre class="text-sm text-gray-700 whitespace-pre-wrap font-mono">{{ $preview }}{{ strlen($fileContent) > 1000 ? "\n\n... [Contenu tronqué]" : '' }}</pre>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded border p-4 text-center text-gray-500">
                                                <p>Contenu du fichier non accessible</p>
                                            </div>
                                        @endif

                                        <div class="mt-4 text-center">
                                            <a href="{{ asset('storage/' . $document->file_path) }}"
                                               download="{{ $document->name ?? 'document.' . $extension }}"
                                               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Aperçu générique pour autres types -->
                                <div class="max-w-lg mx-auto">
                                    <div class="w-full h-64 rounded-lg border-2 border-dashed border-purple-300 bg-gradient-to-br from-purple-50 to-purple-100 flex flex-col items-center justify-center p-8">
                                        <div class="text-center">
                                            <div class="text-6xl mb-4">
                                                @if(str_contains($mimeType, 'video') || in_array(strtolower($extension), ['mp4', 'avi', 'mov', 'wmv']))
                                                    🎥
                                                @elseif(str_contains($mimeType, 'audio') || in_array(strtolower($extension), ['mp3', 'wav', 'flac', 'ogg']))
                                                    🎵
                                                @elseif(str_contains($mimeType, 'archive') || in_array(strtolower($extension), ['zip', 'rar', '7z', 'tar']))
                                                    📦
                                                @else
                                                    📄
                                                @endif
                                            </div>
                                            <h3 class="text-xl font-semibold text-purple-700 mb-2">Fichier {{ strtoupper($extension) }}</h3>
                                            <p class="text-sm text-purple-600 mb-4">Type : {{ $mimeType ?: 'Non détecté' }}</p>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">{{ strtoupper($extension) }}</span>
                                        </div>

                                        <!-- OCR Preview si disponible -->
                                        @if(!empty($document->ocr_text) && $document->ocr_text !== 'En attente de traitement...')
                                            <div class="mt-4 max-w-sm">
                                                <div class="bg-white/80 rounded-lg p-3 text-xs text-gray-700 text-left">
                                                    <div class="font-medium mb-1">Aperçu du contenu :</div>
                                                    <div class="text-xs leading-tight">
                                                        {{ Str::limit(strip_tags($document->ocr_text), 150) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ asset('storage/' . $document->file_path) }}"
                                           download="{{ $document->name ?? 'document.' . $extension }}"
                                           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Télécharger
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center">
                                <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Document</h3>
                                <p class="text-gray-600">Aucun aperçu disponible</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Document Information & AI Analysis -->
            <div class="space-y-6">
                <!-- Document Info -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du document</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de document</label>
                            <p class="text-gray-900">{{ $document->type ?? 'Non spécifié' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Taille</label>
                            <p class="text-gray-900">
                                {{ isset($document->file_size) ? number_format($document->file_size / 1024, 2) . ' KB' : 'Inconnue' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                            <p class="text-gray-900">
                                {{ isset($document->file_path) ? strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) : 'Inconnu' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut de traitement</label>
                            @php
                                $status = $document->status ?? 'uploaded';
                                $hasOcr = isset($document->ocr_text) && !empty(trim($document->ocr_text));
                                $hasAi = isset($document->ai_summary) && !empty(trim($document->ai_summary));

                                if ($hasOcr && $hasAi) {
                                    $actualStatus = 'completed';
                                    $statusLabel = '✅ Traitement terminé';
                                    $statusClass = 'bg-green-100 text-green-800';
                                } elseif ($hasOcr || $hasAi) {
                                    $actualStatus = 'processing';
                                    $statusLabel = '⏳ Traitement en cours';
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                } else {
                                    $actualStatus = 'uploaded';
                                    $statusLabel = '📤 Téléchargé';
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- AI Analysis -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Analyse IA (Mistral)</h3>
                        @if(isset($document->ai_analysis_completed) && $document->ai_analysis_completed)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Analysé
                            </span>
                        @endif
                    </div>

                    @if(isset($document->ai_summary) && $document->ai_summary)
                        <!-- Document Type -->
                        @if(isset($document->ai_document_type) && $document->ai_document_type && $document->ai_document_type !== 'document')
                            <div class="bg-indigo-50 rounded-lg p-4 mb-4">
                                <h4 class="font-medium text-indigo-900 mb-1">Type de document détecté</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 capitalize">
                                    {{ str_replace('-', ' ', $document->ai_document_type) }}
                                </span>
                            </div>
                        @endif

                        <!-- AI Summary -->
                        <div class="bg-blue-50 rounded-lg p-4 mb-4">
                            <h4 class="font-medium text-blue-900 mb-2">Résumé automatique</h4>
                            <div class="text-blue-800 text-sm leading-relaxed whitespace-pre-wrap">{{ $document->ai_summary }}</div>
                        </div>

                        <!-- Key Information -->
                        @if(isset($document->extracted_info) && $document->extracted_info)
                            @php
                                $keyInfo = json_decode($document->extracted_info, true);
                            @endphp
                            @if(!empty($keyInfo))
                                <div class="bg-amber-50 rounded-lg p-4 mb-4">
                                    <h4 class="font-medium text-amber-900 mb-2">Informations clés extraites</h4>
                                    <div class="grid grid-cols-1 gap-2 text-sm">
                                        @if(!empty($keyInfo['dates']))
                                            <div>
                                                <span class="font-medium text-amber-800">Dates :</span>
                                                <span class="text-amber-700">{{ implode(', ', $keyInfo['dates']) }}</span>
                                            </div>
                                        @endif
                                        @if(!empty($keyInfo['amounts']))
                                            <div>
                                                <span class="font-medium text-amber-800">Montants :</span>
                                                <span class="text-amber-700">{{ implode(', ', $keyInfo['amounts']) }}</span>
                                            </div>
                                        @endif
                                        @if(!empty($keyInfo['emails']))
                                            <div>
                                                <span class="font-medium text-amber-800">E-mails :</span>
                                                <span class="text-amber-700">{{ implode(', ', $keyInfo['emails']) }}</span>
                                            </div>
                                        @endif
                                        @if(!empty($keyInfo['phones']))
                                            <div>
                                                <span class="font-medium text-amber-800">Téléphones :</span>
                                                <span class="text-amber-700">{{ implode(', ', $keyInfo['phones']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    @elseif(isset($document->status) && $document->status === 'processing')
                        <!-- Processing State -->
                        <div class="text-center py-8" id="ai-analysis-section">
                            <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-3"></div>
                            <p class="text-gray-600 text-sm mb-2">Analyse IA en cours...</p>
                            <p class="text-gray-500 text-xs">Traitement automatique du document avec Mistral AI</p>
                        </div>
                    @elseif(isset($document->ai_analysis_completed) && !$document->ai_analysis_completed)
                        <!-- Analysis Failed -->
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Échec de l'analyse IA</h4>
                            <p class="text-gray-600 text-sm mb-4">Une erreur est survenue lors de l'analyse automatique</p>
                            <button onclick="analyzeWithAI({{ $document->id }})"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Relancer l'analyse
                            </button>
                        </div>
                    @else
                        <!-- Analysis Not Available -->
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Analyse IA non disponible</h4>
                            <p class="text-gray-600 text-sm mb-4">Le service d'analyse automatique n'est pas disponible pour ce document</p>

                            @if(isset($document->ocr_text) && $document->ocr_text && !str_contains($document->ocr_text, 'ANALYSE BASIQUE'))
                                <!-- Si on a du texte OCR réel, proposer l'analyse manuelle -->
                                <button onclick="analyzeWithAI({{ $document->id }})"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Lancer l'analyse IA manuellement
                                </button>
                            @elseif(isset($document->ocr_text) && str_contains($document->ocr_text, 'ANALYSE BASIQUE'))
                                <!-- Si on a juste l'analyse basique, expliquer pourquoi l'IA n'est pas disponible -->
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">
                                    <p class="text-amber-800">
                                        <strong>Pourquoi l'analyse IA n'est-elle pas disponible ?</strong><br>
                                        L'extraction de texte n'a pas pu être effectuée car Tesseract OCR n'est pas installé.
                                        L'analyse IA nécessite du texte extrait pour fonctionner.
                                    </p>
                                    <p class="text-amber-700 mt-2">
                                        Une fois Tesseract installé, uploadez à nouveau ce document pour bénéficier de l'analyse IA complète.
                                    </p>
                                </div>
                            @else
                                <!-- Aucun texte OCR disponible -->
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm">
                                    <p class="text-red-800">
                                        Aucun texte n'a pu être extrait de ce document.
                                        Vérifiez que le fichier est une image lisible contenant du texte.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- OCR Text Section (if available) -->
                    @if(isset($document->ocr_text) && $document->ocr_text)
                        <div class="bg-gray-50 rounded-lg p-4 mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">Texte extrait (OCR)</h4>
                                <button onclick="toggleOcrText()" class="text-gray-500 hover:text-gray-700 text-xs">
                                    <span id="ocr-toggle-text">Afficher</span>
                                </button>
                            </div>
                            <div id="ocr-text-content" class="hidden max-h-64 overflow-y-auto">
                                <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $document->ocr_text }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions disponibles</h3>
                    <div class="space-y-3">
                        <a href="{{ route('documents.download', $document) }}"
                           class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Télécharger original
                        </a>

                        <button onclick="exportToPDF({{ $document->id }})"
                                class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Exporter analyse PDF
                        </button>

                        <button onclick="shareDocument({{ $document->id }})"
                                class="w-full flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Partager document
                        </button>

                        @if(isset($document->ai_summary) && $document->ai_summary)
                        <button onclick="regenerateAnalysis({{ $document->id }})"
                                class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Relancer l'analyse IA
                        </button>
                        @endif

                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="w-full"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Supprimer document
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Note: Auto-refresh désactivé pour éviter les rechargements intempestifs
    // Les statuts sont mis à jour dynamiquement sans rechargement de page

    // Analyze document with Mistral AI
    async function analyzeWithAI(documentId) {
        const analyzeBtn = document.getElementById('analyze-btn');
        const analyzeText = document.getElementById('analyze-text');
        const analyzeIcon = document.getElementById('analyze-icon');
        const statusText = document.getElementById('ai-status-text');
        const aiSection = document.getElementById('ai-analysis-section');

        // Disable button and show loading state
        analyzeBtn.disabled = true;
        analyzeText.textContent = 'Analyse en cours...';
        analyzeIcon.classList.add('animate-spin');
        statusText.textContent = 'Analyse IA avec Mistral en cours...';

        try {
            const response = await fetch(`/documents/${documentId}/analyze`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Create success message
                const successMessage = document.createElement('div');
                successMessage.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-4';
                successMessage.innerHTML = `
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="font-medium text-green-900">Analyse IA terminée avec succès!</h4>
                    </div>
                    <p class="text-green-700 text-sm">Le document a été analysé et classifié comme: <strong>${result.document_type}</strong></p>
                `;

                // Create AI summary section
                const summarySection = document.createElement('div');
                summarySection.className = 'bg-blue-50 rounded-lg p-4 mt-4';
                summarySection.innerHTML = `
                    <h4 class="font-medium text-blue-900 mb-2">Résumé automatique (Mistral IA)</h4>
                    <div class="text-blue-800 text-sm leading-relaxed whitespace-pre-wrap">${result.summary}</div>
                `;

                // Replace the loading section with success content
                aiSection.innerHTML = '';
                aiSection.appendChild(successMessage);
                aiSection.appendChild(summarySection);

                // Show key information if available
                if (result.key_info && Object.keys(result.key_info).length > 0) {
                    const keyInfoSection = document.createElement('div');
                    keyInfoSection.className = 'bg-gray-50 rounded-lg p-4 mt-4';

                    let keyInfoHtml = '<h4 class="font-medium text-gray-900 mb-2">Informations extraites</h4><div class="space-y-2">';

                    Object.entries(result.key_info).forEach(([key, values]) => {
                        if (values && values.length > 0) {
                            keyInfoHtml += `
                                <div class="text-sm">
                                    <span class="font-medium text-gray-700">${key.charAt(0).toUpperCase() + key.slice(1)}:</span>
                                    <span class="text-gray-600 ml-1">${values.join(', ')}</span>
                                </div>
                            `;
                        }
                    });

                    keyInfoHtml += '</div>';
                    keyInfoSection.innerHTML = keyInfoHtml;
                    aiSection.appendChild(keyInfoSection);
                }

            } else {
                // Show error message
                const errorMessage = document.createElement('div');
                errorMessage.className = 'bg-red-50 border border-red-200 rounded-lg p-4';
                errorMessage.innerHTML = `
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="font-medium text-red-900">Erreur d'analyse</h4>
                    </div>
                    <p class="text-red-700 text-sm">${result.error || 'Une erreur est survenue lors de l\'analyse IA.'}</p>
                    <button onclick="analyzeWithAI(${documentId})" class="mt-3 inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                        Réessayer
                    </button>
                `;

                aiSection.innerHTML = '';
                aiSection.appendChild(errorMessage);
            }

        } catch (error) {
            console.error('Error analyzing document:', error);

            // Show network error
            const errorMessage = document.createElement('div');
            errorMessage.className = 'bg-red-50 border border-red-200 rounded-lg p-4';
            errorMessage.innerHTML = `
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h4 class="font-medium text-red-900">Erreur de connexion</h4>
                </div>
                <p class="text-red-700 text-sm">Impossible de se connecter au service d'analyse IA. Vérifiez votre connexion internet.</p>
                <button onclick="analyzeWithAI(${documentId})" class="mt-3 inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                    Réessayer
                </button>
            `;

            aiSection.innerHTML = '';
            aiSection.appendChild(errorMessage);
        }
    }

    function exportToPDF(documentId) {
        // Créer un PDF avec le résumé d'analyse
        alert('Fonctionnalité d\'export PDF en développement');
    }

    function shareDocument(documentId) {
        // Partager le document
        const url = window.location.href;
        if (navigator.share) {
            navigator.share({
                title: 'Document analysé',
                text: 'Consultez ce document analysé par IA',
                url: url
            });
        } else {
            // Fallback pour les navigateurs qui ne supportent pas Web Share API
            navigator.clipboard.writeText(url).then(() => {
                alert('Lien copié dans le presse-papiers !');
            });
        }
    }

    function regenerateAnalysis(documentId) {
        if (confirm('Voulez-vous relancer l\'analyse IA de ce document ?')) {
            analyzeWithAI(documentId);
        }
    }

    function toggleOcrText() {
        const content = document.getElementById('ocr-text-content');
        const toggleText = document.getElementById('ocr-toggle-text');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            toggleText.textContent = 'Masquer';
        } else {
            content.classList.add('hidden');
            toggleText.textContent = 'Afficher';
        }
    }

    // Note: Auto-refresh désactivé - la page ne se recharge plus automatiquement
    // Cela améliore l'expérience utilisateur et évite les interruptions
</script>
@endpush

<!-- Inclure le modal PDF -->
@include('components.pdf-modal')
