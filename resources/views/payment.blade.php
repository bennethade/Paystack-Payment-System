<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paystack Payment Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.25rem;
        }
        .verify-card .card-header {
            background-color: #1cc88a;
        }
        .btn-primary {
            background-color: #4e73df;
            border: none;
            padding: 10px;
            font-weight: 600;
        }
        .btn-verify {
            background-color: #1cc88a;
        }
        .form-control {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #d1d3e2;
        }
        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .container {
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .row.justify-content-center {
            min-height: 100vh;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-4">
                    <!-- Verify Account Card -->
                    <div class="col-md-6">
                        <div class="card verify-card h-100">
                            <div class="card-header">
                                <h4 class="text-center mb-0"><i class="fas fa-user-check me-2"></i>Verify Account Information</h4>
                            </div>
                            <div class="card-body">
                                <form id="verifyAccountForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="bank" class="form-label">Select Bank</label>
                                        <select class="form-select" id="bank" name="bank" required>
                                            <option value="" selected disabled>Loading banks...</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="account_number" class="form-label">Account Number</label>
                                        <input type="number" class="form-control" id="account_number" name="account_number" placeholder="Enter 10-digit account number" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-verify w-100">
                                        <i class="fas fa-check-circle me-2"></i>Verify Now
                                    </button>
                                    
                                    <!-- Result will be displayed here -->
                                    <div id="verificationResult" class="mt-3"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Card -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="text-center mb-0"><i class="fas fa-credit-card me-2"></i>Make Payment</h4>
                            </div>
                            <div class="card-body">
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form action="{{ route('pay') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Bennett Benard" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="example@gmail.com" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount (NGN)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₦</span>
                                            <input type="number" class="form-control" id="amount" name="amount" min="1" placeholder="1000" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-lock me-2"></i>Pay Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize elements
            const bankSelect = $('#bank');
            const accountInput = $('#account_number');
            const verifyForm = $('#verifyAccountForm');
            const verifyBtn = $('.btn-verify');
            const resultContainer = $('#verificationResult');
            
            // Fetch banks from Paystack when page loads
            fetchBanks();
            
            // Function to fetch banks with loading state
            function fetchBanks() {
                // Set loading state
                bankSelect.html('<option value="" selected disabled>Loading banks...</option>');
                bankSelect.prop('disabled', true);
                
                $.ajax({
                    url: "https://api.paystack.co/bank",
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer <?php echo env('PAYSTACK_SECRET_KEY'); ?>"
                    },
                    beforeSend: function() {
                        bankSelect.next('.select-loading').remove();
                        bankSelect.after('<div class="select-loading spinner-border spinner-border-sm text-primary ms-2"></div>');
                    },
                    success: function(response) {
                        if(response.status) {
                            let banks = response.data;
                            
                            // Clear and populate bank select
                            bankSelect.empty();
                            bankSelect.append('<option value="" selected disabled>Choose bank</option>');
                            
                            banks.forEach(bank => {
                                bankSelect.append(`<option value="${bank.code}">${bank.name}</option>`);
                            });
                            
                            // Sort banks alphabetically
                            bankSelect.html($('option', bankSelect).sort(function(a, b) {
                                return a.text == b.text ? 0 : a.text < b.text ? -1 : 1;
                            }));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching banks:", error);
                        bankSelect.html('<option value="" selected disabled>Failed to load banks. Click to retry.</option>');
                    },
                    complete: function() {
                        bankSelect.prop('disabled', false);
                        bankSelect.next('.select-loading').remove();
                    }
                });
            }
            
            // Retry bank loading if failed
            bankSelect.on('click', function() {
                if ($(this).find('option:disabled').text().includes('Failed')) {
                    fetchBanks();
                }
            });
            
            // Handle form submission
            verifyForm.on('submit', function(e) {
                e.preventDefault();
                
                const bankCode = bankSelect.val();
                const accountNumber = accountInput.val().trim();
                
                // Validate inputs
                if(!bankCode) {
                    showResult('Please select a bank', 'danger');
                    return;
                }
                
                if(!accountNumber || accountNumber.length < 10) {
                    showResult('Please enter a valid 10-digit account number', 'danger');
                    return;
                }
                
                // Verify account
                verifyAccount(bankCode, accountNumber);
            });
            
            // Function to verify account
            function verifyAccount(bankCode, accountNumber) {
                // Set loading state
                verifyBtn.prop('disabled', true);
                verifyBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Verifying...');
                clearResult();
                
                $.ajax({
                    url: "https://api.paystack.co/bank/resolve",
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer <?php echo env('PAYSTACK_SECRET_KEY'); ?>"
                    },
                    data: {
                        account_number: accountNumber,
                        bank_code: bankCode
                    },
                    success: function(response) {
                        if(response.status) {
                            showResult(`
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <div>
                                        <strong>Account Verified</strong>
                                        <div class="text-muted small">${response.data.account_name}</div>
                                    </div>
                                </div>
                            `, 'success');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Verification failed. Please check details and try again.';
                        showResult(`
                            <div class="d-flex align-items-center">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                <div>${errorMsg}</div>
                            </div>
                        `, 'danger');
                    },
                    complete: function() {
                        verifyBtn.prop('disabled', false);
                        verifyBtn.html('<i class="fas fa-check-circle me-2"></i>Verify Now');
                    }
                });
            }
            
            // Helper function to show results
            function showResult(message, type) {
                clearResult();
                resultContainer.html(`
                    <div class="alert alert-${type} alert-dismissible fade show">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
            }
            
            // Helper function to clear results
            function clearResult() {
                resultContainer.empty();
            }
            
            // Auto-tab between fields for better UX
            bankSelect.on('change', function() {
                if($(this).val()) {
                    accountInput.focus();
                }
            });
        });
    </script>

    <!-- Add Bootstrap JS for alert dismissals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>