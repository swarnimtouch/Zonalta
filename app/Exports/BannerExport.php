<?php

namespace App\Exports;

use App\Models\DoctorPoster;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BannerExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return DoctorPoster::with('employee')
            ->when($this->search, function ($q) {

                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('degree', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('msl_code', 'like', '%' . $this->search . '%')
                        ->orWhereHas('employee', function ($q2) {
                            $q2->where('employee_code', 'like', '%' . $this->search . '%')
                                ->orWhere('name', 'like', '%' . $this->search . '%');
                        });
                });

            })
            ->get()
            ->map(function ($item) {

                return [
                    $item->prefix ?? '-',
                    $item->name,
                    $item->msl_code,
                    $item->degree,
                    $item->phone,
                    $item->address,

                    $item->employee->name ?? '',
                    $item->employee->employee_code ?? '',
                    $item->employee->position_code ?? '',

                    $item->photo ? Storage::disk('s3')->url($item->photo) : '',
                    $item->banner_path ? Storage::disk('s3')->url($item->banner_path) : '',
                    $item->video_path ? Storage::disk('s3')->url($item->video_path) : '',

                    optional($item->created_at)
                        ->timezone('Asia/Kolkata')
                        ->format('d-m-Y h:i A'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Prefix',
            'Doctor Name',
            'MSL Code',
            'Degree',
            'Phone',
            'Address',
            'Employee Name',
            'Employee Code',
            'Position Code',
            'Photo URL',
            'Banner URL',
            'Video URL',
            'Created At',
        ];
    }
}
