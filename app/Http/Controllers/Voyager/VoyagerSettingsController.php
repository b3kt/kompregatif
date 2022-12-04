<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerSettingsController as BaseVoyagerSettingsController;

class VoyagerSettingsController extends BaseVoyagerSettingsController
{
    public function update_value(Request $request)
    {
        $value = $request->value;

        // Check permission
        $this->authorize('edit', Voyager::model('Setting'));

        $setting = Voyager::model('Setting')->where('key','revaluasi.base_year')->first();

        if ($setting != null) {
            $setting->value = $value;
            $setting->save();
        }

        request()->flashOnly('setting_tab');

        return back()->with([
            'message'    => __('voyager::settings.successfully_saved'),
            'alert-type' => 'success',
        ]);
    }
}
