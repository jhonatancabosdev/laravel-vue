@if(config('voyager.show_dev_tips'))
    <div class="alert alert-info">
        <strong>{{ __('voyager::generic.how_to_use') }}:</strong>
        <p class="mb-0">{{ trans_choice('voyager::menu_builder.usage_hint', !empty($menu) ? 0 : 1) }} <code>menu('{{ !empty($menu) ? $menu->name : 'name' }}')</code></p>
    </div>
@endif
