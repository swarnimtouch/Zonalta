<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use App\Imports\DoctorImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        return view('import');
    }

    public function importEmployees(Request $request)
    {
        Excel::import(new EmployeeImport, $request->file('file'));

        return back()->with('success', 'Employees Imported Successfully');
    }

    public function importDoctors(Request $request)
    {
        Excel::import(new DoctorImport, $request->file('file'));

        return back()->with('success', 'Doctors Imported Successfully');
    }
}
