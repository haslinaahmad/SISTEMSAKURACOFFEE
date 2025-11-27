<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Monospace untuk kesan struk */
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .info-table { width: 100%; font-size: 10px; }
        .info-table td { padding: 2px 0; }
        .text-right { text-align: right; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th { text-align: left; border-bottom: 1px dashed #000; padding-bottom: 5px; font-size: 10px; }
        .items-table td { padding: 5px 0; font-size: 11px; vertical-align: top; }
        
        .totals { margin-top: 10px; width: 100%; font-size: 11px; }
        .totals td { padding: 2px 0; }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SAKURA COFFEE</h2>
        <p>No. 27, Jalan Bukit Bintang, 55100 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur, Malaysia.</p>
        <p>Telp: 0817-3457-7890</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td>No: {{ $transaction->invoice_number }}</td>
            <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ $transaction->user->name }}</td>
            <td class="text-right">Cust: {{ $transaction->customer_name }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%">Item</th>
                <th style="width: 20%">Qty</th>
                <th style="width: 40%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>x{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pajak (11%)</td>
            <td class="text-right">{{ number_format($transaction->tax, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold; font-size: 14px;">
            <td>TOTAL</td>
            <td class="text-right">{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="height: 5px;"></td>
        </tr>
        <tr>
            <td>Bayar ({{ $transaction->payment_method }})</td>
            <td class="text-right">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">{{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer">
        <p>Thank you for your visit!</p>
        <p>Follow IG: @sakuracoffee_my</p>
        <p style="margin-top:10px;">Password Wifi: sakuracoffee07</p>
    </div>
</body>
</html>