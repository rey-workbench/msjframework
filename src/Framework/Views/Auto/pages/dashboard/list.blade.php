@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid py-4">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-1">Dashboard</h6>
                        <p class="text-sm">Selamat datang di MSJ Framework Dashboard</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Default Dashboard Content --}}
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 56px; height: 56px;">
                                    <i class="ni ni-chart-bar-32 text-white" style="font-size: 1.25rem;"></i>
                                </div>
                            </div>
                            <div class="col text-end">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Data</p>
                                <h5 class="font-weight-bolder mb-0">{{ $totalData ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-success shadow rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 56px; height: 56px;">
                                    <i class="ni ni-check-bold text-white" style="font-size: 1.25rem;"></i>
                                </div>
                            </div>
                            <div class="col text-end">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Data Aktif</p>
                                <h5 class="font-weight-bolder mb-0">{{ $dataAktif ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-warning shadow rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 56px; height: 56px;">
                                    <i class="ni ni-time-alarm text-white" style="font-size: 1.25rem;"></i>
                                </div>
                            </div>
                            <div class="col text-end">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Pending</p>
                                <h5 class="font-weight-bolder mb-0">{{ $dataPending ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-info shadow rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 56px; height: 56px;">
                                    <i class="ni ni-single-02 text-white" style="font-size: 1.25rem;"></i>
                                </div>
                            </div>
                            <div class="col text-end">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total User</p>
                                <h5 class="font-weight-bolder mb-0">{{ $totalUser ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-5 text-center">
                        <div class="icon icon-shape icon-xxl bg-gradient-primary shadow-primary text-center rounded-circle mb-4 mx-auto">
                            <i class="ni ni-building text-white opacity-10" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="text-primary mb-3">Selamat Datang di MSJ Framework</h3>
                        <p class="text-muted mb-4">Sistem Informasi berbasis Laravel</p>
                        <p class="text-sm text-muted">Customize dashboard ini sesuai kebutuhan aplikasi Anda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="./assets/js/plugins/chartjs.min.js"></script>
@endpush
