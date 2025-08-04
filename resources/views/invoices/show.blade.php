@extends('layouts.app')

@section('title', 'Facture #' . $invoice->invoice_number)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-rose-50 via-blue-50 to-indigo-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Navigation et titre -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('invoices.index') }}"
                       class="inline-flex items-center text-slate-600 hover:text-indigo-600 transition-colors duration-200 group">
                        <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Retour aux factures
                    </a>
                    <div class="h-6 w-px bg-slate-300"></div>
                    <h1 class="text-3xl font-bold text-slate-800">
                        Facture <span class="text-indigo-600">#{{ $invoice->invoice_number }}</span>
                    </h1>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm hover:bg-white text-slate-700 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-lg border border-slate-200/50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimer
                    </button>
                    <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Télécharger PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Carte principale -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            <!-- En-tête coloré -->
            <div class="bg-gradient-to-r from-violet-500 via-purple-500 to-indigo-600 px-8 py-8 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent"></div>
                <div class="relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Document Digitizer</h2>
                            <p class="text-violet-100 opacity-90 mb-4">Service de numérisation professionnel</p>
                            <div class="space-y-1 text-sm text-violet-100/80">
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    contact@document-digitizer.com
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    +33 1 23 45 67 89
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-6 py-4 border border-white/30">
                                @if($invoice->status === 'paid')
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></div>
                                        ✅ Payée
                                    </div>
                                @elseif($invoice->status === 'pending')
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                        <div class="w-2 h-2 bg-amber-500 rounded-full mr-2 animate-pulse"></div>
                                        ⏳ En attente
                                    </div>
                                @else
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                        <div class="w-2 h-2 bg-rose-500 rounded-full mr-2"></div>
                                        ❌ {{ ucfirst($invoice->status) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Informations principales -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Informations client -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200/50">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            Informations client
                        </h3>
                        <div class="space-y-3 text-slate-600">
                            <p class="font-semibold text-slate-800 text-lg">{{ $invoice->client_name ?? Auth::user()->name }}</p>
                            <p class="flex items-center text-slate-600">
                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $invoice->client_email ?? Auth::user()->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Détails facture -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200/50">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            Détails de la facture
                        </h3>
                        <div class="space-y-3 text-slate-600">
                            <div class="flex justify-between items-center">
                                <span>Numéro :</span>
                                <span class="font-mono bg-slate-100 px-2 py-1 rounded text-slate-800">#{{ $invoice->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Date d'émission :</span>
                                <span class="text-slate-800 font-medium">{{ $invoice->issued_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Type de service :</span>
                                <span class="text-slate-800 font-medium">Numérisation OCR</span>
                            </div>
                        </div>
                    </div>
                <!-- Éléments de la facture -->
                <div class="bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl p-6 border border-slate-200/50 mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-6 flex items-center">
                        <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        Services fournis
                    </h3>

                    <!-- Tableau des services -->
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-gradient-to-r from-slate-100 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Document</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Pages</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Coût unitaire</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($invoice->items ?? [] as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900">{{ $item->description }}</div>
                                                    <div class="text-sm text-slate-500">OCR + Analyse IA</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($item->document)
                                                <div class="flex items-center">
                                                    @php
                                                        $mimeType = $item->document->mime_type ?? '';
                                                        if (str_contains($mimeType, 'pdf')) {
                                                            $iconColor = 'text-red-500';
                                                            $bgColor = 'bg-red-50';
                                                        } elseif (str_contains($mimeType, 'image')) {
                                                            $iconColor = 'text-green-500';
                                                            $bgColor = 'bg-green-50';
                                                        } else {
                                                            $iconColor = 'text-blue-500';
                                                            $bgColor = 'bg-blue-50';
                                                        }
                                                    @endphp
                                                    <div class="w-8 h-8 {{ $bgColor }} rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-4 h-4 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-slate-900 max-w-48 truncate">{{ $item->document->original_filename }}</div>
                                                        <a href="{{ route('documents.show', $item->document) }}" class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors">
                                                            Voir le document →
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-slate-500 italic">Document supprimé</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                                {{ $item->quantity }} page{{ $item->quantity > 1 ? 's' : '' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 text-right">
                                            {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 text-right">
                                            {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                            Aucun élément dans cette facture
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Résumé financier -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Informations de paiement -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            Informations de paiement
                        </h3>
                        <div class="space-y-3 text-slate-600">
                            <div class="flex justify-between">
                                <span>Méthode :</span>
                                <span class="text-slate-800 font-medium">
                                    @if($invoice->payment_method === 'account_balance')
                                        💳 Solde du compte
                                    @else
                                        {{ ucfirst($invoice->payment_method) }}
                                    @endif
                                </span>
                            </div>
                            @if($invoice->paid_at)
                                <div class="flex justify-between">
                                    <span>Payée le :</span>
                                    <span class="text-slate-800 font-medium">{{ $invoice->paid_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span>Pages traitées :</span>
                                <span class="text-slate-800 font-medium">{{ $invoice->total_pages }} page{{ $invoice->total_pages > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-6 border border-indigo-200/50">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            Résumé financier
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between text-slate-600">
                                <span>Sous-total :</span>
                                <span class="font-medium">{{ number_format($invoice->subtotal, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($invoice->tax_amount > 0)
                                <div class="flex justify-between text-slate-600">
                                    <span>TVA ({{ $invoice->tax_rate }}%) :</span>
                                    <span class="font-medium">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif
                            <div class="border-t border-indigo-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-slate-800">Total à payer :</span>
                                    <span class="text-2xl font-bold text-indigo-600">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                        <span>Montant total :</span>
                        <span>{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items de la facture -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Détails des services</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pages</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($item->document)
                                                <a href="{{ route('documents.show', $item->document) }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $item->document->title }}
                                                </a>
                                            @else
                                                Document supprimé
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $item->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($item->service_type === 'recto_verso')
                                        bg-green-100 text-green-800
                                    @else
                                        bg-blue-100 text-blue-800
                                    @endif">
                                    {{ $item->service_type === 'recto_verso' ? 'Recto-Verso' : 'Recto' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->page_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($item->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                Total général :
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
