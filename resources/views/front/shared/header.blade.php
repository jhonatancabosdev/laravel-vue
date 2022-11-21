<header class="header sticky-header bg-white">
    <div class="header__upper bg-black">
        <div class="container px-md-0 px-3">
            <p class="pt-2 pb-1 m-0 text-right text-white h5">
                Call Us: <a class="text-white" href="tel:+1{{ config('default-variables.phone') }}">{{ formatPhone(config('default-variables.phone')) }}</a>
        </div>
    <div class="header__row--primary bg-white">
        <div class="container px-0">
            <div class="d-flex flex-column flex-sm-row align-items-center bg-header header__wrapper">
                @include('front.shared.navigation.navigation-primary')
            </div>
        </div>
    </div>
</header> 