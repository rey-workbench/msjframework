@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid py-4">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        @if ($role == 'anggot')
                            <h6 class="mb-1">Dashboard Anggota</h6>
                            <p class="text-sm">Selamat datang di dashboard anggota koperasi</p>
                        @elseif($role == 'kadmin')
                            <h6 class="mb-1">Dashboard Admin Level 1</h6>
                            <p class="text-sm">Panel administrasi dan kelola data anggota</p>
                        @elseif($role == 'akredt')
                            <h6 class="mb-1">Dashboard Panitia Kredit</h6>
                            <p class="text-sm">Panel review dan approval pengajuan kredit</p>
                        @elseif($role == 'ketuum')
                            <h6 class="mb-1">Dashboard Ketua Umum</h6>
                            <p class="text-sm">Panel eksekutif dan laporan keuangan</p>
                        @elseif($role == 'atrans')
                            <h6 class="mb-1">Dashboard Admin Transfer</h6>
                            <p class="text-sm">Panel proses transfer dana dan pencairan</p>
                        @else
                            <h6 class="mb-1">Dashboard</h6>
                            <p class="text-sm">Dashboard MSJKoperasi</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($role == 'anggot')
            @include('pages.dashboard.role.anggota')
        @elseif($role == 'kadmin')
            @include('pages.dashboard.role.kadmin')
        @elseif($role == 'akredt')
            @include('pages.dashboard.role.akredt')
        @elseif($role == 'ketuum')
            @include('pages.dashboard.role.ketuum')
        @elseif($role == 'atrans')
            @include('pages.dashboard.role.atrans')
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-5 text-center">
                            <div class="icon icon-shape icon-xxl bg-gradient-primary shadow-primary text-center rounded-circle mb-4 mx-auto">
                                <i class="ni ni-building text-white opacity-10" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="text-primary mb-3">Selamat Datang di MSJ Koperasi</h3>
                            <p class="text-muted mb-4">Sistem Informasi Manajemen Koperasi</p>
                            <p class="text-sm text-muted">Silakan login dengan akun yang sesuai untuk mengakses fitur dashboard</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js')
    <script src="./assets/js/plugins/chartjs.min.js"></script>
@endpush
