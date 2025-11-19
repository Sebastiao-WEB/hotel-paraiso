<?php

namespace App\Http\Controllers;

use App\Models\NotaCobranca;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaCobrancaController extends Controller
{
    public function pdf($id)
    {
        $nota = NotaCobranca::with(['reserva.cliente', 'reserva.quarto', 'reserva.servicos.servico', 'empresa'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pdf.nota-cobranca', compact('nota'));
        
        return $pdf->download('nota-cobranca-' . str_pad($nota->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}


