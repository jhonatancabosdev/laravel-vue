<footer class="footer">
    
    <hr class="py-1 m-0 mb-2">

    <div class="container pb-2">
        <div class="row col-12 px-0">
            <div class="col-lg-3 col-sm-6 col-12">
                <h3>Find Us</h3>
                <ul>
                    <li class="py-1"><i class="fa fa-home text-highlight pr-1"></i>1220 N. Old Rand Rd, Wauconda, IL 60084</li>
                    <li class="py-1"><a href="mailto:info@gwcopiers.com"><i class="fa fa-envelope text-highlight pr-1"></i>info@gwcopiers.com</a></li>
                    <li class="py-1"><a href="tel:+1{{ config('default-variables.phone') }}"><i class="fa fa-phone text-highlight pr-1"></i># {{ formatPhone(config('default-variables.phone')) }}</a></li>
                    <li class="py-1"><a href="tel:+18772110039"><i class="fa fa-fax text-highlight pr-1"></i># (877) 422-8896</a></li>
                    <li class="py-1"><a href="/"><img src="{{ asset('images/logo.png') }}" width="150" alt="Green World Copier & Supplies" /></a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <h3>Information</h3>
                <ul>
                    <li class="py-1"><a href="{{ route('contact-us') }}">Contact Us</a></li>
                    <li class="py-1"><a href="{{ route('faq') }}">FAQs</a></li>
                    <li class="py-1"><a href="{{ route('company-information') }}">Company Information</a></li>
                    <li class="py-1"><a href="{{ route('refunds') }}">Refund Policy</a></li>
                    <li class="py-1"><a href="{{ route('returns') }}">Return Policy</a></li>
                    <li class="py-1"><a href="{{ route('terms-and-conditions') }}">Terms and Conditions</a></li>
                    <li class="py-1"><a href="{{ route('vision') }}">Vision</a></li>
                    <li class="py-1"><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li class="py-1"><a href="{{ route('about-us') }}">About Us</a></li>
                    <li class="py-1"><a target="_blank" href="http://www.waucondachamber.org/list/member/green-world-copier-supplies-5702" target="_blank"><img src="{{ asset('images/wacc-logo.png') }}" width="100" alt="Wauconda Area Chamber of Commerce, Inc." srcset=""></a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <h3>My Account</h3>
                <ul>
                    <li class="py-1"><a href="{{ route('customer.account') }}#v-pills-orders">Orders</a></li>
                    <li class="py-1"><a href="{{ route('customer.account') }}#v-pills-addresses">Addresses</a></li>
                    <li class="py-1"><a href="{{ route('recents') }}">Recently Viewed Products</a></li>
                    <li class="py-1"><a target="_blank" href="{{ route('sitemap') }}">Sitemap</a></li>
                    @if (!Auth::check())
                        <li class="py-1"><a href="{{ route('login') }}?register#apply-for-vendor-account">Apply for Vendor Account</a></li>
                    @else
                        <li class="py-1"><a href="{{ route('logout') }}">Logout</a></li>
                    @endif
                    <li class="py-1"><a target="_blank" href="http://www.lakecountychamber.com/list/member/green-world-copier-supplies-4785"><img src="{{ asset('images/lccc-hclc_logo.jpg') }}" width="150" alt="Green World Copier & Supplies" /></a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <h3>Newsletter</h3>
                <p>Subscribe and become part of our community. Be the first to hear about our latest offers and discounts!</p>
                <subscribe-form-component></subscribe-form-component>
                <p class="py-1"><a target="_blank" href="https://www.bbb.org/us/il/wauconda/profile/electronic-equipment-repair/green-world-copier-and-supplies-0654-90009073#sealclick"><img src="{{ asset('images/bbb.png') }}" width="150" alt="Green World Copier & Supplies" /></a></p>
            </div>

        </div>
    </div>

    <div class="footer__row--social container-fluid">
        <div class="row mx-auto my-4">
            <span class="list pl-1">
                <a class="m-3 d-inline-block text-secondary-5" target="_blank" href="https://twitter.com/Gwcopiers"><i class="social-icons fab fa-twitter"></i></a>
                <a class="m-3 d-inline-block text-secondary-5" target="_blank" href="https://www.facebook.com/greenworldcopierandsupplies"><i class="social-icons fab fa-facebook-f"></i></a>
                <a class="m-3 d-inline-block text-secondary-5" target="_blank" href="https://www.youtube.com/channel/UC86MBg43yG5NC35pIurMrKQ"><i class="social-icons fab fa-youtube"></i></a>
                <a class="m-3 d-inline-block text-secondary-5" target="_blank" href="https://instagram.com/greenworldcopierandsupplies"><i class="social-icons fab fa-instagram"></i></a>
                {{-- <a class="m-3 d-inline-block text-secondary-5" href="{{ Voyager::setting('site.linked_in', '#') }}"><i class="social-icons fab fa-linkedin"></i></a> --}}
            </span>
        </div>
    </div>

    <p class="text-center font-weight-bold pb-2 pt-1 m-0 text-secondary-5">
    <small>copyright &copy;{{ \Carbon\Carbon::now()->year }}</small> <small>{{ config('app.name') }}. All rights reserved.</small>
    </p>
</footer>