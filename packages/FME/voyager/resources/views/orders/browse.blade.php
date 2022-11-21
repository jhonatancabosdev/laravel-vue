@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="h3 mb-0 {{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div id="orders-table" class="container-fluid pb-5">
        <div class="row">
            <div class="col-12">
                <div class="col-12 page-content browse">

                    <div class="row">
                        <div class="col-12">
                            @include('voyager::alerts')
                        </div>
                    </div>

                    <orders-table 
                        class="pb-3"
                        :start-page="{{ request()->input('page', 1) < 1 ? 1 : request()->input('page', 1) }}"
                        :order-status="{{ json_encode($orderStatus) }}"
                        data-route="{{ route('voyager.orders.index') }}"
                        edit-order-route="{{ route('voyager.orders.edit', '__id') }}"
                        update-column-route="{{ route('voyager.orders.update-column', ['__id', '__column']) }}"
                        delete-order-route="{{ route('voyager.orders.destroy', '__id') }}"
                        notify-order-route="{{ route('voyager.mail.order') }}"
                        refund-order-route="{{ route('voyager.refund.order', '__id') }}"
                        export-order-route="{{ route('voyager.export.order') }}"
                    >
                    </orders-table>
                </div>
            </div>
        </div>
        
        <order-details-component
            view-product-route="{{ route('product.show', ['__id']) }}"
            edit-product-route="{{ route('voyager.products.edit', ['__id']) }}"
            storage-path="{{ config('filesystems.disks.tracking.url') }}"
        ></order-details-component>

        @php
            $carriers = config('ups.services');
            $carriers[] = ["label" => "FedEx"];
        @endphp

        <tracking-numbers-manager
            :carriers="{{ json_encode($carriers) }}"
            storage-path="{{ config('filesystems.disks.tracking.url') }}"
            create-tracking-route="{{ route('voyager.create.tracking') }}"
        ></tracking-numbers-manager>

    </div>

@stop

@section('javascript')
    <script>
        var table = new Vue({
            el: '#orders-table'
        });
    </script>
@stop
