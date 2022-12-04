<?php

namespace App\Imports;

use App\Models\AppRevalRCN;
use Maatwebsite\Excel\Concerns\ToModel;

class AppRevalRCNImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AppRevalRCN([
            "year"          => $row[0],
            "category"      => $row[1],
            "capacity"      => $row[2],
            "manufacturer"  => $row[3],
            "type"          => $row[4],
            "rcn"           => $row[5]
        ]);
    }
}
