<?php

namespace App\Http\Controllers;

use App\Exports\BannerExport;
use App\Exports\EmployeeExport;
use App\Models\DoctorPoster;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.doctors.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $totalBanner  = DoctorPoster::count();
        $totalEmployee  = Employee::count();

        return view('admin.dashboard', compact('totalBanner','totalEmployee'));
    }

    public function banner(Request $request)
    {
        $banners = DoctorPoster::with('employee')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('degree', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('address', 'like', '%' . $request->search . '%')
                        ->orWhere('msl_code', 'like', '%' . $request->search . '%')
                        ->orWhereHas('employee', function ($q2) use ($request) {
                            $q2->where('employee_code', 'like', '%' . $request->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $banners->getCollection()->transform(function ($banner) {

            $banner->photo_url = $banner->photo
                ? Storage::disk('s3')->url($banner->photo)
                : null;

            $banner->banner_url = $banner->banner_path
                ? Storage::disk('s3')->url($banner->banner_path)
                : null;

            $banner->video_url = $banner->video_path
                ? Storage::disk('s3')->url($banner->video_path)
                : null;

            return $banner;
        });

        return view('admin.banner', compact('banners'));
    }

    public function banner_destroy($id)
    {
        $banner = DoctorPoster::findOrFail($id);

        // Delete files from S3 bucket
        if ($banner->photo && Storage::disk('s3')->exists($banner->photo)) {
            Storage::disk('s3')->delete($banner->photo);
        }

        if ($banner->banner_path && Storage::disk('s3')->exists($banner->banner_path)) {
            Storage::disk('s3')->delete($banner->banner_path);
        }

        if ($banner->video_path && Storage::disk('s3')->exists($banner->video_path)) {
            Storage::disk('s3')->delete($banner->video_path);
        }

        $banner->delete();

        return redirect()->route('admin.banner.index')
            ->with('success', 'Banner deleted successfully');
    }

    public function banner_export(Request $request)
    {
        return Excel::download(
            new BannerExport($request->search),
            'banner.xlsx'
        );
    }

//    public function doctor(Request $request)
//    {
//        $doctors = Doctor::with('employee')
//            ->when($request->search, function ($q) use ($request) {
//                $q->where('name', 'like', '%' . $request->search . '%')
//                    ->orWhere('msl_code', 'like', '%' . $request->search . '%')
//                    ->orWhere('degree', 'like', '%' . $request->search . '%')
//                    ->orWhere('city', 'like', '%' . $request->search . '%')
//                    ->orWhereHas('employee', function ($q2) use ($request) {
//                        $q2->where('employee_code', 'like', '%' . $request->search . '%');
//                    });
//            })
//            ->paginate(10);
//
//        return view('admin.doctors', compact('doctors'));
//    }
//
//    public function doctor_destroy($id)
//    {
//        $doctor = Doctor::findOrFail($id);
//        $doctor->delete();
//
//        return redirect()->route('admin.doctors.index')
//            ->with('success', 'Doctor deleted successfully');
//    }
//
//    public function doctor_export(Request $request)
//    {
//        return Excel::download(
//            new DoctorExport($request->search),
//            'doctor.xlsx'
//        );
//    }

    public function employee(Request $request)
    {
        $employees = Employee::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('employee_code', 'like', '%' . $request->search . '%')
                ->orWhere('position_code', 'like', '%' . $request->search . '%')
                ->orWhere('designation', 'like', '%' . $request->search . '%')
                ->orWhere('hq_name', 'like', '%' . $request->search . '%')
                ->orWhere('hq_code', 'like', '%' . $request->search . '%');
        })
            ->latest()
            ->paginate(10);

        return view('admin.employee', compact('employees'));
    }


    public function employee_destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully');
    }

    public function employee_export(Request $request)
    {
        return Excel::download(
            new EmployeeExport($request->search),
            'employee.xlsx'
        );
    }

}
