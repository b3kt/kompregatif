<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BpmRole;
use App\Models\BpmMasterFlow;
use App\Models\BpmMasterTask;
use App\Models\BpmMasterScreen;
use TCG\Voyager\Models\Role;

class BPMRelatedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Role::create([
            'name' => 'Staff',
            'display_name' => 'Staff',
            'code' => 'STAF',
            'bpm_related' => true
        ]);


        Role::create([
            'name' => 'Manager',
            'display_name' => 'Manager',
            'code' => 'MSB',
            'bpm_related' => true
        ]);

        $flow = BpmMasterFlow::create([
            'name'           => 'Review Monitoring Usulan Investasi',
            'code'           => 'RMUI',
            'model'          => 'App\Models\FlowUsulanInvestasi',
            'description'    => true,
        ]);


        $task1 = BpmMasterTask::create([
            'name'           => 'Step #1 - Input Data',
            'code'           => 'RMUI_01_00',
            'description'    => 'Input Data',
            'role'           => 'STAF',
            'flow_id'        => $flow->id,
            'next_task'      => 'RMUI_02_00',
            'initial_task'   => true

        ]);

        BpmMasterScreen::create([
            'name'           => 'Step #1 - Input Data',
            'code'           => 'SCR_RMUI_01_00',
            'task_id'        => $task1->id,
            'view_name'      => 'rmui/screen-1'
        ]);


        BpmMasterTask::create([
            'name'           => 'Step #2 - Pengecekan Kelengkapan',
            'code'           => 'RMUI_02_00',
            'description'    => 'Pengecekan Kelengkapan',
            'role'           => 'STAF',
            'flow_id'        => $flow->id,
            'next_task'      => null,
            'initial_task'   => false
        ]);



        BpmMasterTask::create([
            'name'           => 'Step #2.1 - Revisi Kelengkapan',
            'code'           => 'RMUI_02_01',
            'description'    => 'Revisi Kelengkapan',
            'role'           => 'STAF',
            'flow_id'        => $flow->id,
            'next_task'      => 'RMUI_02_02',
            'initial_task'   => false
        ]);


        BpmMasterTask::create([
            'name'           => 'Step #2.2 - Konfirmasi Kelengkapan',
            'code'           => 'RMUI_02_02',
            'description'    => 'Konfirmasi Kelengkapan',
            'role'           => 'STAF',
            'flow_id'        => $flow->id,
            'next_task'      => 'RMUI_02_00',
            'initial_task'   => false
        ]);



        BpmMasterTask::create([
            'name'           => 'Step #3 - Review Laporan',
            'code'           => 'RMUI_03_00',
            'description'    => 'Review Laporan',
            'role'           => 'MSB',
            'flow_id'        => $flow->id,
            'next_task'      => 'RMUI_04_00',
            'initial_task'   => false
        ]);



        BpmMasterTask::create([
            'name'           => 'Step #4 - Penyampaian',
            'code'           => 'RMUI_04_00',
            'description'    => 'Penyampaian',
            'role'           => 'STAF',
            'flow_id'        => $flow->id,
            'next_task'      => 'RMUI_05_00',
            'initial_task'   => false
        ]);


        BpmMasterTask::create([
            'name'           => 'Step #5 - Cetakan Dokumen',
            'code'           => 'RMUI_05_00',
            'description'    => 'Cetakan Dokumen',
            'role'           => 'STAF',
            'flow_id'        => $flow->id,
            'next_task'      => null,
            'initial_task'   => false
        ]);
    }
}
