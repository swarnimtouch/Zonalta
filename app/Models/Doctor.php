<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    //
    protected $table = 'msl_doctor';

    protected $fillable = [
        'employee_id',
        'name',
        'msl_code',
        'degree',
        'city'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
