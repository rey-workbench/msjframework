@if (isset($anggota))
    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Profil Anggota</h6>
                    <p class="text-sm mb-0">Informasi data anggota koperasi</p>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-xs font-weight-bold text-uppercase opacity-7">NIK</label>
                                <p class="text-sm mb-0 font-weight-bold">{{ $anggota->nik ?? 'Tidak tersedia' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-xs font-weight-bold text-uppercase opacity-7">Nama
                                    Lengkap</label>
                                <p class="text-sm mb-0 font-weight-bold">
                                    {{ $anggota->nama_lengkap ?? 'Tidak tersedia' }}</p>
                            </div>
                            <div class="mb-3">
                                <label
                                    class="form-label text-xs font-weight-bold text-uppercase opacity-7">Departemen</label>
                                <p class="text-sm mb-0 font-weight-bold">{{ $anggota->departemen ?? 'Tidak tersedia' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label text-xs font-weight-bold text-uppercase opacity-7">Jabatan</label>
                                <p class="text-sm mb-0 font-weight-bold">{{ $anggota->jabatan ?? 'Tidak tersedia' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-xs font-weight-bold text-uppercase opacity-7">Status
                                    Keanggotaan</label>
                                <div>
                                    <span class="badge badge-sm bg-primary">Aktif</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-xs font-weight-bold text-uppercase opacity-7">Tanggal
                                    Bergabung</label>
                                <p class="text-sm mb-0 font-weight-bold">
                                    {{ $anggota->tanggal_bergabung ? date('d M Y', strtotime($anggota->tanggal_bergabung)) : 'Tidak tersedia' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($anggota->tanggal_bergabung)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase opacity-7">Status
                                        Kelayakan Pinjaman</label>
                                    @php
                                        $joinDate = \Carbon\Carbon::parse($anggota->tanggal_bergabung)->startOfMonth();
                                        $currentDate = \Carbon\Carbon::now()->startOfMonth();
                                        $eligibleDate = $joinDate->copy()->addMonth();
                                    @endphp

                                    @if ($currentDate->greaterThan($joinDate))
                                        <p class="text-sm mb-0 font-weight-bold">
                                            <span class="badge badge-sm bg-primary me-2">Sudah Bisa</span>
                                            Anda sudah dapat mengajukan pinjaman
                                        </p>
                                    @elseif($currentDate->equalTo($joinDate))
                                        <p class="text-sm mb-0 font-weight-bold">
                                            <span class="badge badge-sm bg-warning me-2">Bergabung Bulan Ini</span>
                                        </p>
                                        <p class="text-xs text-muted mb-0 mt-1">
                                            Mendapat Simpanan Pokok, pinjaman mulai {{ $eligibleDate->format('M Y') }}
                                        </p>
                                    @else
                                        <p class="text-sm mb-0 font-weight-bold">
                                            <span class="badge badge-sm bg-danger me-2">Belum Bisa</span>
                                        </p>
                                        <p class="text-xs text-muted mb-0 mt-1">
                                            Dapat mengajukan mulai {{ $eligibleDate->format('M Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase opacity-7">Memenuhi
                                        Syarat
                                        Sejak</label>
                                    <p class="text-sm mb-0 font-weight-bold">
                                        {{ $eligibleDate->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Cicilan Bulan Ini</h6>
                    <p class="text-sm mb-0">Total dari semua pinjaman aktif</p>
                </div>
                <div class="card-body p-3">
                    <div class="text-center">
                        <h4 class="font-weight-bolder text-primary mb-2">
                            {{ $format->CurrencyFormat($totalCicilanBulan ?? 0) }}
                        </h4>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Sisa {{ $sisaBulan ?? 0 }} bulan</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($pinjamanBerjalan)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-2">Pinjaman Berjalan</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">No. Pinjaman</p>
                                    <h6 class="text-sm mb-0">{{ $pinjamanBerjalan->nomor_pinjaman }}</h6>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">Mulai</p>
                                    <h6 class="text-sm mb-0">
                                        {{ date('M Y', strtotime($pinjamanBerjalan->tanggal_pengajuan)) }}</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">Tenor</p>
                                    <h6 class="text-sm mb-0">{{ $pinjamanBerjalan->tenor_pinjaman }} bln</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">Paket</p>
                                    <h6 class="text-sm mb-0">{{ $pinjamanBerjalan->jumlah_paket_dipilih }} paket
                                    </h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">Jumlah</p>
                                    <h6 class="text-sm mb-0">
                                        {{ $format->CurrencyFormat($pinjamanBerjalan->nominal_pinjaman) }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">Cicilan</p>
                                    <h6 class="text-sm mb-0">
                                        {{ $format->CurrencyFormat($totalCicilanBulan) }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">Sisa Bulan</p>
                                    <h6 class="text-sm mb-0">{{ $sisaBulan }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <span class="badge bg-primary">Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-2">Pinjaman Berjalan</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="text-center py-4">
                            <i class="ni ni-money-coins text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-3">Tidak ada pinjaman aktif</h6>
                            <p class="text-sm text-muted">Anda belum memiliki pinjaman yang sedang berjalan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

<div class="row mt-4">
    <div class="col-xl-7 col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Histori Pinjaman</h6>
                <p class="text-sm mb-0">Riwayat pengajuan pinjaman Anda</p>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm align-items-center mb-0">
                        <thead class="thead-light" style="background-color: #00b7bd4f;">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    No. Pinjaman</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Mulai</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Selesai</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Tenor</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Paket</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Jumlah</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historiPinjaman as $pinjaman)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $pinjaman->nomor_pinjaman }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ date('M Y', strtotime($pinjaman->tanggal_pengajuan)) }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @if ($pinjaman->tanggal_selesai)
                                                {{ date('M Y', strtotime($pinjaman->tanggal_selesai)) }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ $pinjaman->tenor_pinjaman }}
                                            bln</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold">{{ $pinjaman->jumlah_paket_dipilih }}
                                            paket</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold">
                                            {{ $format->CurrencyFormat($pinjaman->nominal_pinjaman) }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if ($pinjaman->status_approval == 'approved')
                                            @if ($pinjaman->isactive == '1')
                                                <span class="badge badge-sm bg-primary">Aktif</span>
                                            @else
                                                <span class="badge badge-sm bg-primary">Lunas</span>
                                            @endif
                                        @elseif($pinjaman->status_approval == 'rejected')
                                            <span class="badge badge-sm bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge badge-sm bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="ni ni-archive-2 text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2 mb-0">Belum ada histori pinjaman</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-4 mb-4">
        @if (isset($shuData))
            @php
                

$currentYear = date('Y');
$filteredShuData = array_filter(
    $shuData,
    function ($key) use ($currentYear) {
        return $key < $currentYear;
    },
    ARRAY_FILTER_USE_KEY,
);

                

            @endphp

            @if (!empty($filteredShuData))
                @include('components.chart', [
                    'chartId' => 'chart-shu-anggota',
                    'title' => 'Saldo SHU per Tahun',
                    'chartType' => 'bar',
                    'labels' => array_keys($filteredShuData),
                    'data' => array_map(function ($val) {
                        return $val / 1000;
                    }, array_values($filteredShuData)),
                    'colors' => [
                        'rgba(67, 97, 238, 0.8)', 
                        'rgba(251, 99, 64, 0.8)', 
                        'rgba(66, 186, 150, 0.8)', 
                        'rgba(234, 84, 85, 0.8)', 
                        'rgba(251, 207, 51, 0.8)', 
                    ],
                    'height' => 300,
                    'showTypeSelector' => false,
                    'availableTypes' => ['bar'],
                    'unit' => 'Ribu Rp',
                    'showPercentage' => false,
                    'description' => 'Data SHU tahun-tahun sebelumnya',
                    'showFilters' => false,
                    'dataType' => '',
                    'ajaxUrl' => '',
                    'currentMonth' => '',
                    'currentYear' => date('Y'),
                ])
            @else
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Saldo SHU per Tahun</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="text-center py-4">
                            <i class="ni ni-chart-bar-32 text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-3">Belum ada data SHU</h6>
                            <p class="text-sm text-muted">Data SHU tahun sebelumnya akan muncul di sini</p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Saldo SHU per Tahun</h6>
                </div>
                <div class="card-body p-3">
                    <div class="text-center py-4">
                        <i class="ni ni-chart-bar-32 text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-3">Belum ada data SHU</h6>
                        <p class="text-sm text-muted">Data SHU Anda akan muncul di sini</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>