<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\KafkaProducerService;

class PdfController extends Controller
{    
    public function generatePdf(KafkaProducerService $kafkaService)
    {
        $products = Product::all();        
        $pdf = Pdf::loadView('pdf.product_report', compact('products'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isPhpEnabled' => true,
                      'isRemoteEnabled' => true 
                  ]);

        $data = [
            'event' => 'pdf_report',
            'products' => $products
        ];

        $kafkaService->publishMessage('central-topic', $data, $products);
                  
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="product_report.pdf"',
        ]);
    }


}
