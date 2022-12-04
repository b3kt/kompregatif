<?php

namespace App\Http\Controllers;

use App\Models\FlowUsulanInvestasi;
use Illuminate\Http\Request;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use App\Models\BpmMasterFlow;
use App\Models\BpmMasterTask;
use App\Models\BpmAppInstance;
use App\Models\BpmAppTask;
use App\Models\BpmMasterScreen;
use Route;

class FlowUsulanInvestasiController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function show(Request $request, $id)
    {

        $dataTypeContent = FlowUsulanInvestasi::whereId($id)->first();
        $dataType =  Voyager::model('DataType')->where('model_name', '=', get_class($dataTypeContent))->first();
        $masterFlow = BpmMasterFlow::whereModel(get_class($dataTypeContent))->first();
        $instance = BpmAppInstance::whereId($dataTypeContent->flow_instance_id)->first();
        $masterTask = BpmMasterTask::whereCode($instance->current_task)->first();
        $screen = BpmMasterScreen::whereTaskId($masterTask->id)->first();

        return Voyager::view('voyager::bpm-app-instances.flow-data-detail', compact(
            'instance',
            'dataType',
            'dataTypeContent',
            'masterFlow',
            'masterTask',
            'screen'
        ));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $isSubmission = $request->post('submission');

        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;
        $model = app($dataType->model_name);
        $query = $model->query();
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }
        $data = $query->findOrFail($id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Get fields with images to remove before updating and make a copy of $data
        $to_remove = $dataType->editRows->where('type', 'image')
            ->filter(function ($item, $key) use ($request) {
                return $request->hasFile($item->field);
            });
        $original_data = clone ($data);
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        // Delete Images
        $this->deleteBreadImages($original_data, $to_remove);
        event(new BreadDataUpdated($dataType, $data));

        if($isSubmission){
            //handle submission
            $this->handleSubmission($data);
            return redirect()->back()->with([
                'message'    => __('voyager::generic.successfully_submitted') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);

        }else{
            //save only
            return redirect()->back()->with([
                'message'    => __('voyager::generic.successfully_updated') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        }

    }

    private function handleSubmission($data){
        // complete current task
        $currentInstance = BpmAppInstance::whereId($data->flow_instance_id)->first();
        $currentTask = BpmAppTask::whereCurrentStatus('INITIATED')->whereAppInstanceId($currentInstance->id)->first();
        $masterTask = BpmMasterTask::whereId($currentTask->task_id)->first();

        $currentTask->current_status = 'COMPLETED';
        $currentTask->updated_at = now();
        $currentTask->save();

        // initiate next task
        $nextMasterTask = BpmMasterTask::whereCode($masterTask->next_task)->first();
        $nextTask = new BpmAppTask();
        $nextTask->app_instance_id = $currentInstance->id;
        $nextTask->task_id = $nextMasterTask->id;
        $nextTask->current_status = 'INITIATED';
        $nextTask->created_at = now();
        $nextTask->save();

        // update app instances
        $currentInstance->current_task = $nextTask->code;
        $currentInstance->save();

    }
}
