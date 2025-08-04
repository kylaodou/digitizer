<!-- Modal universel de prévisualisation de documents -->
<div id="universalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 transition-opacity duration-300" onclick="closeUniversalModal(event)">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95" onclick="event.stopPropagation()">
            <!-- En-tête du modal -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-white text-lg font-semibold flex items-center">
                    <svg id="modalHeaderIcon" class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span id="universalModalTitle">Prévisualisation du document</span>
                </h3>
                <button onclick="closeUniversalModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Contenu du modal -->
            <div class="p-6 max-h-[80vh] overflow-y-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Zone de prévisualisation -->
                    <div class="space-y-4">
                        <h4 id="previewTitle" class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg id="previewIcon" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            Document
                        </h4>

                        <!-- Zone de prévisualisation dynamique -->
                        <div id="previewContainer" class="bg-gray-100 rounded-lg p-4 min-h-96">
                            <!-- Contenu sera généré dynamiquement -->
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2">
                            <a id="downloadBtn" href="#" download class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Télécharger
                            </a>
                            <button id="openInNewTabBtn" onclick="openInNewTab()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Ouvrir dans un nouvel onglet
                            </button>
                        </div>
                    </div>

                    <!-- Informations et résultats d'analyse -->
                    <div class="space-y-4">
                        <!-- Informations du document -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Informations du document
                            </h4>
                            <div id="documentInfo" class="space-y-2 text-sm text-gray-600">
                                <!-- Sera rempli par JavaScript -->
                            </div>
                        </div>

                        <!-- Statut de traitement -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Statut de traitement
                            </h4>
                            <div id="processingStatus" class="space-y-2">
                                <!-- Sera rempli par JavaScript -->
                            </div>
                        </div>

                        <!-- Résultats OCR -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Extraction OCR
                            </h4>
                            <div id="ocrResults" class="space-y-2">
                                <!-- Sera rempli par JavaScript -->
                            </div>
                        </div>

                        <!-- Analyse IA -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Analyse IA (Mistral)
                            </h4>
                            <div id="aiAnalysis" class="space-y-2">
                                <!-- Sera rempli par JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour le modal universel -->
<script>
let currentFileUrl = '';
let currentFileType = '';

function openUniversalModal(fileUrl, documentData, fileExtension) {
    currentFileUrl = fileUrl;
    currentFileType = fileExtension;

    const modal = document.getElementById('universalModal');
    const title = document.getElementById('universalModalTitle');
    const downloadBtn = document.getElementById('downloadBtn');

    // Configurer le titre
    title.textContent = documentData.filename || 'Document';

    // Configurer le bouton de téléchargement
    downloadBtn.href = fileUrl;
    downloadBtn.download = documentData.filename || 'document';

    // Générer la prévisualisation selon le type
    generatePreview(fileUrl, fileExtension, documentData);

    // Remplir les informations
    fillDocumentInfo(documentData);
    fillProcessingStatus(documentData);
    fillOcrResults(documentData);
    fillAiAnalysis(documentData);

    // Afficher le modal avec animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
        modal.querySelector('.transform').classList.add('scale-100');
    }, 10);
}

function generatePreview(fileUrl, extension, documentData) {
    const container = document.getElementById('previewContainer');
    const title = document.getElementById('previewTitle');
    const icon = document.getElementById('previewIcon');
    const headerIcon = document.getElementById('modalHeaderIcon');

    switch(extension.toLowerCase()) {
        case 'pdf':
            title.innerHTML = '<svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>Document PDF';
            container.innerHTML = `
                <iframe src="${fileUrl}#toolbar=1&navpanes=1&scrollbar=1"
                        class="w-full h-96 rounded border-0"
                        frameborder="0"
                        title="Prévisualisation PDF">
                </iframe>
            `;
            break;

        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'bmp':
        case 'webp':
            title.innerHTML = '<svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>Image';
            container.innerHTML = `
                <div class="flex items-center justify-center h-96">
                    <img src="${fileUrl}" alt="${documentData.filename}" class="max-w-full max-h-full object-contain rounded">
                </div>
            `;
            break;

        case 'doc':
        case 'docx':
            title.innerHTML = '<svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5zm2 3h8v1H6V8zm0 2h8v1H6v-1zm0 2h5v1H6v-1z"/></svg>Document Word';
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-96 text-center">
                    <div class="w-24 h-24 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5zm2 3h8v1H6V8zm0 2h8v1H6v-1zm0 2h5v1H6v-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${documentData.filename}</h3>
                    <p class="text-sm text-gray-500 mb-4">Document Microsoft Word</p>
                    <p class="text-xs text-gray-400">Prévisualisation non disponible - Utilisez le bouton télécharger pour ouvrir le fichier</p>
                </div>
            `;
            break;

        case 'xls':
        case 'xlsx':
            title.innerHTML = '<svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>Fichier Excel';
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-96 text-center">
                    <div class="w-24 h-24 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${documentData.filename}</h3>
                    <p class="text-sm text-gray-500 mb-4">Fichier Microsoft Excel</p>
                    <p class="text-xs text-gray-400">Prévisualisation non disponible - Utilisez le bouton télécharger pour ouvrir le fichier</p>
                </div>
            `;
            break;

        case 'ppt':
        case 'pptx':
            title.innerHTML = '<svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/></svg>Présentation PowerPoint';
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-96 text-center">
                    <div class="w-24 h-24 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${documentData.filename}</h3>
                    <p class="text-sm text-gray-500 mb-4">Présentation Microsoft PowerPoint</p>
                    <p class="text-xs text-gray-400">Prévisualisation non disponible - Utilisez le bouton télécharger pour ouvrir le fichier</p>
                </div>
            `;
            break;

        case 'txt':
            title.innerHTML = '<svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Fichier texte';
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-96 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${documentData.filename}</h3>
                    <p class="text-sm text-gray-500 mb-4">Fichier texte</p>
                    <p class="text-xs text-gray-400">Prévisualisation non disponible - Utilisez le bouton télécharger pour ouvrir le fichier</p>
                </div>
            `;
            break;

        default:
            title.innerHTML = '<svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Document';
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-96 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${documentData.filename}</h3>
                    <p class="text-sm text-gray-500 mb-4">Type: ${extension.toUpperCase()}</p>
                    <p class="text-xs text-gray-400">Prévisualisation non disponible - Utilisez le bouton télécharger pour ouvrir le fichier</p>
                </div>
            `;
    }
}

function closeUniversalModal(event) {
    if (event && event.target !== event.currentTarget && event.type !== 'click') {
        return;
    }

    const modal = document.getElementById('universalModal');
    const transform = modal.querySelector('.transform');

    // Animation de fermeture
    modal.classList.remove('opacity-100');
    transform.classList.remove('scale-100');
    transform.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        // Nettoyer le contenu
        document.getElementById('previewContainer').innerHTML = '';
    }, 300);
}

function openInNewTab() {
    if (currentFileUrl) {
        window.open(currentFileUrl, '_blank');
    }
}

function fillDocumentInfo(data) {
    const container = document.getElementById('documentInfo');
    container.innerHTML = `
        <div class="flex justify-between">
            <span class="font-medium">Nom du fichier :</span>
            <span>${data.filename || 'N/A'}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-medium">Taille :</span>
            <span>${data.file_size || 'N/A'}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-medium">Type :</span>
            <span>${data.mime_type || 'N/A'}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-medium">Uploadé le :</span>
            <span>${data.created_at || 'N/A'}</span>
        </div>
    `;
}

function fillProcessingStatus(data) {
    const container = document.getElementById('processingStatus');
    const status = data.status || 'uploaded';

    let statusInfo = {
        'uploaded': {
            label: 'Téléchargé',
            color: 'blue',
            icon: '📤',
            description: 'Document téléchargé avec succès'
        },
        'processing': {
            label: 'En cours de traitement',
            color: 'yellow',
            icon: '⏳',
            description: 'Analyse OCR et IA en cours...'
        },
        'completed': {
            label: 'Traitement terminé',
            color: 'green',
            icon: '✅',
            description: 'Analyse terminée avec succès'
        },
        'failed': {
            label: 'Échec du traitement',
            color: 'red',
            icon: '❌',
            description: 'Erreur lors du traitement'
        }
    };

    const info = statusInfo[status] || statusInfo['uploaded'];

    container.innerHTML = `
        <div class="flex items-center space-x-3">
            <span class="text-2xl">${info.icon}</span>
            <div>
                <div class="font-medium text-${info.color}-800">${info.label}</div>
                <div class="text-sm text-gray-600">${info.description}</div>
            </div>
        </div>
    `;
}

function fillOcrResults(data) {
    const container = document.getElementById('ocrResults');

    if (data.ocr_text) {
        const isSuccess = data.ocr_text.includes('EXTRACTION RÉELLE RÉUSSIE');
        const preview = data.ocr_text.length > 200 ? data.ocr_text.substring(0, 200) + '...' : data.ocr_text;

        container.innerHTML = `
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="text-${isSuccess ? 'green' : 'red'}-600 text-sm font-medium">
                        ${isSuccess ? '✅ Extraction réussie' : '⚠️ Extraction partielle'}
                    </span>
                </div>
                <div class="bg-white p-3 rounded border text-sm">
                    <pre class="whitespace-pre-wrap font-mono text-xs">${preview}</pre>
                </div>
                ${data.ocr_text.length > 200 ? `
                    <button onclick="showFullOcrText()" class="text-blue-600 hover:text-blue-800 text-sm">
                        Voir le texte complet →
                    </button>
                ` : ''}
            </div>
        `;
    } else {
        container.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="text-sm">Aucun texte extrait pour le moment</div>
                <div class="text-xs text-gray-400 mt-1">L'analyse OCR n'a pas encore été effectuée</div>
            </div>
        `;
    }
}

function fillAiAnalysis(data) {
    const container = document.getElementById('aiAnalysis');

    if (data.ai_summary) {
        // Nettoyer le formatage Markdown/HTML
        let cleanSummary = data.ai_summary
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // **text** -> <strong>text</strong>
            .replace(/\*(.*?)\*/g, '<em>$1</em>') // *text* -> <em>text</em>
            .replace(/###\s*(.*?)$/gm, '<h4 class="font-semibold text-gray-800 mt-3 mb-1">$1</h4>') // ### Title
            .replace(/##\s*(.*?)$/gm, '<h3 class="font-semibold text-gray-800 mt-4 mb-2">$1</h3>') // ## Title
            .replace(/#\s*(.*?)$/gm, '<h2 class="font-bold text-gray-900 mt-4 mb-2">$1</h2>') // # Title
            .replace(/^\s*[-*+]\s+(.*)$/gm, '<li class="ml-4">• $1</li>') // Liste
            .replace(/\n\n/g, '</p><p class="mb-2">') // Paragraphes
            .replace(/\n/g, '<br>'); // Sauts de ligne

        // Si on détecte des listes, les wrapper
        if (cleanSummary.includes('<li')) {
            cleanSummary = cleanSummary.replace(/(<li.*?<\/li>)/g, '<ul class="list-none space-y-1">$1</ul>');
        }

        // Wrapper dans un paragraphe si pas déjà fait
        if (!cleanSummary.startsWith('<h') && !cleanSummary.startsWith('<p')) {
            cleanSummary = '<p class="mb-2">' + cleanSummary + '</p>';
        }

        container.innerHTML = `
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <span class="text-green-600 text-sm font-medium">✅ Analyse terminée</span>
                </div>
                <div class="bg-white p-4 rounded border">
                    <div class="text-sm text-gray-800 prose prose-sm max-w-none">
                        ${cleanSummary}
                    </div>
                </div>
                ${data.ai_classification ? `
                    <div class="bg-blue-50 p-3 rounded border border-blue-200">
                        <div class="text-xs text-blue-800">
                            <strong class="font-medium">Classification :</strong>
                            <span class="ml-1 px-2 py-1 bg-blue-100 rounded text-xs">${data.ai_classification}</span>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    } else {
        container.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <div class="text-sm">Aucune analyse IA disponible</div>
                <div class="text-xs text-gray-400 mt-1">L'analyse Mistral n'a pas encore été effectuée</div>
            </div>
        `;
    }
}

// Fermer le modal avec Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeUniversalModal();
    }
});

// Fonction de compatibilité pour l'ancien modal PDF
function openPdfModal(pdfUrl, documentData) {
    openUniversalModal(pdfUrl, documentData, 'pdf');
}

function closePdfModal(event) {
    closeUniversalModal(event);
}

// Fonction pour l'intégration avec la liste de documents
function openDocumentModal(documentId) {
    // Récupérer les données du document via l'API
    fetch(`/api/documents/${documentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const document = data.document;

                // Vérifier que les données essentielles existent
                if (!document.filename || !document.file_path) {
                    console.error('Données du document manquantes:', document);
                    alert('Erreur: données du document incomplètes');
                    return;
                }

                const fileUrl = `/storage/${document.file_path}`;
                const extension = document.filename.includes('.')
                    ? document.filename.split('.').pop().toLowerCase()
                    : 'unknown';

                openUniversalModal(fileUrl, document, extension);
            } else {
                console.error('Erreur lors de la récupération du document:', data.message);
                alert('Erreur lors du chargement du document');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement du document');
        });
}
</script>
