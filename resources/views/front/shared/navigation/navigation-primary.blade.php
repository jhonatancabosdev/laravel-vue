<div class="container-fluid bg-white px-0">
    <div class="container px-0">
        <nav class="px-0 col-12 navbar navbar-dark navbar-expand-lg main-navbar text-uppercase py-0">
            <div class="header__logo__wrapper text-center text-sm-left px-0 pl-3 pl-sm-0 py-0">
                <a class="d-block header__logo__link py-lg-2" href="{{ route('home') }}">
                    <img
                        src="{{ Voyager::setting('site.logo') ? '/' . Voyager::setting('site.logo') : asset('images/logo.png') }}"
                        width="200"
                        class="img-fluid py-lg-0 py-md-2 py-3"
                        alt="{{ Voyager::setting('site.title') }}" />
                </a>
            </div>
            <button 
            class="navbar-toggler bg-highlight p-1 border-0 mr-lg-0 ml-auto border-radius-0" 
            type="button" 
                data-toggle="collapse" 
                data-target="#main-navbar" 
                aria-controls="main-navbar" 
                aria-expanded="false" 
                aria-label="Show Navbar Items"
                >
                <span class="navbar-toggler-icon"></span>
            </button>
           
            <div class="collapse navbar-collapse" id="main-navbar">
                <div class="nav-item--search-new text-nowrap mr-auto ml-2">
                    <form class="nav-item--search-new__form d-lg-block d-flex mt-lg-0 mt-2 mb-1" action="{{ route('product.search') }}">
                        <button type="submit" class="p-lg-3 px-3 py-1 bg-white border-0 border-radius-0"><i class="fa fa-search"></i></button>
                        <input type="text" name="keyword" id="keyword" class="border-0 px-3" value="{{ request()->keyword }}" placeholder="Search">
                    </form>
                    {{-- <a href="#" class="d-block p-3 nav-item--search__link"> <i class="fa fa-search"></i> </a> --}}
                </div>
                @if (!$navigationCategories->isEmpty())

                    <ul class="ml-lg-3 navbar-nav mx-0 px-md-0">

                        @foreach ($navigationCategories as $cat)

                            @if ($cat->children->isEmpty())

                                <li class="nav-item">
                                    <a class="nav-link text-nowrap mx-xl-2 px-2 {{ $cat->slug }}{{ isset($category) && $category->slug === $cat->slug ? ' text-highlight' : '' }}" href="{{ route('category.show', $cat) }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>

                            @else

                                <li class="nav-item dropdown">

                                    <a 
                                        class="{{
                                            !isset($isHome) && 
                                            isset($category) &&
                                            (
                                                ($category->parent && $category->parent->slug === $cat->slug) ||
                                                $category->slug === $cat->slug
                                            ) ? 'text-highlight active ' : '' }}p-2 mx-xl-2 nav-link dropdown-toggle" 
                                        href="#" 
                                        id="main-nav-{{ $cat->slug }}" 
                                        role="button" 
                                        data-toggle="dropdown" 
                                        aria-haspopup="true" 
                                        aria-expanded="false"
                                    >
                                        {{ $cat->name }}
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="{{ $cat->slug }}">

                                        @foreach ($cat->children as $child)

                                            <a
                                                id="main-nav-{{ $child->slug }}"
                                                class="dropdown-item {{ $child->slug }}{{ isset($category) && $category->slug === $child->slug ? ' text-highlight' : '' }}"
                                                href="{{ route('category.show', $child) }}">{{ $child->name }}</a>

                                        @endforeach
                                        <div>
                                            <a class="viewall dropdown-item text-highlight" href="{{ route('category.show', $cat) }}"> Shop All {{ $cat->name }}</a>
                                        </div>

                                    </div>
                                    
                                </li>

                            @endif

                        @endforeach

                        <li class="nav-item--acount nav-item text-nowrap">
                            <a class="nav-link px-xl-3 p-2" href="{{ route('customer.account') }}">My Account</a>
                        </li>

                        <li class="nav-item text-nowrap">
                            <a href="{{ route('contact-us') }}?contact_type=instant-quote" class="nav-link px-xl-3 p-2 text--orange">Get Instant Quote</a>
                        </li>

                        <li class="nav-item text-nowrap">
                            <a href="{{ route('contact-us') }}" class="nav-link px-xl-3 p-2">Contact Us</a>
                        </li>
                    </ul>

                @endif
                <div class="nav-item--button-group my-2 px-0 my-lg-0">
                    <ul class="list-inline mb-0 mr-0 d-flex flex-column">
                        <li class="nav-item--drawer nav-item--account{{ auth()->guard('customer')->check() ? ' logged-in' : '' }} bg-secondary">
                            @if (!auth()->guard('customer')->check())
                                <a href="{{ route('login') }}" class="d-block py-2 px-3 bg-secondary"><i class="fas fa-user"></i><span>My Account</span></a>
                            @else 
                                <a href="{{ route('customer.account') }}" class="customer-account-link d-block bg-secondary py-2 px-3 d-flex">
                                    <i class="fas fa-user"></i> <span>My Account</span>
                                </a>
                                <div aria-labelledby="customer-profile" class="customer-profile--form">
                                    <div class="list-group">
                                        <form method="POST" action="{{ route('logout') }}" >
                                            @csrf
                                            <button class="px-3 d-flex py-1 justify-content-between align-items-center btn px-0 w-100 text-dark" type="submit">
                                                Logout
                                                <i class="fas m-0 fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </li>
                        <li class="nav-item--drawer nav-item--wishlist">
                            <a class="bg-secondary d-block px-3 py-2" href="{{ route('favorites') }}"><i class="fa fa-heart"></i><span>Wish List</span></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="nav-item--dynamic-cart text-center text-sm-right">
                <div class="dynamic-cart-wrapper__container">
                    <div class="dynamic-cart-wrapper py-lg-3 py-1 px-lg-2 bg-highlight">
                        <cart-component
                            class="cart-component__wrapper text-center fw-900 text-white"
                            css-classes="cart-component__headline"
                            checkout-url="{{ 
                                auth()->guard('customer')->check() 
                                ? route('checkout.index')
                                :  route('guest.checkout.index')
                            }}"
                        >
                        </cart-component>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>