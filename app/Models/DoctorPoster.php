<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorPoster extends Model
{
    protected $fillable = [
        'employee_id',
//        'doctor_id',
        'name',
        'msl_code',
        'degree',
        'phone',
        'address',
        'photo',
        'banner_path',
        'video_path'
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
