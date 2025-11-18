@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pages'])

    <div class="card shadow-lg mx-4">
        <div class="row">
            <main class="main-content mt-0 ps">
                <div class="page-header min-vh-100" style="background-image: url('/img/illustrations/404.svg');">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-9 mx-auto text-center">
                                <h1 class="display-4 text-bolder text-primary">Error</h1>
                                <h3 class="text-danger mb-4">{{ $errorpages }}</h3>
                                
                                @if(str_contains($errorpages, 'ID parameter tidak ditemukan') || str_contains($errorpages, 'ID tidak valid'))
                                    <div class="alert alert-warning mx-auto" style="max-width: 600px;">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Petunjuk:</strong> Akses halaman detail melalui daftar data, jangan gunakan URL langsung.
                                    </div>
                                    <div class="d-flex justify-content-center gap-3 mt-4">
                                        <a href="/pengajuanPinjaman" class="btn bg-gradient-primary">
                                            <i class="fas fa-list me-2"></i>Daftar Pengajuan Pinjaman
                                        </a>
                                        <button onclick="history.back()" class="btn bg-gradient-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </button>
                                        <a href="/dashboard" class="btn bg-gradient-dark">
                                            <i class="fas fa-home me-2"></i>Dashboard
                                        </a>
                                    </div>
                                @else
                                    <p class="lead mb-4">Silahkan hubungi administrator jika masalah berlanjut.</p>
                                    <div class="d-flex justify-content-center gap-3">
                                        <button onclick="history.back()" class="btn bg-gradient-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </button>
                                        <a href="/dashboard" class="btn bg-gradient-dark">
                                            <i class="fas fa-home me-2"></i>Dashboard
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px;">
                    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                </div>
            </main>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('js')
    <script src="./assets/js/plugins/chartjs.min.js"></script>
    <script>
        var ctx1 = document.getElementById("chart-line").getContext("2d");

        var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(251, 99, 64, 0)');
    </script>
@endpush
