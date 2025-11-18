<div class="container position-sticky z-index-sticky top-0">
    <div class="row">
        <div class="col-12">
            <!-- Navbar -->
            <nav
                class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
                <div class="container-fluid">
                    <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="">
                        <img src="{{ asset('/storage' . '/' . @$setup_app->logo_small) }}" class="navbar-brand-img"
                            alt="main_logo" style="height: 40px;">
                        <span class="ms-1 font-weight-bold">{{ @$setup_app->appname }} </span>
                    </a>
                    <h6 class="text-primary">{{ @$setup_app->company }}</h6>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>
    </div>
</div>
