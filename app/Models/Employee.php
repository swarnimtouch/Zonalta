<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $fillable = [
        'name',
        'employee_code',
        'position_code',
        'user_position_code',
        'designation',
        'hq_name',
        'hq_code'
    ];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

}
