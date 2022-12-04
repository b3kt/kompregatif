<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

class RevaluasiSummaryExport implements FromCollection
{
    private $list;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->list;
    }

    public function setCollection($list)
    {
        return $this->list = $list;
    }
}
