<!-- form start -->
<form role="form"
        class="form-edit-add"
        action="{{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->id)}}"
        method="POST" enctype="multipart/form-data">
    <!-- PUT Method if we are editing -->
    {{ method_field("POST") }}

    <!-- CSRF TOKEN -->
    {{ csrf_field() }}

    <div class="panel-body">


        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Adding / Editing -->
        @php
            $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
        @endphp

        @foreach($dataTypeRows as $row)

            <!-- GET THE DISPLAY OPTIONS -->
            @php
                $tasksDetails = $row->details;

                if(!property_exists($tasksDetails, 'tasks')){
                    continue;
                }elseif(!in_array($masterTask->code, $tasksDetails->tasks)){
                    continue;
                }

                $display_options = $row->details->display ?? NULL;
                if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                    $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                }
            @endphp
            @if (isset($row->details->legend) && isset($row->details->legend->text))
                <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
            @endif

            <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                {{ $row->slugify }}
                <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                @include('voyager::multilingual.input-hidden-bread-edit-add')


                @if ($add && isset($row->details->view_add))
                    @include($row->details->view_add, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'add', 'options' => $row->details])
                @elseif ($edit && isset($row->details->view_edit))
                    @include($row->details->view_edit, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'edit', 'options' => $row->details])
                @elseif (isset($row->details->view))
                    @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                @elseif ($row->type == 'relationship')
                    @include('voyager::formfields.relationship', ['options' => $row->details])
                @else
                    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                @endif

                @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                    {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                @endforeach
                @if ($errors->has($row->field))
                    @foreach ($errors->get($row->field) as $error)
                        <span class="help-block">{{ $error }}</span>
                    @endforeach
                @endif
            </div>
        @endforeach

    </div><!-- panel-body -->

    <div class="panel-footer text-right">
        @section('submit-buttons')
            <button type="submit" class="btn btn-primary save">{{ __('simanis.button.save') }}</button>
            <button type="submit" class="btn btn-primary submit" name="submission" value="true">{{ __('simanis.button.submit') }}</button>
        @stop
        @yield('submit-buttons')
    </div>
</form>
