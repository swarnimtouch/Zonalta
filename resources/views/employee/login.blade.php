<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zonalta - Employee Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* Same light blue gradient background */
            background: radial-gradient(circle at center, #dcedfb 0%, #b3d7f6 100%);
        }

        .login-card {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1.5px solid #204e8a;
            max-width: 420px;
            width: 100%;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        .login-title {
            color: #204e8a;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .form-label {
            color: #204e8a;
            font-size: 14px;
            font-weight: 600;
        }

        /* Custom Input Group to keep your specific icon design intact */
        .icon-input-wrapper {
            position: relative;
        }

        .icon-input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #204e8a;
            font-size: 14px;
        }

        .icon-input-wrapper .form-control {
            padding-left: 45px; /* Space for the icon */
            font-size: 14px;
            border-color: #cbd5e1;
        }

        .icon-input-wrapper .form-control:focus {
            border-color: #204e8a;
            box-shadow: 0 0 0 0.2rem rgba(32, 78, 138, 0.25); /* Bootstrap focus ring custom color */
        }

        .btn-submit {
            background-color: #3461a3;
            color: #ffffff;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #204e8a;
            color: #ffffff;
        }

        /* jQuery Validation Error Styling */
        label.error {
            color: #dc3545; /* Bootstrap danger color */
            font-size: 12px;
            margin-top: 4px;
            display: block;
            font-weight: 500;
        }
        
        .form-control.error {
            border-color: #dc3545;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 m-0 px-3">

    <div class="card login-card bg-white">
        
        <div class="card-header bg-transparent border-bottom px-4 pt-4 pb-3 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Zonalta Logo" class="logo mb-3" onerror="this.style.display='none'">
            <h2 class="login-title m-0">EMPLOYEE LOGIN</h2>
        </div>

        <div class="card-body p-4">
            
            @if(session('error'))
                <div class="alert alert-danger py-2 px-3 text-center" style="font-size: 14px;" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login.submit') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="employee_code">Employee Code</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-id-badge"></i>
                        <input type="text" name="employee_code" id="employee_code" class="form-control py-2" placeholder="Enter Employee Code">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control py-2" placeholder="Enter Password">
                    </div>
                </div>

                <button type="submit" class="btn btn-submit w-100 py-2">Log in</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#loginForm").validate({
                rules: {
                    employee_code: {
                        required: true
                    },
                    password: {
                        required: true,
                        equalTo: "#employee_code"
                    }
                },
                messages: {
                    employee_code: {
                        required: "Please enter your employee code"
                    },
                    password: {
                        required: "Please enter your password",
                        equalTo: "Password must match employee code"
                    }
                },
                errorPlacement: function(error, element) {
                    // Custom error placement under the wrapper div
                    error.insertAfter(element.parent(".icon-input-wrapper"));
                }
            });
        });
    </script>

</body>
</html>