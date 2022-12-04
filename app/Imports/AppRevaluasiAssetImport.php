<?php

namespace App\Imports;

use App\Models\AppRevaluasiAsset;
use Maatwebsite\Excel\Concerns\ToModel;

class AppRevaluasiAssetImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AppRevaluasiAsset([
            "year"                  => $row[0],
            "entitas"               => $row[1],
            "nilai_perolehan"       => $row[2],
            "akumulasi_penyusutan"  => $row[3],
            "reval"                 => $row[4],
            "uraian_akun"           => $row[5],
            "koefisien_idx"         => $row[6]
        ]);
    }
}
