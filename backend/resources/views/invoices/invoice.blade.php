<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:14px;
            color:#333;
        }

        .header{
            text-align:center;
            margin-bottom:30px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        td{
            padding:8px;
            border:1px solid #ddd;
        }

        .title{
            font-size:24px;
            font-weight:bold;
            margin-bottom:10px;
        }

        .footer{
            margin-top:40px;
            text-align:center;
            font-size:12px;
            color:#777;
        }
    </style>

</head>

<body>

<div class="header">

    <div class="title">

        Tena Adam Healthcare

    </div>

    <p>

        Invoice Receipt

    </p>

</div>

<table>

    <tr>

        <td><strong>Invoice Number</strong></td>

        <td>{{ $invoice->invoice_number }}</td>

    </tr>

    <tr>

        <td><strong>Patient</strong></td>

        <td>{{ $invoice->payment->patient->user->full_name ?? '' }}</td>

    </tr>

    <tr>

        <td><strong>Hospital</strong></td>

        <td>{{ $invoice->payment->hospital->name }}</td>

    </tr>

    <tr>

        <td><strong>Amount</strong></td>

        <td>

            {{ number_format($invoice->payment->amount,2) }}
            {{ $invoice->payment->currency }}

        </td>

    </tr>

    <tr>

        <td><strong>Status</strong></td>

        <td>{{ strtoupper($invoice->status) }}</td>

    </tr>

    <tr>

        <td><strong>Reference</strong></td>

        <td>{{ $invoice->payment->reference }}</td>

    </tr>

    <tr>

        <td><strong>Payment Date</strong></td>

        <td>{{ $invoice->payment->payment_date }}</td>

    </tr>

</table>

<div class="footer">

    Thank you for choosing Tena Adam Healthcare.

</div>

</body>

</html>