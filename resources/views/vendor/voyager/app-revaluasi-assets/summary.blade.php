@extends('voyager::master')
{{-- @inject('revaluasiHelper', 'App\Helpers\RevaluasiHelper') --}}

@section('page_title', __('voyager::generic.viewing') . ' ' . $dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> Summary {{ $dataType->getTranslatedAttribute('display_name_plural') }}
            Tahun {{ $tahun }}
        </h1>
        @can('add', app($dataType->model_name))
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="voyager-news"></i> <span>Export</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" onclick="exportPDFForm()" class="btn btn-default btn-add-new">as PDF</a>
                    </li>
                    <li>
                        <a href="{{ route('voyager.' . $dataType->slug . '.summary', $tahun) . '/export/xlsx' }}"
                            class="btn btn-default btn-add-new">as Excel</a>
                    </li>

                </ul>
            </div>
        @endcan
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <div class="col-2">
                                        <select id="search_key" name="key">
                                            @foreach ($searchNames as $key => $name)
                                                <option value="{{ $key }}"
                                                    @if ($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)) selected @endif>{{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select id="filter" name="filter">
                                            <option value="contains" @if ($search->filter == 'contains') selected @endif>
                                                {{ __('voyager::generic.contains') }}</option>
                                            <option value="equals" @if ($search->filter == 'equals') selected @endif>=
                                            </option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('voyager::generic.search') }}" name="s"
                                            value="{{ $search->value }}">
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
                                        <th>Uraian</th>
                                        <th>NBV Konsolidasi Tahun {{ $tahunSebelumnya }}</th>
                                        <th>Estimasi NBV</th>
                                        <th>Estimasi Dampak</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($final_summary as $summary_item)
                                        @php
                                            $itemArray = explode(',', trim($summary_item->get_summary_v2, '()'));
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="text-left">
                                                    {{ trim($itemArray[2], '""') }}
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                @currency(trim($itemArray[3], '""'))
                                            </td>
                                            <td class="text-right">
                                                @currency(trim($itemArray[16], '""'))
                                            </td>
                                            <td class="text-right">
                                                @currency(trim($itemArray[17], '""'))
                                            </td>
                                            <td>
                                                @php
                                                    $color = $itemArray[18] > 7.5 || $itemArray[18] < -7.5 ? 'red' : '#76838f';
                                                @endphp
                                                <div class="text-right" style="color:{{ $color }};">
                                                    @currency($itemArray[18]) %
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                                <thead>
                                    <tr>
                                        {{-- <th class="text-left">Total</th>
                                        <th class="text-right">@currency($total_nbv_konsolidasi[0]->get_sum_column)</th>
                                        <th class="text-right">@currency($total_estimasi_nbv[0]->get_sum_column)</th>
                                        <th class="text-right">@currency($total_estimasi_dampak[0]->get_sum_column)</th>
                                        <th class="text-right">
                                            @if ($total_estimasi_nbv[0]->get_sum_column > 0)
                                            @currency($total_estimasi_dampak[0]->get_sum_column / $total_estimasi_nbv[0]->get_sum_column) %
                                            @else
                                            -
                                            @endif
                                        </th> --}}
                                    </tr>
                                </thead>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="get" action="{{ route('voyager.' . $dataType->slug . '.summary', $tahun) . '/export/pdf' }}" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Export to PDF</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Document Date</label>
                            <input type="text" class="form-control" id="exampleInputDate1" name="date" aria-describedby="dateHelp"
                                placeholder="Enter date">
                            <small id="dateHelp" class="form-text text-muted">Example: Jakarta, 26 Desember 2021</small>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1">Name</label>
                            <input type="text" class="form-control" id="exampleInputName1" placeholder="Name" name="name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
@stop

@section('javascript')
    <!-- DataTables -->
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script>
        function exportPDFForm() {
            $('#exampleModal').modal('show');
        }

        $(document).ready(function() {
            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge(
                        [
                            'paging' => false,
                            'ordering' => false,
                            'searching' => false,
                            'info' => false,
                            'order' => $orderColumn,
                            'language' => __('voyager::datatable'),
                            'columnDefs' => [['targets' => 'dt-not-orderable', 'searchable' => false, 'orderable' => false]],
                        ],
                        config('voyager.dashboard.data_tables', []),
                    ),
                    true,
                ) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function() {
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function(e) {
            $('#delete_form')[0].action = '{{ route('voyager.' . $dataType->slug . '.destroy', '__id') }}'.replace(
                '__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if ($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before(
                            '<a id="redir" href="{{ route('voyager.' . $dataType->slug . '.index', array_merge($params, ['showSoftDeleted' => 1]), true) }}"></a>'
                            );
                    } else {
                        $('#dataTable').before(
                            '<a id="redir" href="{{ route('voyager.' . $dataType->slug . '.index', array_merge($params, ['showSoftDeleted' => 0]), true) }}"></a>'
                            );
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
        $('input[name="row_id"]').on('change', function() {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });
    </script>
@stop
