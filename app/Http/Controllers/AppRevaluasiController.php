<?php

namespace App\Http\Controllers;

use App\Exports\RevaluasiSummaryExport;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AppRevaluasiAsset;
use Illuminate\Support\Facades\Cache;
use App\Models\AppRevaluasiSummary;

class AppRevaluasiController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{

    /**
     * Get BREAD relations data.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function relation(Request $request)
    {
        $page = $request->input('page');
        $on_page = 50;


        $cacheResults = Cache::get($request->type);
        if (empty($cacheResults)) {


            $slug = $this->getSlug($request);

            $search = $request->input('search', false);
            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            $method = $request->input('method', 'add');

            $model = app($dataType->model_name);
            if ($method != 'add') {
                $model = $model->find($request->input('id'));
            }


            $this->authorize($method, $model);

            $rows = $dataType->{$method . 'Rows'};

            foreach ($rows as $key => $row) {
                if ($row->field === $request->input('type')) {
                    $options = $row->details;
                    $model = app($options->model);
                    $skip = $on_page * ($page - 1);

                    $additional_attributes = $model->additional_attributes ?? [];

                    // custom filter
                    if ($options->model == 'App\Models\EnumText') {
                        $model = $model->where('group', $options->group);
                    } else if ($options->model == 'TCG\Voyager\Models\Role') {
                        $model = $model->where('bpm_related', $options->bpm_related);
                    } else if ($options->model == 'TCG\Voyager\Models\MenuItem') {
                        $model = $model->whereNull('parent_id')->orderBy('title','asc');
                    }

                    // Apply local scope if it is defined in the relationship-options
                    if (isset($options->scope) && $options->scope != '' && method_exists($model, 'scope' . ucfirst($options->scope))) {
                        $model = $model->{$options->scope}();
                    }


                    // If search query, use LIKE to filter results depending on field label
                    if ($search) {
                        // If we are using additional_attribute as label
                        if (in_array($options->label, $additional_attributes)) {
                            $relationshipOptions = $model->get();
                            $relationshipOptions = $relationshipOptions->filter(function ($model) use ($search, $options) {
                                return stripos($model->{$options->label}, $search) !== false;
                            });
                            $total_count = $relationshipOptions->count();
                            $relationshipOptions = $relationshipOptions->forPage($page, $on_page);
                        } else {
                            $total_count = $model->where($options->label, 'LIKE', '%' . $search . '%')->count();
                            $relationshipOptions = $model->take($on_page)->skip($skip)
                                ->where($options->label, 'LIKE', '%' . $search . '%')
                                ->get();
                        }
                    } else {
                        $total_count = $model->count();
                        $relationshipOptions = $model->take($on_page)->skip($skip)->get();
                    }

                    $results = [];

                    if (!$row->required && !$search && $page == 1) {
                        $results[] = [
                            'id'   => '',
                            'text' => __('voyager::generic.none'),
                        ];
                    }

                    // Sort results
                    if (!empty($options->sort->field)) {
                        if (!empty($options->sort->direction) && strtolower($options->sort->direction) == 'desc') {
                            $relationshipOptions = $relationshipOptions->sortByDesc($options->sort->field);
                        } else {
                            $relationshipOptions = $relationshipOptions->sortBy($options->sort->field);
                        }
                    }

                    foreach ($relationshipOptions as $relationshipOption) {
                        $results[] = [
                            'id'   => $relationshipOption->{$options->key},
                            'text' => $relationshipOption->{$options->label},
                        ];
                    }

                    array_multisort(array_column($results, 'id'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $results);

                    Cache::put($request->type, $results);

                    return response()->json([
                        'results'    => $results,
                        'pagination' => [
                            'more' => ($total_count > ($skip + $on_page)),
                        ],
                    ]);
                }
            }

            // No result found, return empty array
            return response()->json([], 404);
        } else {
            $skip = $on_page * ($page - 1);

            return response()->json([
                'results'    => $cacheResults,
                'pagination' => [
                    'more' => (count($cacheResults) > ($skip + $on_page)),
                ],
            ]);
        }
    }


    public function summary(Request $request)
    {

        $tahun = $request->tahun;
        $final_summary = DB::select("SELECT public.get_summary_v2($tahun)");
        $total_nbv_konsolidasi = DB::select("SELECT public.get_sum_column($tahun, 'nbv_konsolidasi')");
        $total_estimasi_nbv = DB::select("SELECT public.get_sum_column($tahun, 'estimasi_nbv')");
        $total_estimasi_dampak = DB::select("SELECT public.get_sum_column($tahun, 'estimasi_dampak')");
        $total_presentase = DB::select("SELECT public.get_sum_column($tahun, 'presentase')");
        $tahunSebelumnya = $tahun - 1;

        $summaryFields = array(
            //'app_revaluasi_asset_belongsto_enum_text_relationship',
            'app_revaluasi_asset_belongsto_app_reval_asset_item_relationship'
        );

        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchNames = $dataType->browseRows->mapWithKeys(function ($row) {
                return [$row['field'] => $row->getTranslatedAttribute('display_name')];
            });
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            // $model = app($dataType->model_name);
            $model = app('App\Models\AppRevalAssetItem');
            $query = $model::select('*');
            // filter by url param (year)
            // $query->where('year', $tahun);


            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $query->{$dataType->scope}();
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%' . $search->value . '%';

                $searchField = $dataType->name . '.' . $search->key;
                if ($row = $this->findSearchableRelationshipRow($dataType->rows->where('type', 'relationship'), $search->key)) {
                    $query->whereIn(
                        $searchField,
                        $row->details->model::where($row->details->label, $search_filter, $search_value)->pluck('id')->toArray()
                    );
                } else {
                    if ($dataType->browseRows->pluck('field')->contains($search->key)) {
                        $query->where($searchField, $search_filter, $search_value);
                    }
                }
            }

            $row = $dataType->rows->where('field', $orderBy)->firstWhere('type', 'relationship');
            if ($orderBy && (in_array($orderBy, $dataType->fields()) || !empty($row))) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                if (!empty($row)) {
                    $query->select([
                        $dataType->name . '.*',
                        'joined.' . $row->details->label . ' as ' . $orderBy,
                    ])->leftJoin(
                        $row->details->table . ' as joined',
                        $dataType->name . '.' . $row->details->column,
                        'joined.' . $row->details->key
                    );
                }

                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        // Define list of columns that can be sorted server side
        $sortableColumns = $this->getSortableColumns($dataType->browseRows);

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.summary")) {
            $view = "voyager::$slug.summary";
        }

        return Voyager::view($view, compact(
            'final_summary',
            'total_nbv_konsolidasi',
            'total_estimasi_nbv',
            'total_estimasi_dampak',
            'total_presentase',
            'tahun',
            'tahunSebelumnya',
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortableColumns',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn'
            // 'summaryFields'
        ));
    }


    public function import()
    {
        // https://stackoverflow.com/questions/50680185/csv-upload-voyager

        // Excel::import(new UsersImport, 'users.xlsx');

        // return redirect('/')->with('success', 'All good!');
    }

    public function export(Request $request)
    {



        if($request->type == 'xlsx'){
            $summary = new RevaluasiSummaryExport();
            $summary->setCollection(AppRevaluasiSummary::
                select("title","nbv_konsolidasi","estimasi_nbv","estimasi_dampak","presentase")
                ->where("tahun", $request->tahun-1)->get());
            return \Maatwebsite\Excel\Facades\Excel::download($summary, "revaluasi-summary-".$request->tahun.".xlsx");

        }else if($request->type == 'pdf'){

            $kota = "Jakarta";
            $jabatan = "EXECUTIVE VICE PRESIDENT AKUNTASI";
            $ttd_url = null;
            $name = $request->get('name');
            $date = $request->get('date');

            $tahun = $request->tahun;
            $final_summary = DB::select("SELECT public.get_summary_v2($tahun)");
            $total_nbv_konsolidasi = DB::select("SELECT public.get_sum_column($tahun, 'nbv_konsolidasi')");
            $total_estimasi_nbv = DB::select("SELECT public.get_sum_column($tahun, 'estimasi_nbv')");
            $total_estimasi_dampak = DB::select("SELECT public.get_sum_column($tahun, 'estimasi_dampak')");
            $total_presentase = DB::select("SELECT public.get_sum_column($tahun, 'presentase')");
            $tahunSebelumnya = $tahun - 1;

            //----

            $summaryFields = array(
                //'app_revaluasi_asset_belongsto_enum_text_relationship',
                'app_revaluasi_asset_belongsto_app_reval_asset_item_relationship'
            );

            // GET THE SLUG, ex. 'posts', 'pages', etc.
            $slug = $this->getSlug($request);

            // GET THE DataType based on the slug
            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            // Check permission
            $this->authorize('browse', app($dataType->model_name));

            $getter = $dataType->server_side ? 'paginate' : 'get';
            $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

            // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
            if (strlen($dataType->model_name) != 0) {
                $model = app('App\Models\AppRevalAssetItem');
                $query = $model::select('*');

                if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                    $query->{$dataType->scope}();
                }

                // If a column has a relationship associated with it, we do not want to show that field
                $this->removeRelationshipField($dataType, 'browse');
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);

                // Replace relationships' keys for labels and create READ links if a slug is provided.
                $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
            } else {
                // If Model doesn't exist, get data from table name
                $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
                $model = false;
            }

            // Check if BREAD is Translatable
            $isModelTranslatable = is_bread_translatable($model);

            // Eagerload Relations
            $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

            //----

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('voyager::app-revaluasi-assets.report-pdf', compact(
                'final_summary',
                'total_nbv_konsolidasi',
                'total_estimasi_nbv',
                'total_estimasi_dampak',
                'total_presentase',
                'tahun',
                'tahunSebelumnya',
                'dataType',
                'dataTypeContent',
                'isModelTranslatable',
                'kota',
                'jabatan',
                'ttd_url',
                'name',
                'date'
            ));

            // return $pdf->stream('revaluasi-report.pdf',array("Attachment" => false));
            // exit(0);

            return $pdf->download('revaluasi-report-'.$tahun.'.pdf');

            // return Voyager::view('voyager::app-revaluasi-assets.report-pdf', compact(
            //     'final_summary',
            //     'total_nbv_konsolidasi',
            //     'total_estimasi_nbv',
            //     'total_estimasi_dampak',
            //     'total_presentase',
            //     'tahun',
            //     'tahunSebelumnya',
            //     'dataType',
            //     'dataTypeContent',
            //     'isModelTranslatable',
            //     'kota',
            //     'jabatan',
            //     'ttd_url',
            //     'name'
            // ));

        }

    }
}
