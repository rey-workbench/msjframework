
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                            <i class="ni ni-bag-17 text-white" style="font-size: 1.25rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col text-end">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Pinjaman Bulan {{ date('F Y') }}</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $format->CurrencyFormat($totalPinjamanBulanIni ?? 0) }}
                            </h5>
                        </div>
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
                        <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                            <i class="ni ni-bold-down text-white" style="font-size: 1.25rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col text-end">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Pengeluaran {{ date('Y') }}</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $format->CurrencyFormat($pengeluaran ?? 0) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-danger text-sm font-weight-bolder">Dana tersalurkan</span>
                            </p>
                        </div>
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
                        <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                            <i class="ni ni-bold-up text-white" style="font-size: 1.25rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col text-end">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Pemasukan {{ date('Y') }}</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $format->CurrencyFormat($pemasukan ?? 0) }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-info text-sm font-weight-bolder">Cicilan terbayar</span>
                            </p>
                        </div>
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
                        <div class="bg-primary shadow rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                            <i class="ni ni-single-02 text-white" style="font-size: 1.25rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col text-end">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Anggota</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $jumlahRekeningAktif ?? 0 }}
                            </h5>
                            <p class="mb-0">
                                <span class="text-primary text-sm font-weight-bolder">Anggota aktif</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Menu Akses Cepat Admin</h6>
                <p class="text-sm mb-0 text-muted">Klik menu di bawah untuk mengakses halaman secara langsung</p>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL::to('pengajuanPinjaman') }}" class="btn btn-outline-warning w-100">
                            <i class="ni ni-check-bold me-2"></i>
                            Approval Pengajuan
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL::to('potongan') }}" class="btn btn-outline-danger w-100">
                            <i class="fa fa-money me-2"></i>
                            Potongan
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL::to('anggota') }}" class="btn btn-outline-primary w-100">
                            <i class="ni ni-single-02 me-2"></i>
                            Kelola Data Anggota
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL::to('periode') }}" class="btn btn-outline-info w-100">
                            <i class="ni ni-calendar-grid-58 me-2"></i>
                            Master Periode
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL::to('debitKredit') }}" class="btn btn-outline-success w-100">
                            <i class="ni ni-money-coins me-2"></i>
                            Transaksi Keuangan
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <a href="{{ URL::to('shu') }}" class="btn btn-outline-secondary w-100">
                            <i class="ni ni-chart-bar-32 me-2"></i>
                            Rekap SHU
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
                <div class="row">
                    <div class="col-md-8 d-flex align-items-center">
                        <h6 class="mb-0">Pengajuan Pinjaman</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm align-items-center mb-0" id="table_pengajuan">
                        <thead class="thead-light" style="background-color: #00b7bd4f;">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jenis
                                    Pengajuan</th>
                                <th
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Jumlah</th>
                                <th
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-money-coins text-warning me-2"></i>
                                    <span class="font-weight-bold">Penarikan Simpanan</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-secondary">{{ $penarikanSimpanan ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-secondary">Belum tersedia</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-paper-diploma text-info me-2"></i>
                                    <span class="font-weight-bold">Pengajuan Pinjaman</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-info">{{ $pengajuanPinjaman ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-sm {{ ($pengajuanPinjaman ?? 0) > 0 ? 'bg-danger' : 'bg-primary' }}">
                                        {{ ($pengajuanPinjaman ?? 0) > 0 ? 'Perlu Review' : 'Clear' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-time-alarm text-warning me-2"></i>
                                    <span class="font-weight-bold">Menunggu Persetujuan</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-sm bg-warning">{{ $pengajuanPinjamanMenunggu ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-sm {{ ($pengajuanPinjamanMenunggu ?? 0) > 0 ? 'bg-warning' : 'bg-primary' }}">
                                        {{ ($pengajuanPinjamanMenunggu ?? 0) > 0 ? 'Dalam Proses' : 'Clear' }}
                                    </span>
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
                <div class="row">
                    <div class="col-md-8 d-flex align-items-center">
                        <h6 class="mb-0">Total Transaksi Debit Kredit Bulan Ini</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm align-items-center mb-0" id="table_belum_bayar">
                        <thead class="thead-light" style="background-color: #00b7bd4f;">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Keterangan</th>
                                <th
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Jumlah</th>
                                <th
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-money-coins text-primary me-2"></i>
                                    <span class="font-weight-bold">Total Debit Bulan Ini</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-primary">{{ $countDebit ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-primary">
                                        {{ $format->CurrencyFormat($totalDebit ?? 0) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm">
                                    <i class="ni ni-credit-card text-info me-2"></i>
                                    <span class="font-weight-bold">Total Kredit Bulan Ini</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-info">{{ $countKredit ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-info">
                                        {{ $format->CurrencyFormat($totalKredit ?? 0) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @if (($countDebit ?? 0) > 0 || ($countKredit ?? 0) > 0)
                    <div class="mt-3 text-center">
                        <a href="{{ URL::to('debitKredit') }}" class="btn btn-sm btn-outline-info">
                            <i class="ni ni-bullet-list-67 me-1"></i>
                            Lihat Detail Transaksi Debit Kredit
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        @include('components.chart', [
            'chartId' => 'chartVisualisasiKadmin',
            'title' => 'Visualisasi Data',
            'chartType' => 'bar',
            'labels' => [],
            'data' => [],
            'height' => 300,
            'showTypeSelector' => true,
            'showFilters' => true,
            'showDataSelector' => true,
            'dataType' => 'anggota',
            'ajaxUrl' => route('dashboard.chart-data'),
            'dataOptions' => [
                'anggota' => ['label' => 'Data Anggota'],
                'pinjaman' => ['label' => 'Data Pinjaman'],
                'debitkredit' => ['label' => 'Data Debit Kredit'],
                'keuangan' => ['label' => 'Data Keuangan'],
            ],
        ])
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            
            $('#table_pengajuan').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: true
            });

            
            $('#table_belum_bayar').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: true
            });

        });
    </script>
@endpush
