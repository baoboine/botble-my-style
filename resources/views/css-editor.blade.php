@if($isWriteable)
    {{ Form::textarea('my_custom_css', $css, ['class' => 'form-control', 'rows' => 3, 'id' => 'custom_css']) }}
    <input type="hidden" name="has-my-style" value="1"/>
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
@else
    <div class="alert alert-danger">
        {{ trans('packages/theme::theme.folder_is_not_writeable', ['name' => $path]) }}
    </div>
@endif
