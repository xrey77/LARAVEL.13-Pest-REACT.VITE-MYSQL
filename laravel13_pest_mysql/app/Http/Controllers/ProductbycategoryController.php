<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\KafkaProducerService;

class ProductbycategoryController extends Controller
{
    public function generateCategoryReport(KafkaProducerService $kafkaService)
    {
        $data = Product::all()->groupBy('category');
        $pdf = Pdf::loadView('reports.products', compact('data'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isPhpEnabled' => true,
                      'isRemoteEnabled' => true 
                  ]);

        $proddata = [
            'event' => 'category_report',
            'products' => $data
        ];

        $kafkaService->publishMessage('central-topic', $proddata, $data);        
        return $pdf->download('product-report.pdf');
    }
    
}
