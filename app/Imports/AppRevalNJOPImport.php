<?php

namespace App\Imports;

use App\Models\AppRevalNJOP;
use Maatwebsite\Excel\Concerns\ToModel;

class AppRevalNJOPImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AppRevalNJOP([
            "year" => $row[0],
            "provinsi" => $row[1],
            "njop"  => $row[2]
        ]);
    }
}
