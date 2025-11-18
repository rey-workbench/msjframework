<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                @if ($title_group && $title_group != '-')
                    <li class="breadcrumb-item text-sm"><a class="text-white" href="javascript:;">{{ $title_group }}</a>
                    </li>
                @endif
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">{{ $title_menu }}</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">{{ $title }} {{ $title_menu }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                    </div>
                </a>
            </li>
            <div class="ms-md-auto pe-md-3 d-flex align-items-center"> </div>
            <ul class="navbar-nav  justify-content-end">
                <li class="nav-item dropdown d-flex align-items-center">
                    <div class="dropdown">
                        <a href="#" class="btn bg-gradient-primary dropdown-toggle " data-bs-toggle="dropdown"
                            id="myprofile">
                            <img src="{{ asset('/storage' . '/' . $user_login->image) }}"
                                class="avatar avatar-sm me-1 ">
                            {{ $user_login->firstname }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="myprofile">
                            <li class="mb-2">
                                <a class="dropdown-item border-radius-md text-primary" href="{{ URL::to('profile') }}"
                                    class="nav-link text-white font-weight-bold px-0">
                                    <i class="fa fa-user pe-2"></i>
                                    <span class="font-weight-bold">Akun Saya</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="dropdown-item border-radius-md text-primary"
                                    href="{{ URL::to('changepass') }}"
                                    class="nav-link text-white font-weight-bold px-0">
                                    <i class="fa fa-key pe-2"></i>
                                    <span class="font-weight-bold">Ganti Password</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <form role="form" method="post" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a class="dropdown-item border-radius-md text-primary" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="nav-link text-white font-weight-bold px-0">
                                        <i class="fa fa-right-from-bracket pe-2"></i>
                                        <span class="font-weight-bold">Log out</span>
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
