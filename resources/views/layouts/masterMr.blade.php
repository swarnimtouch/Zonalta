<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Doctor Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    {{-- Custom Style --}}
    <style>
        body {
            background: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-wrapper {
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            min-height: 100vh;
            background: #1e293b;
            color: #fff;
            padding: 20px;
        }

        .sidebar h4 {
            font-weight: 600;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #334155;
            color: #fff;
        }

        /* Content */
        .content-area {
            flex: 1;
            padding: 20px;
        }

        /* Header */
        .topbar {
            background: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            height: 40px;
        }

        .user-box {
            font-size: 14px;
            color: #333;
        }

        /* Card */
        .card {
            border-radius: 14px;
            border: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }
    </style>

    @yield('header_css')
</head>

<body>

<div class="main-wrapper">



    {{-- MAIN CONTENT --}}
    <div class="content-area">



        @yield('content')

    </div>
</div>

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@yield('script')

</body>
</html>
