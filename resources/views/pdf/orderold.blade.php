<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order</title>

</head>
<body>


<div class="company-name">Your Company Name</div>

<div class="invoice-header">
    <div class="invoice-title">Invoice</div>
    <div class="invoice-meta">Date: 01/05/2023</div>
    <div class="invoice-meta">Invoice #: test</div>
</div>

<div class="billing-section">
    <h2 class="billing-title">Bill To:</h2>
    <div class="billing-info">test</div>
    <div class="billing-info">123 Main St.</div>
    <div class="billing-info">Anytown, USA 12345</div>
    <div class="billing-info">johndoe@example.com</div>
</div>

<table class="invoice-table">
    <thead>
    <tr>
        <th>Description</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Product 1</td>
        <td>1</td>
        <td>$100.00</td>
        <td>$100.00</td>
    </tr>
    <tr>
        <td>Product 2</td>
        <td>2</td>
        <td>$50.00</td>
        <td>$100.00</td>
    </tr>
    <tr>
        <td>Product 3</td>
        <td>3</td>
        <td>$75.00</td>
        <td>$225.00</td>
    </tr>
    </tbody>
</table>

<div class="totals-row">
    <div class="totals-label">Subtotal:</div>
    <div class="totals-value">$425.00</div>
</div>

<div class="totals-row right">
    <div class="totals-label">Tax:</div>
    <div class="totals-value">$25.50</div>
</div>

<div class="totals-row">
    <div class="totals-label">Total:</div>
    <div class="totals-value total-final">$450.50</div>
</div>

<div class="footer-section">
    <div class="footer-text">Payment is due within 30 days. Late payments are subject to fees.</div>
    <div class="footer-text">Please make checks payable to Your Company Name and mail to:</div>
    <div class="footer-text">123 Main St., Anytown, USA 12345</div>
</div>


<style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
        color: #374151; /* Tailwind's gray-700 */
    }

    .company-name {
        font-weight: 600;
        font-size: 1.125rem; /* text-lg */
        margin-bottom: 1rem;
    }

    .invoice-header {
        margin-bottom: 2rem;
    }

    .invoice-title {
        font-weight: bold;
        font-size: 1.25rem; /* text-xl */
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .invoice-meta {
        font-size: 0.875rem; /* text-sm */
        margin-bottom: 0.25rem;
    }

    .billing-section {
        border-bottom: 2px solid #D1D5DB; /* border-gray-300 */
        padding-bottom: 2rem;
        margin-bottom: 2rem;
    }

    .billing-title {
        font-size: 1.5rem; /* text-2xl */
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .billing-info {
        margin-bottom: 0.5rem;
    }

    .invoice-table {
        width: 100%;
        text-align: left;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }

    .invoice-table th {
        font-weight: bold;
        text-transform: uppercase;
        padding: 0.5rem 0;
        border-bottom: 1px solid #D1D5DB;
    }

    .invoice-table td {
        padding: 1rem 0;
        border-bottom: 1px solid #E5E7EB;
    }

    .totals-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 2rem;
        gap: 0.5rem;
    }

    .totals-row.right {
        text-align: right;
        flex-direction: column;
        align-items: flex-end;
    }

    .totals-label {
        margin-right: 0.5rem;
    }

    .totals-value {
        font-weight: normal;
    }

    .total-final {
        font-weight: bold;
        font-size: 1.25rem; /* text-xl */
    }

    .footer-section {
        border-top: 2px solid #D1D5DB;
        padding-top: 2rem;
        margin-bottom: 2rem;
    }

    .footer-text {
        margin-bottom: 0.5rem;
    }
</style>
</body>
</html>
