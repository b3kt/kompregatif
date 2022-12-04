<?php

namespace App\Helpers;

use App\Models\AppRevaluasiAsset;
use App\Models\AppRevalNJOP;

/**
 * DEPRECATED
 */

class RevaluasiHelper{

    // function getAppRevaluasiAsset($uraian_akun, $year, $entity){
    //     return AppRevaluasiAsset::where('uraian_akun', $uraian_akun)
    //             ->where('entitas',$entity)
    //             ->where('year', $year)
    //             ->first();
    // }


    // function getNBVByYear($uraian_akun, $year){
    //     $result = $this->getAppRevaluasiAsset($uraian_akun, $year, 'HOLDING');
    //     if(!empty($result)){
    //         return $result->nilai_perolehan - $result->nilai_penyusutan;
    //     }
    //     return null;
    // }

    // function getNBVATNonReval($uraian_akun, $year){
    //     $result = $this->getAppRevaluasiAsset($uraian_akun, $year, 'AP');
    //     if(!empty($result)){
    //         return $result->nilai_perolehan - $result->nilai_penyusutan;
    //     }
    //     return null;
    // }

    // function getNBVRevalOnly($uraian_akun, $year){
    //     $nbv = $this->getNBVByYear($uraian_akun, $year);
    //     $nbvNonReval = $this->getNBVATNonReval($uraian_akun, $year);
    //     return $nbv - $nbvNonReval;
    // }

    // function getMinYear(){
    //     return AppRevaluasiAsset::select('year')->distinct()->min('year');
    // }

    // function getTahunDasarAwalReval($uraian_akun){
    //     return $this->getNBVByYear($uraian_akun, $this->getMinYear());
    // }

    // function getPenambahan($uraian_akun, $year, $reval){
    //     $nbv = $this->getNBVByYear($uraian_akun, $year);
    //     return ($nbv * $reval) - $this->getTahunDasarAwalReval($uraian_akun);
    // }

    // function getAverageNJOPByYear($year){
    //     $result =  AppRevalNJOP::where('year',$year)
    //         ->avg('njop');
    //     return $result != null ? $result : 1;
    // }

    // function getENWAAPByYear($uraian_akun, $year){
    //     $awal = $this->getTahunDasarAwalReval($uraian_akun);
    //     $avgNjopAwal = $this->getAverageNJOPByYear($this->getMinYear()) / $this->getAverageNJOPByYear($year);
    //     return $awal * $avgNjopAwal;
    // }

    // function getSumENWAAP($uraian_akun, $year){
    //     $minyear = $this->getMinYear();
    //     $total = 0;
    //     for ($i = $minyear; $i <= $year; $i++) {
    //         $total += $this->getENWAAPByYear($uraian_akun, $i);
    //     }
    //     return $total;
    // }
}
