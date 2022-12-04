<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnumText;

class RevaluasiRelatedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EnumText::create(['code'=>'HOLDING',
            'name'=>'Holding',
            'label'=>'Holding',
            'value'=>'Holding',
            'notes'=>'',
            'group'=>'ENTITY',
        ]);

        EnumText::create(['code'=>'AP',
            'name'=>'AP',
            'label'=>'AP',
            'value'=>'AP',
            'notes'=>'',
            'group'=>'ENTITY',
        ]);


        EnumText::create([
            'code'=>'IHPB',
            'name'=>'IHPB',
            'label'=>'IHPB',
            'value'=>'IHPB',
            'notes'=>'',
            'group'=>'KOEFISIEN_IDX'
        ]);
        EnumText::create([
            'code'=>'PPI',
            'name'=>'PPI',
            'label'=>'PPI',
            'value'=>'PPI',
            'notes'=>'',
            'group'=>'KOEFISIEN_IDX'
        ]);
        EnumText::create([
            'code'=>'IKK',
            'name'=>'IKK',
            'label'=>'IKK',
            'value'=>'IKK',
            'notes'=>'',
            'group'=>'KOEFISIEN_IDX'
        ]);
        EnumText::create([
            'code'=>'NJOP',
            'name'=>'NJOP',
            'label'=>'NJOP',
            'value'=>'NJOP',
            'notes'=>'',
            'group'=>'KOEFISIEN_IDX'
        ]);
        EnumText::create([
            'code'=>'RCN',
             'name' =>'RCN',
             'label'=>'RCN',
             'value'=>'RCN',
             'notes'=>'',
             'group'=>'KOEFISIEN_IDX'
        ]);


        EnumText::create(['code'=>'aceh', 'name'=>'Aceh', 'value'=>'Aceh', 'label'=>'Aceh', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sumatera-utara', 'name'=>'Sumatera Utara', 'value'=>'Sumatera Utara', 'label'=>'Sumatera Utara', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sumatera-barat', 'name'=>'Sumatera Barat', 'value'=>'Sumatera Barat', 'label'=>'Sumatera Barat', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'riau', 'name'=>'Riau', 'value'=>'Riau', 'label'=>'Riau', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'jambi', 'name'=>'Jambi', 'value'=>'Jambi', 'label'=>'Jambi', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sumatera-selatan', 'name'=>'Sumatera Selatan', 'value'=>'Sumatera Selatan', 'label'=>'Sumatera Selatan', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'bengkulu', 'name'=>'Bengkulu', 'value'=>'Bengkulu', 'label'=>'Bengkulu', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'lampung', 'name'=>'Lampung', 'value'=>'Lampung', 'label'=>'Lampung', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'kep.-bangka-belitung', 'name'=>'Kep. Bangka Belitung', 'value'=>'Kep. Bangka Belitung', 'label'=>'Kep. Bangka Belitung', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'kep.-riau', 'name'=>'Kep. Riau', 'value'=>'Kep. Riau', 'label'=>'Kep. Riau', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'dki-jakarta', 'name'=>'DKI Jakarta', 'value'=>'DKI Jakarta', 'label'=>'DKI Jakarta', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'jawa-barat', 'name'=>'Jawa Barat', 'value'=>'Jawa Barat', 'label'=>'Jawa Barat', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'jawa-tengah', 'name'=>'Jawa Tengah', 'value'=>'Jawa Tengah', 'label'=>'Jawa Tengah', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'di-yogyakarta', 'name'=>'DI Yogyakarta', 'value'=>'DI Yogyakarta', 'label'=>'DI Yogyakarta', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'jawa-timur', 'name'=>'Jawa Timur', 'value'=>'Jawa Timur', 'label'=>'Jawa Timur', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'banten', 'name'=>'Banten', 'value'=>'Banten', 'label'=>'Banten', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'bali', 'name'=>'Bali', 'value'=>'Bali', 'label'=>'Bali', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'nusa-tenggara-barat', 'name'=>'Nusa Tenggara Barat', 'value'=>'Nusa Tenggara Barat', 'label'=>'Nusa Tenggara Barat', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'nusa-tenggara-timur', 'name'=>'Nusa Tenggara Timur', 'value'=>'Nusa Tenggara Timur', 'label'=>'Nusa Tenggara Timur', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'kalimantan-barat', 'name'=>'Kalimantan Barat', 'value'=>'Kalimantan Barat', 'label'=>'Kalimantan Barat', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'kalimantan-tengah', 'name'=>'Kalimantan Tengah', 'value'=>'Kalimantan Tengah', 'label'=>'Kalimantan Tengah', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'kalimantan-selatan', 'name'=>'Kalimantan Selatan', 'value'=>'Kalimantan Selatan', 'label'=>'Kalimantan Selatan', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'kalimantan-timur', 'name'=>'Kalimantan Timur', 'value'=>'Kalimantan Timur', 'label'=>'Kalimantan Timur', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'kalimantan-utara', 'name'=>'Kalimantan Utara', 'value'=>'Kalimantan Utara', 'label'=>'Kalimantan Utara', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sulawesi-utara', 'name'=>'Sulawesi Utara', 'value'=>'Sulawesi Utara', 'label'=>'Sulawesi Utara', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sulawesi-tengah', 'name'=>'Sulawesi Tengah', 'value'=>'Sulawesi Tengah', 'label'=>'Sulawesi Tengah', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sulawesi-selatan', 'name'=>'Sulawesi Selatan', 'value'=>'Sulawesi Selatan', 'label'=>'Sulawesi Selatan', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sulawesi-tenggara', 'name'=>'Sulawesi Tenggara', 'value'=>'Sulawesi Tenggara', 'label'=>'Sulawesi Tenggara', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'gorontalo', 'name'=>'Gorontalo', 'value'=>'Gorontalo', 'label'=>'Gorontalo', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'sulawesi-barat', 'name'=>'Sulawesi Barat', 'value'=>'Sulawesi Barat', 'label'=>'Sulawesi Barat', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'maluku', 'name'=>'Maluku', 'value'=>'Maluku', 'label'=>'Maluku', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'maluku-utara', 'name'=>'Maluku Utara', 'value'=>'Maluku Utara', 'label'=>'Maluku Utara', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'papua-barat', 'name'=>'Papua Barat', 'value'=>'Papua Barat', 'label'=>'Papua Barat', 'group'=>'PROVINSI']);
        EnumText::create(['code'=>'papua', 'name'=>'Papua', 'value'=>'Papua', 'label'=>'Papua', 'group'=>'PROVINSI']);


        EnumText::create(['code'=>'PLTA','name'=>'PLTA','label'=>'PLTA','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTB','name'=>'PLTB','label'=>'PLTB','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTD','name'=>'PLTD','label'=>'PLTD','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTG','name'=>'PLTG','label'=>'PLTG','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTGU','name'=>'PLTGU','label'=>'PLTGU','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTM','name'=>'PLTM','label'=>'PLTM','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTMG','name'=>'PLTMG','label'=>'PLTMG','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTMH','name'=>'PLTMH','label'=>'PLTMH','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTP','name'=>'PLTP','label'=>'PLTP','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTS','name'=>'PLTS','label'=>'PLTS','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);
        EnumText::create(['code'=>'PLTU','name'=>'PLTU','label'=>'PLTU','value'=>'label','notes'=>null,'group'=>'JENIS_PEMBANGKIT']);


        EnumText::create(['code'=>'JAN','name'=>'Januari','label'=>'Januari','value'=>'Januari','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'FEB','name'=>'Februari','label'=>'Februari','value'=>'Februari','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'MAR','name'=>'Maret','label'=>'Maret','value'=>'Maret','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'APR','name'=>'April','label'=>'April','value'=>'April','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'MEI','name'=>'Mei','label'=>'Mei','value'=>'Mei','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'JUN','name'=>'Juni','label'=>'Juni','value'=>'Juni','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'JUL','name'=>'Juli','label'=>'Juli','value'=>'Juli','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'AGU','name'=>'Agustus','label'=>'Agustus','value'=>'Agustus','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'SEP','name'=>'September','label'=>'September','value'=>'September','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'OKT','name'=>'Oktober','label'=>'Oktober','value'=>'Oktober','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'NOV','name'=>'November','label'=>'November','value'=>'November','notes'=>null,'group'=>'MONTH']);
        EnumText::create(['code'=>'DES','name'=>'Desember','label'=>'Desember','value'=>'Desember','notes'=>null,'group'=>'MONTH']);


        EnumText::create(['code'=>'2016','name'=>'2016','label'=>'2016','value'=>'2016','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2017','name'=>'2017','label'=>'2017','value'=>'2017','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2018','name'=>'2018','label'=>'2018','value'=>'2018','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2019','name'=>'2019','label'=>'2019','value'=>'2019','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2020','name'=>'2020','label'=>'2020','value'=>'2020','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2021','name'=>'2021','label'=>'2021','value'=>'2021','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2022','name'=>'2022','label'=>'2022','value'=>'2022','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2023','name'=>'2023','label'=>'2023','value'=>'2023','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2024','name'=>'2024','label'=>'2024','value'=>'2024','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2025','name'=>'2025','label'=>'2025','value'=>'2025','notes'=>null,'group'=>'YEAR']);
        EnumText::create(['code'=>'2026','name'=>'2026','label'=>'2026','value'=>'2026','notes'=>null,'group'=>'YEAR']);



        EnumText::create(['code'=>'A','name'=>'A','label'=>'A','value'=>'A','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'B','name'=>'B','label'=>'B','value'=>'B','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'C','name'=>'C','label'=>'C','value'=>'C','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'D','name'=>'D','label'=>'D','value'=>'D','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'E','name'=>'E','label'=>'E','value'=>'E','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'F','name'=>'F','label'=>'F','value'=>'F','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'G','name'=>'G','label'=>'G','value'=>'G','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'H','name'=>'H','label'=>'H','value'=>'H','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'I','name'=>'I','label'=>'I','value'=>'I','notes'=>null,'group'=>'KATEGORI']);
        EnumText::create(['code'=>'J','name'=>'J','label'=>'J','value'=>'J','notes'=>null,'group'=>'KATEGORI']);

        EnumText::create(['code'=>'MAX100','name'=>'100','label'=>'100','value'=>'100','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX200','name'=>'200','label'=>'200','value'=>'200','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX300','name'=>'300','label'=>'300','value'=>'300','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX400','name'=>'400','label'=>'400','value'=>'400','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX500','name'=>'500','label'=>'500','value'=>'500','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX600','name'=>'600','label'=>'600','value'=>'600','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX700','name'=>'700','label'=>'700','value'=>'700','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX800','name'=>'800','label'=>'800','value'=>'800','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX900','name'=>'900','label'=>'900','value'=>'900','notes'=>null,'group'=>'CAPACITY']);
        EnumText::create(['code'=>'MAX1000UP','name'=>'>1000','label'=>'>1000','value'=>'>1000','notes'=>null,'group'=>'CAPACITY']);


        EnumText::create(['code'=>'USA','name'=>'USA','label'=>'USA','value'=>'USA','notes'=>null,'group'=>'MANUFACTURER']);
        EnumText::create(['code'=>'CHINA','name'=>'China','label'=>'China','value'=>'China','notes'=>null,'group'=>'MANUFACTURER']);
        EnumText::create(['code'=>'MIXED','name'=>'Mixed','label'=>'Mixed','value'=>'Mixed','notes'=>null,'group'=>'MANUFACTURER']);
    }
}
