<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{    
    public function generatePdf()
    {
        $products = Product::all();        
        $pdf = Pdf::loadView('pdf.product_report', compact('products'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isPhpEnabled' => true,
                      'isRemoteEnabled' => true 
                  ]);
    
        // Return the raw binary data with correct headers
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="product_report.pdf"',
        ]);
    }


}
