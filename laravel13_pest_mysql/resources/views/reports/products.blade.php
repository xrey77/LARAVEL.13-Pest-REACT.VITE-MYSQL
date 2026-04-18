<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .category-header { background: #f4f4f4; padding: 10px; margin-top: 20px; font-weight: bold; font-size: 19px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        .logo { width: 150px; height: 30px;}
    </style>
</head>
<body>
    <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Logo">
    <h2 style="font-family: Helvetica;">Products by Category Report</h2>
    <p style="font-family: Helvetica;margin-top:-15px;font-size:10px;">As of {{ now()->format('l, F j, Y') }}</p>
    @foreach($data as $category => $products)
        <div class="category-header">{{ $category }}</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Cost</th>
                    <th>Sell Price</th>
                </tr>
            </thead>
            <tbody>

                @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->descriptions }}</td>
                    <td>{{ $product->qty }}</td>
                    <td>{{ $product->unit }}</td>
                    <td>{{ number_format($product->costprice, 2) }}</td>
                    <td>{{ number_format($product->sellprice, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <script type="text/php">
                if (isset($pdf)) {
                    $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                    $font = $fontMetrics->get_font("helvetica", "bold");
                    $size = 10;
                    $color = array(0,0,0);
                    $word_space = 0.0;
                    $char_space = 0.0;
                    $angle = 0.0;
            
                    // Coordinates for footer: adjust $x and $y for your layout
                    $x = 250; // Center roughly for A4
                    $y = $pdf->get_height() - 35; // Distance from bottom
            
                    $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
                }
            </script>    
        </table>
    @endforeach
</body>
</html>
