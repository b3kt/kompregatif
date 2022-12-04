<?php

namespace App\Imports;

use App\Models\AppRevalAssetItem;
use Maatwebsite\Excel\Concerns\ToModel;

class AppRevalAssetItemImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AppRevalAssetItem([
            "title"                => $row[0],
            "active"               => $row[1]
        ]);
    }
}
