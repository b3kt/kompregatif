<?php

namespace App\Imports;

use App\Models\AppRevalIHPB;
use Maatwebsite\Excel\Concerns\ToModel;

class AppRevalIHPBImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AppRevalIHPB([
            "year"  => $row[0],
            "month" => $row[1],
            "ihpb"  => $row[2]
        ]);
    }
}
