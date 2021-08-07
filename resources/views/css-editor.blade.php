{{ Form::textarea('my_custom_css', $css, ['class' => 'form-control', 'rows' => 3, 'id' => 'custom_css']) }}

@push('header')
    <style>
        #my_style {
            background: #ffdddd;
        }
        #my_style .CodeMirror {
            background: #fff1f1;
        }
    </style>
@endpush