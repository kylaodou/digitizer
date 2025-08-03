<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Document;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the user's invoices.
     */
    public function index()
    {
        $invoices = Auth::user()->invoices()
            ->latest()
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Generate invoice for processed documents.
     */
    public function generate(Request $request)
    {
        $user = Auth::user();

        // Get completed documents without invoices
        $documents = Document::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('invoiceItems')
            ->get();

        if ($documents->isEmpty()) {
            return back()->with('warning', 'Aucun document à facturer.');
        }

        // Calculate totals
        $rectoPages = 0;
        $rectoVersoPages = 0;
        $rectoRate = 200; // FCFA
        $rectoVersoRate = 150; // FCFA

        foreach ($documents as $document) {
            if ($document->document_type === 'recto_verso') {
                $rectoVersoPages += $document->page_count;
            } else {
                $rectoPages += $document->page_count;
            }
        }

        $rectoCost = $rectoPages * $rectoRate;
        $rectoVersoCost = $rectoVersoPages * $rectoVersoRate;
        $totalAmount = $rectoCost + $rectoVersoCost;

        // Create invoice
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'total_amount' => $totalAmount,
            'total_pages' => $rectoPages + $rectoVersoPages,
            'recto_pages' => $rectoPages,
            'recto_verso_pages' => $rectoVersoPages,
            'recto_cost' => $rectoCost,
            'recto_verso_cost' => $rectoVersoCost,
            'status' => 'pending',
            'issued_at' => now(),
        ]);

        // Create invoice items
        foreach ($documents as $document) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'document_id' => $document->id,
                'description' => 'Numérisation - ' . $document->title,
                'quantity' => $document->page_count,
                'unit_price' => $document->document_type === 'recto_verso' ? $rectoVersoRate : $rectoRate,
                'total_price' => $document->processing_cost,
            ]);
        }

        // Log invoice generation
        AuditLog::logAction('invoice_generated', $invoice, null, [
            'total_amount' => $totalAmount,
            'total_pages' => $rectoPages + $rectoVersoPages,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Facture générée avec succès.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        // Check if user owns the invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $invoice->load(['items.document', 'user']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadPdf(Invoice $invoice)
    {
        // Check if user owns the invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $invoice->load(['items.document', 'user']);

        // Log PDF download
        AuditLog::logAction('invoice_pdf_downloaded', $invoice);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('facture-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(Invoice $invoice, Request $request)
    {
        // Check if user owns the invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'payment_method' => ['required', 'string', 'max:255'],
        ]);

        $invoice->markAsPaid($request->payment_method);

        // Log payment
        AuditLog::logAction('invoice_paid', $invoice, null, [
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Facture marquée comme payée.');
    }

    /**
     * Get invoice statistics.
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'total_invoices' => $user->invoices()->count(),
            'paid_invoices' => $user->invoices()->where('status', 'paid')->count(),
            'pending_invoices' => $user->invoices()->where('status', 'pending')->count(),
            'total_amount_paid' => $user->invoices()->where('status', 'paid')->sum('total_amount'),
            'total_amount_pending' => $user->invoices()->where('status', 'pending')->sum('total_amount'),
            'current_month_amount' => $user->invoices()
                ->where('status', 'paid')
                ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total_amount'),
        ];

        return view('invoices.statistics', compact('stats'));
    }
}
