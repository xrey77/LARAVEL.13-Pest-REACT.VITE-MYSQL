<div>
<img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Logo">
<h2 style="font-family: Helvetica;">Products Report</h2>
<p style="font-family: Helvetica;margin-top:-15px;font-size:10px;">As of {{ now()->format('l, F j, Y') }}</p>
<table>
    <thead style="background-color: burlywood;">
      <tr style="border-width:1;border-style:solid;">
        <th style="font-family: Helvetica;" scope="col">#</th>
        <th style="font-family: Helvetica;" scope="col">Description</th>
        <th style="font-family: Helvetica;width: 90px;text-align:center;" scope="col">Stocks</th>
        <th style="font-family: Helvetica;" scope="col">CostPrice</th>
        <th style="font-family: Helvetica;" scope="col">SellPrice</th>
      </tr>      
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td style="font-family: Helvetica;">{{ $product->id}}</td>
                <td style="font-family: Helvetica;">{{ $product->descriptions }}</td>
                <td style="font-family: Helvetica;width: 90px;text-align:center;">{{ $product->qty }}</td>
                <td style="font-family: Helvetica;width: 90px;">${{ number_format($product->costprice, 2) }}</td>
                <td style="font-family: Helvetica;width: 90px;">${{ number_format($product->sellprice, 2) }}</td>
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


</div>