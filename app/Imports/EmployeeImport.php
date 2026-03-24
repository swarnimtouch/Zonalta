<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Employee([
            'name' => $row['name'],
            'employee_code' => $row['employee_code'],
            'position_code' => $row['position_code'],
            'user_position_code' => $row['user_position_code'],
            'designation' => $row['designation'],
            'hq_name' => $row['hq_name'],
            'hq_code' => $row['hq_code'],
        ]);
    }
}
