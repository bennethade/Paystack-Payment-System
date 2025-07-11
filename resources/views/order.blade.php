<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="text-center">Payment Successful!</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Thank you for your order!</h4>
                            <p>Your payment was processed successfully.</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Order Summary</h5>
                                <ul class="list-group mb-4">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Reference:</span>
                                        <strong>{{ $order['reference'] }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Name:</span>
                                        <strong>{{ $order['name'] }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Email:</span>
                                        <strong>{{ $order['email'] }}</strong>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Details</h5>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Amount Paid:</span>
                                        <strong>₦{{ number_format($order['amount'], 2) }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Payment Method:</span>
                                        <strong>{{ ucfirst($order['channel']) }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Payment Date:</span>
                                        <strong>{{ \Carbon\Carbon::parse($order['paid_at'])->format('jS F Y, g:i a') }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="/" class="btn btn-primary">Goto Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>