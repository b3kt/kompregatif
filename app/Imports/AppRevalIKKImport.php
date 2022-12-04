<?php

namespace App\Imports;

use App\Models\AppRevalIKK;
use Maatwebsite\Excel\Concerns\ToModel;

class AppRevalIKKImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AppRevalIKK([
            "year" => $row[0],
            "provinsi" => $row[1],
            "ikk"  => $row[2]
        ]);
    }
}
