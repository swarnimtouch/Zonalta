<!DOCTYPE html>
<html>
<head>
    <title>Import Data</title>
</head>
<body>

<h2>Import Employees</h2>
<form action="{{ route('import.employees') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Upload Employees</button>
</form>

<br><br>

<h2>Import Doctors</h2>
<form action="{{ route('import.doctors') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Upload Doctors</button>
</form>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

</body>
</html>
