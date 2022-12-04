<style type="text/css" media="screen">

    .container {
        margin: 0 1.5em;
        font-size: 0.9em;
        font-family: sans-serif;
    }

    .text-center {
        text-align: center !important;
    }
    .text-right {
        text-align: right;
    }

    p {
        text-align: justify;
        line-height: 1.5em;
        font-size: 0.9em;
    }

    table * {
        font-size: smaller;
    }
    table th,
    table td  {
        font-size: 0.7em;
    }
    .bordered{
        border: 1px solid #eee
    }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        border-top: 1px solid #ddd;
        line-height: 1.428571429;
        padding: 3px 8px!important;
        vertical-align: top;
    }
    li{
        margin-bottom: 1em;
    }
</style>
<div size="A4">
    <div class="container">
        <h5 class="text-center">{{Illuminate\Mail\Markdown::parse(Voyager::setting('data-export.pdf_title'))}}</h5>
        <ol>
            <li>
                <span>Hasil Penilaian</span>
                <div>
                    {{ Illuminate\Mail\Markdown::parse(Voyager::setting('data-export.pdf_content_first_begin'))}}
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-hover bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Uraian</th>
                                    <th class="text-center">NBV Konsolidasi<br/>Tahun {{$tahunSebelumnya}}</th>
                                    <th class="text-center">Estimasi NBV</th>
                                    <th class="text-center">Estimasi Dampak</th>
                                    <th class="text-center">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($final_summary as $summary_item)
                                @php
                                    $itemArray = explode(',', trim($summary_item->get_summary_v2, '()'));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="text-left">
                                            {{trim($itemArray[2], '""')}}
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
                                        <div class="text-right" style="color:{{$color}};" >
                                            @currency($itemArray[18]) %
                                        </div>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                            <thead>
                                <tr>
                                    <th class="text-left">Total</th>
                                    <th class="text-right">@currency($total_nbv_konsolidasi[0]->get_sum_column)</th>
                                    <th class="text-right">@currency($total_estimasi_nbv[0]->get_sum_column)</th>
                                    <th class="text-right">@currency($total_estimasi_dampak[0]->get_sum_column)</th>
                                    <th class="text-right">
                                        @currency($total_estimasi_dampak[0]->get_sum_column / $total_estimasi_nbv[0]->get_sum_column) %
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    {{ Illuminate\Mail\Markdown::parse(Voyager::setting('data-export.pdf_content_first_end'))}}
                </div>
            </li>
            <li>
                <span>Kesimpulan</span>
                <div>
                    {{ Illuminate\Mail\Markdown::parse(Voyager::setting('data-export.pdf_content_second'))}}
                </div>
            </li>
        </ol>
        <br />
        <div class="row">
            <div class="col" style="padding-left: 55%">
                <div class="text-center">{{ !empty($date) ? $date : (Voyager::setting('data-export.pdf_kota') .", " . date('d F Y', time())) }}</div>
                <div class="text-center">{{Voyager::setting('data-export.pdf_jabatan')}}</div>
                <div class="text-center">
                    <br/><br/><br/><br/>
                </div>
                <div class="text-center">{{$name}}</div>
            </div>
        </div>
    </div>
</div>
