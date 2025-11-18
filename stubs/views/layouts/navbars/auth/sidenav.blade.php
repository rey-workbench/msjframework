<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ URL::to('/') }}" target="">
            <img src="{{ asset('/storage' . '/' . @$setup_app->logo_small) }}" class="navbar-brand-img h-100"
                alt="main_logo">
            <span class="ms-1 font-weight-bold">{{ @$setup_app->appname }}</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            {{-- LOOPING GMENU --}}
            @foreach ($gmenu as $g)
                @if ($g->gmenu == 'blankx')
                    {{-- Query DMenu --}}
                    @php
                        $querydmenu = DB::table('sys_dmenu')
                            ->join('sys_auth', 'sys_dmenu.dmenu', '=', 'sys_auth.dmenu')
                            ->whereIn('sys_auth.idroles', $users_rules)
                            ->where('sys_dmenu.isactive', '1')
                            ->where('sys_dmenu.show', '1')
                            ->where('sys_auth.isactive', '1')
                            ->where('sys_dmenu.gmenu', $g->gmenu)
                            ->select('sys_dmenu.*')
                            ->distinct()
                            ->orderBy('sys_dmenu.urut', 'asc')
                            ->get();
                    @endphp
                    @foreach ($querydmenu as $d)
                        <li class="nav-item">
                            <a class="nav-link blank {{ $d->url == $url_menu ? 'active' : '' }}"
                                href="{{ URL::to($d->url) }}">
                                <div
                                    class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                                    <i class="ni {{ $d->icon }} text-primary text-sm opacity-10"></i>
                                </div>
                                <span class="nav-link-text ms-1 py-1">{{ $d->name }}</span>
                                {{-- check query notif --}}
                                @if ($d->notif != '')
                                    @php
                                        // get notif
                                        $data_notif = DB::select($d->notif);
                                        foreach ($data_notif as $n) {
                                            $notif = $n->notif;
                                        }
                                    @endphp
                                    <span style="background-color: #f33b3b;position: absolute;right: 20px;"
                                        class="badge text-white ms-1">{{ $notif }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#{{ $g->gmenu }}"
                            class="nav-link {{ $g->name == $title_group ? 'active' : '' }}"
                            aria-controls="{{ $g->gmenu }}" role="button"
                            {{ $g->name == $title_group ? 'aria-expanded=true' : '' }}>
                            <div
                                class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                                <i class="ni {{ $g->icon }} text-primary text-sm opacity-10"></i>
                            </div>
                            <span class="nav-link-text ms-1">{{ $g->name }}</span>
                        </a>
                        <div class="collapse {{ $g->name == $title_group ? 'show' : '' }}" id="{{ $g->gmenu }}"
                            style="">
                            <ul class="nav ms-4">
                                {{-- Query DMenu --}}
                                @php
                                    $querydmenu = DB::table('sys_dmenu')
                                        ->join('sys_auth', 'sys_dmenu.dmenu', '=', 'sys_auth.dmenu')
                                        ->whereIn('sys_auth.idroles', $users_rules)
                                        ->where('sys_dmenu.isactive', '1')
                                        ->where('sys_dmenu.show', '1')
                                        ->where('sys_auth.isactive', '1')
                                        ->where('sys_dmenu.gmenu', $g->gmenu)
                                        ->select('sys_dmenu.*')
                                        ->distinct()
                                        ->orderBy('sys_dmenu.urut', 'asc')
                                        ->get();
                                @endphp
                                @foreach ($querydmenu as $d)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $d->url == $url_menu ? 'active' : '' }}"
                                            href="{{ URL::to($d->url) }}">
                                            <div
                                                class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                                                <i class="ni {{ $d->icon }} text-primary text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1 py-1">{{ $d->name }}</span>
                                            {{-- check query notif --}}
                                            @if ($d->notif != '')
                                                @php
                                                    // get notif
                                                    $data_notif = DB::select($d->notif);
                                                    foreach ($data_notif as $n) {
                                                        $notif = $n->notif;
                                                    }
                                                @endphp
                                                <span style="background-color: #f33b3b;position: absolute;right: 20px;"
                                                    class="badge text-white ms-1">{{ $notif }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</aside>
