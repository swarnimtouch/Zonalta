<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

        :root {
            --primary:      #009ea3;
            --primary-soft: #e0f2f1;
            --card-bg:      #ffffff;
            --text-main:    #1e293b;
            --text-muted:   #64748b;
            --border-color: rgba(226, 232, 240, 0.6);
            --input-bg:     #f1f5f9;
            --input-focus:  #ffffff;
            --error:        #ef4444;
            --error-bg:     #fef2f2;
            --shadow-md:    0 10px 30px rgba(0,0,0,0.04);
        }

        body {
            background: linear-gradient(135deg, #cceff1 0%, #efd6ea 100%);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: var(--text-main);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-md);
        }

        .login-card .brand {
            color: var(--text-main);
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            background: var(--input-bg);
            border: 1.5px solid transparent;
            border-radius: 12px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .input-group:focus-within {
            border-color: var(--primary);
            background: var(--input-focus);
            box-shadow: 0 0 0 4px var(--primary-soft);
        }
        /* Error state on input-group */
        .input-group.field-error {
            border-color: var(--error) !important;
            background: var(--error-bg) !important;
            box-shadow: none !important;
        }

        .form-control {
            background: transparent;
            border: none;
            color: var(--text-main);
            padding: 14px 18px 14px 0;
            font-size: 0.95rem;
            box-shadow: none !important;
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:focus { background: transparent; color: var(--text-main); box-shadow: none; }

        .input-group-text {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 14px 18px;
        }

        /* ── Inline error message ── */
        .error-msg {
            display: none;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--error);
            animation: slideDown 0.2s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* jQuery validate puts label with class 'error' — we style it */
        label.error {
            display: flex !important;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--error);
            animation: slideDown 0.2s ease-out;
        }

        input[type="checkbox"] {
            accent-color: var(--primary);
            width: 16px; height: 16px;
            cursor: pointer;
        }
        .remember-label {
            margin-bottom: 0;
            text-transform: none;
            letter-spacing: normal;
            font-size: 0.9rem;
            color: var(--text-main);
            font-weight: 500;
            cursor: pointer;
        }

        .btn-login {
            padding: 14px 36px;
            background: linear-gradient(to right, #009ea3 0%, #b3569f 100%);
            color: #ffffff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            letter-spacing: 0.03em;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(179,86,159,0.25);
            width: 100%;
        }
        .btn-login:hover  { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(179,86,159,0.35); color: #fff; }
        .btn-login:active { transform: translateY(1px); }
    </style>
</head>
<body>

<div class="login-card">

    <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo"
             style="height:55px; display:block; margin:0 auto 12px auto; object-fit:contain;">
        Admin Panel
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 mb-3" style="font-size:0.875rem;">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form id="loginForm" action="{{ route('admin.login.submit') }}" method="POST" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <div class="input-group" id="wrap_email">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" id="email"
                       value="{{ old('email') }}"
                       class="form-control"
                       placeholder="admin@admin.com"
                       autocomplete="email" autofocus>
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group" id="wrap_password">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" id="password"
                       class="form-control"
                       placeholder="••••••••"
                       autocomplete="current-password">
                <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        {{-- Remember Me --}}
        <div class="mb-4 d-flex align-items-center gap-2">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" class="form-label remember-label">Remember me</label>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt me-2"></i> Login
        </button>

    </form>
</div>

<script>
    $(document).ready(function () {

        $('#loginForm').validate({
            errorElement: 'label',
            errorClass:   'error',

            // Error highlight — input-group ka border red karo
            highlight: function (element) {
                $('#wrap_' + element.name).addClass('field-error');
            },
            unhighlight: function (element) {
                $('#wrap_' + element.name).removeClass('field-error');
            },

            // Error message input ke baad insert karo (input-group ke baad)
            errorPlacement: function (error, element) {
                error.prepend('<i class="fas fa-exclamation-circle" style="font-size:11px;"></i> ');
                error.insertAfter(element.closest('.input-group'));
            },

            rules: {
                email: {
                    required: true,
                    email:    true
                },
                password: {
                    required:  true,
                    minlength: 6
                }
            },

            messages: {
                email: {
                    required: 'Email address is required',
                    email:    'Please enter a valid email address'
                },
                password: {
                    required:  'Password is required',
                    minlength: 'Password must be at least 6 characters'
                }
            }
        });

        // Clear error on input
        $('input').on('input', function () {
            $('#wrap_' + this.name).removeClass('field-error');
        });

    });

    function togglePassword() {
        const field   = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (field.type === 'password') {
            field.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

</body>
</html>
