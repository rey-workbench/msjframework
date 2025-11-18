<div class="row">
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Pengajuan Review</p>
                            <h5 class="font-weight-bolder">
                                {{ $pengajuanReview ?? 0 }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-warning text-sm font-weight-bolder">Bulan {{ date('F Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-warning shadow-warning text-center rounded-circle">
                            <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Approved Bulan {{ date('F Y') }}</p>
                            <h5 class="font-weight-bolder">
                                {{ $pengajuanApproved ?? 0 }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-primary text-sm font-weight-bolder">Bulan {{ date('F Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-primary shadow-primary text-center rounded-circle">
                            <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Rejected Bulan {{ date('F Y') }}</p>
                            <h5 class="font-weight-bolder">
                                {{ $pengajuanRejected ?? 0 }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-danger text-sm font-weight-bolder">Bulan {{ date('F Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-danger shadow-danger text-center rounded-circle">
                            <i class="ni ni-fat-remove text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Menu Akses Cepat Panitia Kredit</h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ URL::to('pengajuanPinjaman') }}" class="btn btn-outline-warning w-100">
                            <i class="ni ni-check-bold me-2"></i>
                            Approval Pengajuan
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ URL::to('potongan') }}" class="btn btn-outline-info w-100">
                            <i class="fa fa-money me-2"></i>
                            Potongan
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ URL::to('reportrekap/show/shu') }}" class="btn btn-outline-success w-100">
                            <i class="ni ni-chart-bar-32 me-2"></i>
                            Rekap SHU
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ URL::to('reportrekap/show/potongan') }}" class="btn btn-outline-secondary w-100">
                            <i class="ni ni-money-coins me-2"></i>
                            Rekap Potongan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6 mb-4">
        @include('components.chart', [
            'chartId' => 'paymentMethodChart',
            'title' => 'Statistik Metode Pembayaran',
            'chartType' => 'doughnut',
            'labels' => ['Potongan Gaji', 'Pelunasan Manual'],
            'data' => [$pinjamanDenganPotongan ?? 0, $pinjamanDenganPelunasan ?? 0],
            'colors' => [
                'rgba(67, 97, 238, 0.8)', 
                'rgba(251, 99, 64, 0.8)', 
            ],
            'height' => 350,
            'showTypeSelector' => true,
            'availableTypes' => ['bar', 'pie', 'doughnut', 'polarArea'],
            'unit' => 'pinjaman',
            'showPercentage' => true,
            'description' => 'Data berdasarkan pinjaman aktif yang telah disetujui',
            'showFilters' => true,
            'dataType' => 'paymentMethod',
            'ajaxUrl' => '/dashboard/chart-data',
            'currentMonth' => $currentMonth ?? date('m'),
            'currentYear' => $currentYear ?? date('Y'),
        ])
    </div>
    <div class="col-lg-6 mb-4">
        @if (isset($shuData))
            @include('components.chart', [
                'chartId' => 'chart-shu',
                'title' => 'Chart SHU per Tahun',
                'chartType' => 'bar',
                'labels' => array_keys($shuData),
                'data' => array_map(function ($val) {
                    return $val / 1000;
                }, array_values($shuData)),
                'colors' => [
                    'rgba(67, 97, 238, 0.8)', 
                    'rgba(251, 99, 64, 0.8)', 
                    'rgba(66, 186, 150, 0.8)', 
                    'rgba(234, 84, 85, 0.8)', 
                    'rgba(251, 207, 51, 0.8)', 
                ],
                'height' => 375,
                'showTypeSelector' => true,
                'availableTypes' => ['bar', 'line', 'pie', 'doughnut', 'polarArea'],
                'unit' => 'Ribu Rp',
                'showPercentage' => false,
                'showFilters' => true,
                'dataType' => 'shu',
                'ajaxUrl' => '/dashboard/chart-data',
                'currentMonth' => '',
                'currentYear' => $currentYear ?? date('Y'),
            ])
        @endif
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 mb-4">
        @include('components.chart', [
            'chartId' => 'chartVisualisasiAkredt',
            'title' => 'Visualisasi Data Kredit',
            'chartType' => 'bar',
            'labels' => [],
            'data' => [],
            'height' => 300,
            'showTypeSelector' => true,
            'showFilters' => true,
            'showDataSelector' => true,
            'dataType' => 'approval',
            'ajaxUrl' => route('dashboard.chart-data'),
            'dataOptions' => [
                'approval' => ['label' => 'Data Approval'],
                'pinjaman' => ['label' => 'Data Pinjaman'],
                'potongan' => ['label' => 'Data Potongan'],
                'shu' => ['label' => 'Data SHU'],
            ],
        ])
    </div>
</div>
