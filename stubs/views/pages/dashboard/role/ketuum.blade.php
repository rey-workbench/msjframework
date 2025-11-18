
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                            <i class="ni ni-trophy text-white" style="font-size: 1.25rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col text-end">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Final Approval</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $finalApproval ?? 0 }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-warning text-sm font-weight-bolder">Menunggu keputusan</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                            <i class="ni ni-money-coins text-white" style="font-size: 1.25rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col text-end">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Pinjaman</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $format->CurrencyFormat($laporanKeuangan['totalPinjaman'] ?? 0) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-primary text-sm font-weight-bolder">Tahun {{ date('Y') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                            <i class="ni ni-diamond text-white" style="font-size: 1.25rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col text-end">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">SHU</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $format->CurrencyFormat($laporanKeuangan['totalSHU'] ?? 0) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-primary text-sm font-weight-bolder">Tahun {{ date('Y') }}</span>
                            </p>
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
                <h6 class="mb-0">Menu Akses Cepat Ketua Umum</h6>
            </div>
            <div class="card-body p-3">
                <div class="row justify-content-center">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL::to('pengajuanPinjaman') }}" class="btn btn-outline-warning w-100">
                            <i class="ni ni-trophy me-2"></i>
                            Final Approval
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL:: to('reportrekap/show/shu') }}" class="btn btn-outline-primary w-100">
                            <i class="ni ni-diamond me-2"></i>
                            Report SHU
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL:: to('reportrekap/show/potongan') }}" class="btn btn-outline-info w-100">
                            <i class="ni ni-chart-pie-35 me-2"></i>
                            Report Potongan
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL:: to('reportrekap/show/pinjaman') }}" class="btn btn-outline-secondary w-100">
                            <i class="ni ni-money-coins me-2"></i>
                            Report Pinjaman
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL:: to('reportrekap/show/debitkredit') }}" class="btn btn-outline-dark w-100">
                            <i class="ni ni-archive-2 me-2"></i>
                            Report Debit Kredit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">Laporan Keuangan Eksekutif</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <select id="laporanFilterMonth" class="form-select form-select-sm"
                                style="max-width: 120px;">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                        {{ date('m') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('M') }}
                                    </option>
                                @endfor
                            </select>
                            <select id="laporanFilterYear" class="form-select form-select-sm" style="max-width: 80px;">
                                @for ($i = 2020; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm align-items-center mb-0" id="table_laporan">
                        <thead class="thead-light" style="background-color: #00b7bd4f;">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Keterangan</th>
                                <th
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Nilai</th>
                                <th
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody id="laporanTableBody">
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-check-bold text-primary me-2"></i>
                                    <span class="font-weight-bold">Pinjaman Aktif</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-primary"
                                        id="aktifCount">{{ $laporanKeuangan['pinjamanAktif'] ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-primary">Berjalan</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-archive-2 text-info me-2"></i>
                                    <span class="font-weight-bold">Pinjaman Selesai</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-info"
                                        id="selesaiCount">{{ $laporanKeuangan['pinjamanSelesai'] ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-info">Lunas</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-delivery-fast text-warning me-2"></i>
                                    <span class="font-weight-bold">Sudah Ditransfer</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-warning"
                                        id="transferredCount">{{ $laporanKeuangan['pinjamanTransferred'] ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-warning">Selesai</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">Pinjaman per Departemen</h6>
                        <p class="text-sm mb-0">Top 5 departemen</p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <select id="departemenFilterMonth" class="form-select form-select-sm"
                                style="max-width: 120px;">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                        {{ date('m') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('M') }}
                                    </option>
                                @endfor
                            </select>
                            <select id="departemenFilterYear" class="form-select form-select-sm"
                                style="max-width: 80px;">
                                @for ($i = 2020; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                @if (isset($pinjamanPerDepartemen) && count($pinjamanPerDepartemen) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm align-items-center mb-0" id="table_departemen">
                            <thead class="thead-light" style="background-color: #00b7bd4f;">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Departemen</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Jumlah</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody id="departemenTableBody">
                                @foreach ($pinjamanPerDepartemen as $dept)
                                    <tr>
                                        <td class="text-sm">
                                            <i class="ni ni-building text-primary me-2"></i>
                                            <span class="font-weight-bold">{{ $dept->departemen }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-sm bg-primary">{{ $dept->total }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="text-xs font-weight-bold">{{ $format->CurrencyFormat($dept->nominal) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4" id="departemenNoData">
                        <i class="ni ni-building text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">Belum ada data departemen</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        @if (isset($shuData))
            @include('components.chart', [
                'chartId' => 'chart-shu-ketuum',
                'title' => 'Chart SHU per Tahun',
                'chartType' => 'bar',
                'labels' => array_keys($shuData),
                'data' => array_map(function ($val) {
                    return $val / 1000000;
                }, array_values($shuData)),
                'colors' => [
                    'rgba(67, 97, 238, 0.8)', 
                    'rgba(251, 99, 64, 0.8)', 
                    'rgba(66, 186, 150, 0.8)', 
                    'rgba(234, 84, 85, 0.8)', 
                    'rgba(251, 207, 51, 0.8)', 
                ],
                'height' => 300,
                'showTypeSelector' => true,
                'availableTypes' => ['bar', 'line', 'pie', 'doughnut', 'polarArea'],
                'unit' => 'Juta Rp',
                'showPercentage' => false,
                'showFilters' => true,
                'dataType' => 'shu',
                'ajaxUrl' => '/dashboard/chart-data',
                'currentMonth' => '',
                'currentYear' => date('Y'),
            ])
        @endif
    </div>
    <div class="col-lg-6 mb-4">
        @include('components.chart', [
            'chartId' => 'chartVisualisasiKetuum',
            'title' => 'Visualisasi Data',
            'chartType' => 'bar',
            'labels' => [],
            'data' => [],
            'height' => 300,
            'showTypeSelector' => true,
            'showFilters' => true,
            'showDataSelector' => true,
            'dataType' => 'pinjaman',
            'ajaxUrl' => route('dashboard.chart-data'),
            'dataOptions' => [
                'pinjaman' => ['label' => 'Data Pinjaman'],
                'anggota' => ['label' => 'Data Anggota'],
                'keuangan' => ['label' => 'Data Keuangan'],
                'departemen' => ['label' => 'Data per Departemen'],
                'shu' => ['label' => 'Data SHU'],
            ],
        ])
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            
            $('#table_laporan').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: true
            });

            
            $('#table_departemen').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: true
            });

            
            function loadLaporanKeuangan() {
                const month = $('#laporanFilterMonth').val();
                const year = $('#laporanFilterYear').val();

                $.ajax({
                    url: '/dashboard/laporan-keuangan',
                    method: 'GET',
                    data: {
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        $('#aktifCount').text(response.pinjamanAktif || 0);
                        $('#selesaiCount').text(response.pinjamanSelesai || 0);
                        $('#transferredCount').text(response.pinjamanTransferred || 0);
                    },
                    error: function(xhr) {
                    }
                });
            }

            
            function loadPinjamanPerDepartemen() {
                const month = $('#departemenFilterMonth').val();
                const year = $('#departemenFilterYear').val();

                $.ajax({
                    url: '/dashboard/pinjaman-departemen',
                    method: 'GET',
                    data: {
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        const tbody = $('#departemenTableBody');
                        const table = $('#table_departemen');
                        const noDataDiv = $('#departemenNoData');

                        if (response.data && response.data.length > 0) {
                            let html = '';
                            response.data.forEach(function(dept) {
                                html += `
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-building text-primary me-2"></i>
                                    <span class="font-weight-bold">${dept.departemen}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-primary">${dept.total}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-xs font-weight-bold">${dept.nominal_formatted}</span>
                                </td>
                            </tr>
                        `;
                            });
                            tbody.html(html);
                            table.show();
                            noDataDiv.hide();
                        } else {
                            table.hide();
                            noDataDiv.show();
                        }
                    },
                    error: function(xhr) {
                    }
                });
            }

            
            $('#laporanFilterMonth, #laporanFilterYear').on('change', function() {
                loadLaporanKeuangan();
            });

            
            $('#departemenFilterMonth, #departemenFilterYear').on('change', function() {
                loadPinjamanPerDepartemen();
            });

        });
    </script>
@endpush
