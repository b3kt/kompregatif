<?php

namespace App\Http\Controllers;

use App\Asset;
use App\Models\PermissionMenu;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Role;

class AssetsController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    //
    public function show(Request $request, $code)
    {
        $asset = Asset::whereDashboardCode($code)->first();
        if(!empty($asset)){
            $id = $asset->id;
        }

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $isSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model->query();

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $query = $query->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $query->{$dataType->scope}();
            }
            if(!empty($id)){
                $dataTypeContent = call_user_func([$query, 'findOrFail'], $id);
                if ($dataTypeContent->deleted_at) {
                    $isSoftDeleted = true;
                }
            }
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('code', $id)->first();
        }

        if(!empty($dataTypeContent)){
            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'read');

            // Check permission
            $this->authorize('read', $dataTypeContent);

            // Check if BREAD is Translatable
            $isModelTranslatable = is_bread_translatable($dataTypeContent);

            // Eagerload Relations
            $this->eagerLoadRelations($dataTypeContent, $dataType, 'read', $isModelTranslatable);
        }else{
            return Voyager::view('404');
        }

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted'));
    }

    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        $this->createMenuUnder($request);

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

    private function createMenuUnder($data){

        //create menu
        $menu = new MenuItem();
        $menu->title = $data->post('name');
        $menu->parent_id = $data->post('parent_menu_item_id');
        $menu->menu_id = 2;
        $menu->target = '_self';
        $menu->icon_class = 'voyager-double-right';
        $menu->color = '#000000';
        $menu->order = '99';
        $menu->url = '/admin/assets/'.$data->post('dashboard_code');
        if($menu->save()){

            //add to current permission
            $perm = PermissionMenu::where('role_id', Auth::user()->role->id)->first();
            if($perm == null){
                $role = Role::where('name','RA')->first();
                $perm = new PermissionMenu();
                $perm->menu_item_id = $menu->id;
                $perm->role_id = $role->id;
                $perm->save();
            }
        }

    }
}
