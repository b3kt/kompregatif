@php
    // exit(var_dump());

    $slug = str_replace('_', '-', $options->table);
    // $list = app($options->model)::where($options->column, request('id'))->get();

    // GET THE DataType based on the slug
    $dataType = Voyager::model('DataType')
        ->where('slug', '=', $slug)
        ->first();

    // Check permission
    // $this->authorize('browse', app($dataType->model_name));

    $getter = $dataType->server_side ? 'paginate' : 'get';

    // $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
    // $searchNames = [];
    // if ($dataType->server_side) {
    //     $searchNames = $dataType->browseRows->mapWithKeys(function ($row) {
    //         return [$row['field'] => $row->getTranslatedAttribute('display_name')];
    //     });
    // }

    // $orderBy = $request->get('order_by', $dataType->order_column);
    // $sortOrder = $request->get('sort_order', $dataType->order_direction);
    $usesSoftDeletes = false;
    $showSoftDeleted = false;

    // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
    if (strlen($dataType->model_name) != 0) {
        $model = app($dataType->model_name);

        $query = $model::select($dataType->name . '.*');

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
        // $this->removeRelationshipField($dataType, 'browse');

        // if ($search->value != '' && $search->key && $search->filter) {
        //     $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
        //     $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';

        //     $searchField = $dataType->name.'.'.$search->key;
        //     if ($row = $this->findSearchableRelationshipRow($dataType->rows->where('type', 'relationship'), $search->key)) {
        //         $query->whereIn(
        //             $searchField,
        //             $row->details->model::where($row->details->label, $search_filter, $search_value)->pluck('id')->toArray()
        //         );
        //     } else {
        //         if ($dataType->browseRows->pluck('field')->contains($search->key)) {
        //             $query->where($searchField, $search_filter, $search_value);
        //         }
        //     }
        // }

        $orderBy = 'id';
        $row = $dataType->rows->where('field', $orderBy)->firstWhere('type', 'relationship');
        // if ($orderBy && (in_array($orderBy, $dataType->fields()) || !empty($row))) {
        //     $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
        //     if (!empty($row)) {
        //         $query->select([
        //             $dataType->name.'.*',
        //             'joined.'.$row->details->label.' as '.$orderBy,
        //         ])->leftJoin(
        //             $row->details->table.' as joined',
        //             $dataType->name.'.'.$row->details->column,
        //             'joined.'.$row->details->key
        //         );
        //     }

        //     $dataTypeContent = call_user_func([
        //         $query->orderBy($orderBy, $querySortOrder),
        //         $getter,
        //     ]);
        // } else
        if ($model->timestamps) {
            // exit(json_encode($options->column));
            $dataTypeContent = call_user_func([$query->where($options->column, request('id'))->latest($model::CREATED_AT), $getter]);
        } else {
            $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
    // $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
} else {
    // If Model doesn't exist, get data from table name
        $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
        $model = false;
    }

    // Check if BREAD is Translatable
    $isModelTranslatable = is_bread_translatable($model);

    // Eagerload Relations
    // $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

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
    // $orderColumn = [];
    // if ($orderBy) {
    //     $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
    //     $orderColumn = [[$index, $sortOrder ?? 'desc']];
    // }

    // Define list of columns that can be sorted server side
    // $sortableColumns = $this->getSortableColumns($dataType->browseRows);

    // $view = 'voyager::bread.browse';

    // if (view()->exists("voyager::$slug.browse")) {
    //     $view = "voyager::$slug.browse";
    // }

@endphp

@include('voyager::alerts')

@if ($isServerSide)
    <form method="get" class="form-search">
        <div id="search-input">
            <div class="col-2">
                <select id="search_key" name="key">
                    @foreach ($searchNames as $key => $name)
                        <option value="{{ $key }}" @if ($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)) selected @endif>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                <select id="filter" name="filter">
                    <option value="contains" @if ($search->filter == 'contains') selected @endif>
                        {{ __('voyager::generic.contains') }}</option>
                    <option value="equals" @if ($search->filter == 'equals') selected @endif>=</option>
                </select>
            </div>
            <div class="input-group col-md-12">
                <input type="text" class="form-control" placeholder="{{ __('voyager::generic.search') }}"
                    name="s" value="{{ $search->value }}">
                <span class="input-group-btn">
                    <button class="btn btn-info btn-lg" type="submit">
                        <i class="voyager-search"></i>
                    </button>
                </span>
            </div>
        </div>
        @if (Request::has('sort_order') && Request::has('order_by'))
            <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
            <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
        @endif
    </form>
@endif
<div class="table-responsive">
    <table id="dataTable" class="table table-hover">
        <thead>
            <tr>
                @if ($showCheckboxColumn)
                    <th class="dt-not-orderable">
                        <input type="checkbox" class="select_all">
                    </th>
                @endif
                @foreach ($dataType->browseRows as $row)
                    <th>
                        @if ($isServerSide && in_array($row->field, $sortableColumns))
                            <a href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">
                        @endif
                        {{ $row->getTranslatedAttribute('display_name') }}
                        @if ($isServerSide)
                            @if ($row->isCurrentSortField($orderBy))
                                @if ($sortOrder == 'asc')
                                    <i class="voyager-angle-up pull-right"></i>
                                @else
                                    <i class="voyager-angle-down pull-right"></i>
                                @endif
                            @endif
                            </a>
                        @endif
                    </th>
                @endforeach
                <th class="actions text-right dt-not-orderable">{{ __('voyager::generic.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataTypeContent as $data)
                <tr>
                    @if ($showCheckboxColumn)
                        <td>
                            <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}"
                                value="{{ $data->getKey() }}">
                        </td>
                    @endif
                    @foreach ($dataType->browseRows as $row)
                        @php
                            if ($data->{$row->field . '_browse'}) {
                                $data->{$row->field} = $data->{$row->field . '_browse'};
                            }
                        @endphp
                        <td>
                            @if (isset($row->details->view_browse))
                                @include($row->details->view_browse, [
                                    'row' => $row,
                                    'dataType' => $dataType,
                                    'dataTypeContent' => $dataTypeContent,
                                    'content' => $data->{$row->field},
                                    'view' => 'browse',
                                    'options' => $row->details,
                                ])
                            @elseif (isset($row->details->view))
                                @include($row->details->view, [
                                    'row' => $row,
                                    'dataType' => $dataType,
                                    'dataTypeContent' => $dataTypeContent,
                                    'content' => $data->{$row->field},
                                    'action' => 'browse',
                                    'view' => 'browse',
                                    'options' => $row->details,
                                ])
                            @elseif($row->type == 'image')
                                <img src="@if (!filter_var($data->{$row->field}, FILTER_VALIDATE_URL)) {{ Voyager::image($data->{$row->field}) }}@else{{ $data->{$row->field} }} @endif"
                                    style="width:100px">
                            @elseif($row->type == 'relationship')
                                @include('voyager::formfields.relationship', [
                                    'view' => 'browse',
                                    'options' => $row->details,
                                ])
                            @elseif($row->type == 'select_multiple')
                                @if (property_exists($row->details, 'relationship'))
                                    @foreach ($data->{$row->field} as $item)
                                        {{ $item->{$row->field} }}
                                    @endforeach
                                @elseif(property_exists($row->details, 'options'))
                                    @if (!empty(json_decode($data->{$row->field})))
                                        @foreach (json_decode($data->{$row->field}) as $item)
                                            @if (@$row->details->options->{$item})
                                                {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                            @endif
                                        @endforeach
                                    @else
                                        {{ __('voyager::generic.none') }}
                                    @endif
                                @endif
                            @elseif($row->type == 'multiple_checkbox' && property_exists($row->details, 'options'))
                                @if (@count(json_decode($data->{$row->field}, true)) > 0)
                                    @foreach (json_decode($data->{$row->field}) as $item)
                                        @if (@$row->details->options->{$item})
                                            {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                        @endif
                                    @endforeach
                                @else
                                    {{ __('voyager::generic.none') }}
                                @endif
                            @elseif(($row->type == 'select_dropdown' || $row->type == 'radio_btn') && property_exists($row->details, 'options'))
                                {!! $row->details->options->{$data->{$row->field}} ?? '' !!}
                            @elseif($row->type == 'date' || $row->type == 'timestamp')
                                @if (property_exists($row->details, 'format') && !is_null($data->{$row->field}))
                                    {{ \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($row->details->format) }}
                                @else
                                    {{ $data->{$row->field} }}
                                @endif
                            @elseif($row->type == 'checkbox')
                                @if (property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                    @if ($data->{$row->field})
                                        <span class="label label-info">{{ $row->details->on }}</span>
                                    @else
                                        <span class="label label-primary">{{ $row->details->off }}</span>
                                    @endif
                                @else
                                    {{ $data->{$row->field} }}
                                @endif
                            @elseif($row->type == 'color')
                                <span class="badge badge-lg"
                                    style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
                            @elseif($row->type == 'text')
                                @include('voyager::multilingual.input-hidden-bread-browse')
                                <div>
                                    {{ mb_strlen($data->{$row->field}) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}
                                </div>
                            @elseif($row->type == 'text_area')
                                @include('voyager::multilingual.input-hidden-bread-browse')
                                <div>
                                    {{ mb_strlen($data->{$row->field}) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}
                                </div>
                            @elseif($row->type == 'file' && !empty($data->{$row->field}))
                                @include('voyager::multilingual.input-hidden-bread-browse')
                                @if (json_decode($data->{$row->field}) !== null)
                                    @foreach (json_decode($data->{$row->field}) as $file)
                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}"
                                            target="_blank">
                                            {{ $file->original_name ?: '' }}
                                        </a>
                                        <br />
                                    @endforeach
                                @else
                                    <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}"
                                        target="_blank">
                                        {{ __('voyager::generic.download') }}
                                    </a>
                                @endif
                            @elseif($row->type == 'rich_text_box')
                                @include('voyager::multilingual.input-hidden-bread-browse')
                                <div>
                                    {{ mb_strlen(strip_tags($data->{$row->field}, '<b><i><u>')) > 200 ? mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}
                                </div>
                            @elseif($row->type == 'coordinates')
                                @include('voyager::partials.coordinates-static-image')
                            @elseif($row->type == 'multiple_images')
                                @php $images = json_decode($data->{$row->field}); @endphp
                                @if ($images)
                                    @php $images = array_slice($images, 0, 3); @endphp
                                    @foreach ($images as $image)
                                        <img src="@if (!filter_var($image, FILTER_VALIDATE_URL)) {{ Voyager::image($image) }}@else{{ $image }} @endif"
                                            style="width:50px">
                                    @endforeach
                                @endif
                            @elseif($row->type == 'media_picker')
                                @php
                                    if (is_array($data->{$row->field})) {
                                        $files = $data->{$row->field};
                                    } else {
                                        $files = json_decode($data->{$row->field});
                                    }
                                @endphp
                                @if ($files)
                                    @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                        @foreach (array_slice($files, 0, 3) as $file)
                                            <img src="@if (!filter_var($file, FILTER_VALIDATE_URL)) {{ Voyager::image($file) }}@else{{ $file }} @endif"
                                                style="width:50px">
                                        @endforeach
                                    @else
                                        <ul>
                                            @foreach (array_slice($files, 0, 3) as $file)
                                                <li>{{ $file }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (count($files) > 3)
                                        {{ __('voyager::media.files_more', ['count' => count($files) - 3]) }}
                                    @endif
                                @elseif (is_array($files) && count($files) == 0)
                                    {{ trans_choice('voyager::media.files', 0) }}
                                @elseif ($data->{$row->field} != '')
                                    @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                        <img src="@if (!filter_var($data->{$row->field}, FILTER_VALIDATE_URL)) {{ Voyager::image($data->{$row->field}) }}@else{{ $data->{$row->field} }} @endif"
                                            style="width:50px">
                                    @else
                                        {{ $data->{$row->field} }}
                                    @endif
                                @else
                                    {{ trans_choice('voyager::media.files', 0) }}
                                @endif
                            @else
                                @include('voyager::multilingual.input-hidden-bread-browse')
                                <span>{{ $data->{$row->field} }}</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="no-sort no-click bread-actions">
                        @foreach ($actions as $action)
                            @if (!method_exists($action, 'massAction'))
                                @include('voyager::bread.partials.actions', ['action' => $action])
                            @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if ($isServerSide)
    <div class="pull-left">
        <div role="status" class="show-res" aria-live="polite">
            {{ trans_choice('voyager::generic.showing_entries', $dataTypeContent->total(), [
                'from' => $dataTypeContent->firstItem(),
                'to' => $dataTypeContent->lastItem(),
                'all' => $dataTypeContent->total(),
            ]) }}
        </div>
    </div>
    <div class="pull-right">
        {{ $dataTypeContent->appends([
                's' => $search->value,
                'filter' => $search->filter,
                'key' => $search->key,
                'order_by' => $orderBy,
                'sort_order' => $sortOrder,
                'showSoftDeleted' => $showSoftDeleted,
            ])->links() }}
    </div>
@endif



{{-- Single delete modal --}}
<div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }}
                    {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
            </div>
            <div class="modal-footer">
                <form action="#" id="delete_form" method="POST">
                    {{ method_field('DELETE') }}
                    {{ csrf_field() }}
                    <input type="submit" class="btn btn-danger pull-right delete-confirm"
                        value="{{ __('voyager::generic.delete_confirm') }}">
                </form>
                <button type="button" class="btn btn-default pull-right"
                    data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
