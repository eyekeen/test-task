<!DOCTYPE html>
<html>
<head>
    <title>New Order error</title>
</head>
<body>
    <h1>New Order error</h1>
    <p>A new order has been error with the following details:</p>
    <ul>
        <li><strong>Phone:</strong> {{ $order->phone }}</li>
        <li><strong>VIN:</strong> {{ $order->vin }}</li>
    </ul>
    <p>Thank you!</p>
</body>
</html>