<!-- Modal de prévisualisation PDF -->
<div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 transition-opacity duration-300" onclick="closePdfModal(event)">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95" onclick="event.stopPropagation()">
            <!-- En-tête du modal -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-white text-lg font-semibold flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span id="pdfModalTitle">Prévisualisation du document</span>
                </h3>
                <button onclick="closePdfModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Contenu du modal -->
            <div class="p-6 max-h-[80vh] overflow-y-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Prévisualisation PDF -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            Document PDF
                        </h4>

                        <!-- Viewer PDF intégré -->
                        <div class="bg-gray-100 rounded-lg p-4 min-h-96">
                            <iframe
                                id="pdfViewer"
                                class="w-full h-96 rounded border-0"
                                frameborder="0"
                                title="Prévisualisation PDF">
                            </iframe>
                        </div>

                        <!-- Actions PDF -->
                        <div class="flex flex-wrap gap-2">
                            <a id="downloadPdfBtn" href="#" download class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Télécharger
                            </a>
                            <button onclick="openInNewTab()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
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

<!-- Scripts pour le modal PDF -->
<script>
let currentPdfUrl = '';

function openPdfModal(pdfUrl, documentData) {
    currentPdfUrl = pdfUrl;
    const modal = document.getElementById('pdfModal');
    const viewer = document.getElementById('pdfViewer');
    const title = document.getElementById('pdfModalTitle');
    const downloadBtn = document.getElementById('downloadPdfBtn');

    // Configurer le viewer PDF
    viewer.src = pdfUrl + '#toolbar=1&navpanes=1&scrollbar=1';

    // Configurer le titre
    title.textContent = documentData.filename || 'Document PDF';

    // Configurer le bouton de téléchargement
    downloadBtn.href = pdfUrl;
    downloadBtn.download = documentData.filename || 'document.pdf';

    // Remplir les informations du document
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

function closePdfModal(event) {
    if (event && event.target !== event.currentTarget && event.type !== 'click') {
        return;
    }

    const modal = document.getElementById('pdfModal');
    const transform = modal.querySelector('.transform');

    // Animation de fermeture
    modal.classList.remove('opacity-100');
    transform.classList.remove('scale-100');
    transform.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('pdfViewer').src = '';
    }, 300);
}

function openInNewTab() {
    if (currentPdfUrl) {
        window.open(currentPdfUrl, '_blank');
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
        container.innerHTML = `
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <span class="text-green-600 text-sm font-medium">✅ Analyse terminée</span>
                </div>
                <div class="bg-white p-3 rounded border">
                    <div class="text-sm text-gray-800">${data.ai_summary}</div>
                </div>
                ${data.ai_classification ? `
                    <div class="text-xs text-gray-600">
                        <strong>Classification :</strong> ${data.ai_classification}
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
        closePdfModal();
    }
});
</script>
