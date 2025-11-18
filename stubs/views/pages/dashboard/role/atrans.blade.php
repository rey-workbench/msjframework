<div class="row">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Transfer Pending</p>
                            <h5 class="font-weight-bolder">
                                {{ $transferPending ?? 0 }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-warning text-sm font-weight-bolder">Menunggu transfer</span>
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
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Transfer Selesai</p>
                            <h5 class="font-weight-bolder">
                                {{ $transferSelesai ?? 0 }}
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
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Nominal Pending</p>
                            <h5 class="font-weight-bolder">
                                {{ $format->CurrencyFormat($nominalPending ?? 0) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-info text-sm font-weight-bolder">Bulan {{ date('F Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-info shadow-info text-center rounded-circle">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Nominal Transfer</p>
                            <h5 class="font-weight-bolder">
                                {{ $format->CurrencyFormat($nominalTransfer ?? 0) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-primary text-sm font-weight-bolder">Bulan {{ date('F Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-primary shadow-primary text-center rounded-circle">
                            <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
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
                <h6 class="mb-0">Menu Akses Cepat Admin Transfer</h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="{{ URL::to('pengajuanPinjaman') }}" class="btn btn-outline-primary w-100">
                            <i class="ni ni-money-coins me-2"></i>
                            Proses Transfer Dana
                        </a>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="{{ URL::to('reportrekap/show/pinjaman') }}" class="btn btn-outline-info w-100">
                            <i class="ni ni-archive-2 me-2"></i>
                            Report Pinjaman
                        </a>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="{{ URL::to('reportrekap/show/debitkredit') }}" class="btn btn-outline-success w-100">
                            <i class="ni ni-money-coins me-2"></i>
                            Report Debit Kredit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (isset($transferHarian))
    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            @if (isset($transferHarian))
                @include('components.chart', [
                    'chartId' => 'chart-transfer-harian',
                    'title' => 'Transfer Dana Mingguan',
                    'chartType' => 'line',
                    'labels' => array_column($transferHarian, 'tanggal'),
                    'data' => array_column($transferHarian, 'jumlah'),
                    'colors' => [
                        'rgba(67, 97, 238, 0.8)', 
                    ],
                    'height' => 250,
                    'showTypeSelector' => true,
                    'availableTypes' => ['bar', 'line'],
                    'unit' => 'transfer',
                    'showPercentage' => false,
                    'description' => '7 Hari Terakhir',
                    'showFilters' => false,
                    'dataType' => '',
                    'ajaxUrl' => '',
                    'currentMonth' => '',
                    'currentYear' => date('Y'),
                ])
            @endif
        </div>
        @if (isset($avgProcessTime))
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Performa Transfer</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="text-center mb-4">
                            <div class="icon icon-shape icon-lg bg-primary shadow text-center rounded-circle mb-3">
                                <i class="ni ni-time-alarm text-white opacity-10" style="font-size: 1.5rem;"></i>
                            </div>
                            <h4 class="text-primary">{{ $avgProcessTime }} Hari</h4>
                            <p class="text-sm text-muted">Rata-rata waktu proses transfer</p>
                        </div>
                        <div class="row">
                            <div class="col-6 text-center">
                                <h5 class="text-primary">{{ $transferSelesai ?? 0 }}</h5>
                                <p class="text-xs text-muted mb-0">Transfer Selesai</p>
                            </div>
                            <div class="col-6 text-center">
                                <h5 class="text-warning">{{ $transferPending ?? 0 }}</h5>
                                <p class="text-xs text-muted mb-0">Menunggu Transfer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-lg-5">
                @if (isset($shuData))
                    @include('components.chart', [
                        'chartId' => 'chart-shu-atrans',
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
                        'height' => 250,
                        'showTypeSelector' => true,
                        'availableTypes' => ['bar', 'line', 'pie', 'doughnut', 'polarArea'],
                        'unit' => 'Ribu Rp',
                        'showPercentage' => false,
                        'showFilters' => false,
                        'dataType' => '',
                        'ajaxUrl' => '',
                        'currentMonth' => '',
                        'currentYear' => date('Y'),
                    ])
                @endif
            </div>
        @endif
    </div>
@endif

<div class="row mt-4">
    <div class="col-12 mb-4">
        @include('components.chart', [
            'chartId' => 'chartVisualisasiAtrans',
            'title' => 'Visualisasi Data Transfer',
            'chartType' => 'bar',
            'labels' => [],
            'data' => [],
            'height' => 300,
            'showTypeSelector' => true,
            'showFilters' => true,
            'showDataSelector' => true,
            'dataType' => 'transfer',
            'ajaxUrl' => route('dashboard.chart-data'),
            'dataOptions' => [
                'transfer' => ['label' => 'Data Transfer'],
                'pinjaman' => ['label' => 'Data Pinjaman'],
                'performa' => ['label' => 'Data Performa'],
                'shu' => ['label' => 'Data SHU'],
            ],
        ])
    </div>
</div>
