<!DOCTYPE html>
<html>
<head>
    <title>Employee Login</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <style>
        .error {
            color: red;
            font-size: 13px;
        }
    </style>
</head>
<body>

<h2>Login</h2>

<form id="loginForm" method="POST" action="{{ route('login.submit') }}">
    @csrf

    <input type="text" name="employee_code" id="employee_code" placeholder="Employee Code">
    <br><br>

    <input type="password" name="password" id="password" placeholder="Password">
    <br><br>

    <button type="submit">Login</button>
</form>

@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<script>
    $(function () {
        $("#loginForm").validate({
            rules: {
                employee_code: "required",
                password: {
                    required: true,
                    equalTo: "#employee_code"
                }
            },
            messages: {
                employee_code: "Enter employee code",
                password: {
                    required: "Enter password",
                    equalTo: "Password must match employee code"
                }
            }
        });
    });
</script>

</body>
</html>
