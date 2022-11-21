@php
    $checked = false;
    $class = isset($options->class) && trim($options->class) !== '' 
        ? $options->class 
        : 'toggleswitch';
@endphp

@if(isset($dataTypeContent->{$row->field}) || old($row->field))
    @php
        $checked = old($row->field, $dataTypeContent->{$row->field});
    @endphp
@else
    @php
        $checked = isset($options->checked) && filter_var($options->checked, FILTER_VALIDATE_BOOLEAN) 
            ? true 
            : false;
    @endphp
@endif

@if(isset($options->on) && isset($options->off))
    <input type="checkbox" name="{{ $row->field }}" class="{{ $class }}"
    	data-toggle="toggle"
    	data-offstyle="pink"
        data-onstyle="default"
        data-on="{{ $options->on }}" {!! $checked ? 'checked="checked"' : '' !!}
        data-off="{{ $options->off }}">
@else
    <input type="checkbox" name="{{ $row->field }}" class="{{ $class }}"
        @if($checked) checked @endif>
@endif
