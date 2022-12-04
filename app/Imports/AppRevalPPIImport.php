<?php

namespace App\Imports;

use App\Models\AppRevalPPI;
use Maatwebsite\Excel\Concerns\ToModel;

class AppRevalPPIImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AppRevalPPI([
            "year" => $row[0],
            "provinsi" => $row[1],
            "ppi"  => $row[2]
        ]);
    }
}
