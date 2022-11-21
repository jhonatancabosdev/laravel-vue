<div class="side-menu sidebar-inverse position-fixed d-block h-100 d-flex flex-column">
    <nav class="navbar navbar-default py-0 d-block" role="navigation">
        <div class="text-right d-block">
            <button class="hamburger btn btn-transparent">
                <span class="hamburger-inner"></span>
            </button>
        </div>
        <div class="pl-1 py-1" id="adminmenu">
            <admin-menu :items="{{ menu('admin', '_json') }}"></admin-menu>
        </div>
    </nav>
    <div class="d-flex flex-column mt-auto mb-0 p-2 text-center text-muted jq-fme-copyright">
        <div class="d-block mb-3">
            Another awesome Laravel build <br> By - <a class="text-muted text-decoration-none" href="https://fountainheadme.com/">FountainheadME.com</a> - <br>
            Copyright 2020 - (844)-691-1363
        </div>

        <div class="d-block">
            <form action="{{ route('voyager.logout') }}" method="POST">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-pink btn-block">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>

    </div>
</div>
