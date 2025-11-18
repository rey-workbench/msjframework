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

        <div class="row mx-1">
            <div class="card">
                <div class="row">
                    <div class="card-header col-md-auto">
                        <h5 class="mb-0">{{ $title_menu }}</h5>
                    </div>
                    <div class="col">
                        @include('components.alert')
                    </div>
                </div>
                <hr class="horizontal dark mt-0">

                <div class="row px-4 py-3">
                    @foreach ($report_buttons as $button)
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card report-card h-100">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <h5 class="font-weight-bolder text-dark mb-1">
                                                    {{ $button['title'] }}
                                                </h5>
                                                <p class="text-sm mb-0 text-muted">
                                                    {{ $button['description'] }}
                                                </p>
                                                <div class="mt-3">
                                                    <button class="btn btn-{{ $button['color'] }} btn-sm btn-generate"
                                                        data-type="{{ $button['id'] }}" data-title="{{ $button['title'] }}"
                                                        data-bs-toggle="modal" data-bs-target="#generateModal">
                                                        <i class="{{ $button['icon'] }} me-2"></i>
                                                        Generate {{ $button['title'] }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div
                                                class="icon icon-shape icon-lg {{ $button['bg_class'] }} shadow text-center rounded-circle">
                                                <i class="{{ $button['icon'] }} text-lg text-white opacity-10"
                                                    aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1 text-dark">Informasi Report</h6>
                                        <p class="text-sm mb-0 text-muted">
                                            Generate laporan dari dashboard ini akan langsung mengarahkan Anda ke halaman
                                            detail
                                            laporan dengan data yang sudah terfilter.
                                            Setiap laporan dapat diexport ke Excel, PDF, atau dicetak.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div
                                            class="icon icon-shape icon-lg bg-gradient-info shadow-info text-center rounded-circle">
                                            <i class="fas fa-info-circle text-lg text-white opacity-10"
                                                aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ url('report-rekap/generate') }}" id="form-generate-report">
                            @csrf
                            <input type="hidden" name="report_type" id="report_type" value="">

                            <div class="modal-header">
                                <h5 class="modal-title" id="generateModalLabel">Generate Laporan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div id="filter-content">
                                    <div class="row" id="base-filter">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Bulan <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="bulan" id="bulan">
                                                    <option value="all">Semua Bulan</option>
                                                    <option value="01" {{ date('m') == '01' ? 'selected' : '' }}>Januari</option>
                                                    <option value="02" {{ date('m') == '02' ? 'selected' : '' }}>Februari</option>
                                                    <option value="03" {{ date('m') == '03' ? 'selected' : '' }}>Maret</option>
                                                    <option value="04" {{ date('m') == '04' ? 'selected' : '' }}>April</option>
                                                    <option value="05" {{ date('m') == '05' ? 'selected' : '' }}>Mei</option>
                                                    <option value="06" {{ date('m') == '06' ? 'selected' : '' }}>Juni</option>
                                                    <option value="07" {{ date('m') == '07' ? 'selected' : '' }}>Juli</option>
                                                    <option value="08" {{ date('m') == '08' ? 'selected' : '' }}>Agustus</option>
                                                    <option value="09" {{ date('m') == '09' ? 'selected' : '' }}>September</option>
                                                    <option value="10" {{ date('m') == '10' ? 'selected' : '' }}>Oktober</option>
                                                    <option value="11" {{ date('m') == '11' ? 'selected' : '' }}>November</option>
                                                    <option value="12" {{ date('m') == '12' ? 'selected' : '' }}>Desember</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Tahun <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="tahun" id="tahun"
                                                    min="2020" max="{{ date('Y') + 2 }}"
                                                    value="{{ date('Y') }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="additional-filters" style="display: none;">
                                        <div class="pinjaman-filters" style="display: none;">
                                            <div class="col-12 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Status Approval <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="status_approval"
                                                        id="status_approval">
                                                        <option value="">Pilih Status</option>
                                                        <option value="all">Semua Status</option>
                                                        <option value="approve">Approved</option>
                                                        <option value="rejected">Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="shu-filters" style="display: none;">
                                        </div>

                                        <div class="potongan-filters" style="display: none;">
                                            <div class="col-12 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Jenis Potongan <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="jenis_potongan"
                                                        id="jenis_potongan">
                                                        <option value="">Pilih Jenis Potongan</option>
                                                        <option value="all">Semua Jenis</option>
                                                        <option value="simpanan">Simpanan Saja</option>
                                                        <option value="cicilan">Cicilan Saja</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="debitkredit-filters" style="display: none;">
                                            <div class="col-12 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Jenis Transaksi <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="type" id="type">
                                                        <option value="">Pilih Jenis Transaksi</option>
                                                        <option value="all">Semua Transaksi</option>
                                                        <option value="debit">Debit</option>
                                                        <option value="kredit">Kredit</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="card border-primary">
                                        <div class="card-body p-3">
                                            <h6 class="text-sm mb-2"><i class="fas fa-info-circle me-1"></i> Ringkasan Laporan</h6>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">Jenis:</small>
                                                    <span id="preview-type" class="badge bg-primary text-xs">-</span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">Periode:</small>
                                                    <span id="preview-periode" class="badge bg-secondary text-xs">-</span>
                                                </div>
                                                <div class="col-6" id="preview-filter-container" style="display: none;">
                                                    <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">Filter:</small>
                                                    <span id="preview-filter" class="badge bg-info text-xs">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3" id="report-info">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                        <div id="report-info-content">
                                            <strong>Informasi Generate Laporan:</strong><br>
                                            Pilih jenis laporan untuk melihat informasi lebih detail.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" id="btn-submit-generate">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Generate Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="processingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center py-4">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h6 class="mb-2" id="processing-title">Memproses Laporan...</h6>
                            <p class="text-sm text-muted mb-0" id="processing-subtitle">Mohon tunggu sebentar</p>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-3"
                                id="btn-cancel-processing" style="display: none;">
                                Batalkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @push('js')
            <script>
                $(document).ready(function() {
                    hideStuckModals();
                    cleanupExistingDataTables();
                    initializeGenerateModal();
                    initializeFormSubmission();
                    initializeFilterChanges();
                    handlePageVisibility();
                });


                function initializeGenerateModal() {
                    $('.btn-generate').on('click', function() {
                        const reportType = $(this).data('type');
                        const reportTitle = $(this).data('title');


                        $('#report_type').val(reportType);


                        $('#generateModalLabel').text(`Generate ${reportTitle}`);


                        showFiltersForReportType(reportType);


                        updateReportPreview();


                        updateReportInfo(reportType);


                        resetForm();
                    });
                }


                function showFiltersForReportType(reportType) {

                    $('#additional-filters').hide();
                    $('.pinjaman-filters, .shu-filters, .potongan-filters, .debitkredit-filters').hide();

                    // Reset all conditional required attributes
                    $('#bulan, #status_approval, #jenis_potongan, #type').prop('required', false);

                    $('#base-filter').show();
                    $('#bulan').closest('.col-md-6').show();
                    $('#tahun').closest('.col-md-12').removeClass('col-md-12').addClass('col-md-6');
                    $('#bulan').closest('.col-md-12').removeClass('col-md-12').addClass('col-md-6');

                    // Set bulan as required by default (except for SHU)
                    $('#bulan').prop('required', true);

                    switch (reportType) {
                        case 'laporan_pinjaman':

                            $('#additional-filters').show();
                            $('.pinjaman-filters').show();
                            $('#status_approval').prop('required', true);
                            break;
                        case 'laporan_shu':

                            $('#bulan').closest('.col-md-6').hide();
                            $('#bulan').prop('required', false);
                            $('#tahun').closest('.col-md-6').removeClass('col-md-6').addClass('col-md-12');

                            break;
                        case 'laporan_potongan':

                            $('#additional-filters').show();
                            $('.potongan-filters').show();
                            $('#jenis_potongan').prop('required', true);
                            break;
                        case 'laporan_debitkredit':

                            $('#additional-filters').show();
                            $('.debitkredit-filters').show();
                            $('#type').prop('required', true);
                            break;
                    }
                }


                function updateReportInfo(reportType) {
                    let infoContent = '';

                    switch (reportType) {
                        case 'laporan_pinjaman':
                            infoContent = `
                <strong>Informasi Laporan Pinjaman:</strong><br>
                • Menampilkan data pinjaman dengan status <strong>Approved</strong> dan <strong>Rejected</strong><br>
                • Filter berdasarkan periode pengajuan dan status approval<br>
                • Data meliputi: NIK, Nama, Total Pinjaman, Periode, Tanggal Pengajuan, Status<br>
                • Hasil dapat diexport ke Excel, PDF, atau dicetak
            `;
                            break;
                        case 'laporan_shu':
                            infoContent = `
                <strong>Informasi Laporan SHU:</strong><br>
                • Menampilkan data Sisa Hasil Usaha (SHU) anggota<br>
                • Filter berdasarkan tahun (tanpa filter bulan atau nominal)<br>
                • Data meliputi: NIK, Nama, Periode, Simpanan Total, Bunga, Total SHU<br>
                • Hasil dapat diexport ke Excel, PDF, atau dicetak
            `;
                            break;
                        case 'laporan_potongan':
                            infoContent = `
                <strong>Informasi Laporan Potongan:</strong><br>
                • Menampilkan data potongan gaji anggota<br>
                • Filter berdasarkan periode dan jenis potongan<br>
                • Simpanan Saja: hanya data dengan simpanan > 0 dan cicilan = 0<br>
                • Cicilan Saja: hanya data dengan cicilan > 0 dan simpanan = 0<br>
                • Data meliputi: NIK, Nama, Periode, Simpanan, Cicilan, Total Potongan<br>
                • Hasil dapat diexport ke Excel, PDF, atau dicetak
            `;
                            break;
                        case 'laporan_debitkredit':
                            infoContent = `
                <strong>Informasi Laporan Debit & Kredit:</strong><br>
                • Menampilkan data transaksi debit dan kredit<br>
                • Filter berdasarkan periode dan jenis transaksi (debit/kredit)<br>
                • Data meliputi: ID Transaksi, Tanggal, Jenis, Nominal, Keterangan, User<br>
                • Hasil dapat diexport ke Excel, PDF, atau dicetak
            `;
                            break;
                        default:
                            infoContent = `
                <strong>Informasi Generate Laporan:</strong><br>
                Pilih jenis laporan untuk melihat informasi lebih detail.
            `;
                    }

                    $('#report-info-content').html(infoContent);
                }


                function initializeFormSubmission() {
                    $('#form-generate-report').on('submit', function(e) {
                        e.preventDefault();

                        const reportType = $('#report_type').val();

                        if (!reportType) {
                            hideStuckModals();
                            alert('Tipe laporan tidak valid');
                            return;
                        }


                        $('#generateModal').modal('hide');
                        showProcessingModal();




                        const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();


                        let currentRequest;


                        const formAction = $(this).attr('action');

                        currentRequest = $.ajax({
                                    url: formAction,
                                    method: 'POST',
                                    data: $(this).serialize(),
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    timeout: 60000,
                                    beforeSend: function() {
                                        $('#btn-cancel-processing').show().off('click').on('click', function() {
                                            if (currentRequest) {
                                                currentRequest.abort();
                                                hideProcessingModal();
                                                showErrorMessage('Permintaan dibatalkan oleh pengguna.');
                                            }
                                        });
                                    },
                                    success: function(response) {

                                        hideProcessingModal();

                                        if (response.success) {

                                            showSuccessMessage(
                                                'Laporan berhasil di-generate. Mengarahkan ke halaman laporan...');


                                            setTimeout(function() {
                                                window.location.href = response.redirect_url;
                                            }, 1500);
                                        } else {
                                            showErrorMessage('Error: ' + (response.message ||
                                                'Terjadi kesalahan tidak diketahui'));
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        xhr,
                                        status,
                                        error
                                    }); hideProcessingModal();


                                if (status === 'abort') {
                                    return;
                                }

                                let errorMessage = 'Terjadi kesalahan saat generate laporan.';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.status === 422) {
                                    errorMessage = 'Data yang dimasukkan tidak valid.';
                                } else if (xhr.status === 401) {
                                    errorMessage = 'Sesi login telah berakhir. Silakan login kembali.';
                                    setTimeout(function() {
                                        window.location.href = '/login';
                                    }, 2000);
                                } else if (xhr.status === 403) {
                                    errorMessage = 'Anda tidak memiliki akses untuk fitur ini.';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                                } else if (status === 'timeout') {
                                    errorMessage = 'Request timeout. Silakan coba lagi dalam beberapa saat.';
                                } else if (xhr.status === 0 && status !== 'abort') {
                                    errorMessage = 'Koneksi terputus. Periksa koneksi internet Anda.';
                                }

                                showErrorMessage(errorMessage);
                            },
                            complete: function() {
                                currentRequest = null;
                            }
                    });
                });
                }


                function initializeFilterChanges() {
                    $('#bulan, #tahun, #status_approval, #jenis_potongan, #type, #status_anggota, #departemen').on('change keyup',
                        function() {
                            updateReportPreview();
                        });
                }


                function updateReportPreview() {
                    const reportType = $('#report_type').val();
                    const bulan = $('#bulan').val();
                    const tahun = $('#tahun').val();

                    const reportNames = {
                        'laporan_pinjaman': 'Laporan Pinjaman',
                        'laporan_shu': 'Laporan SHU',
                        'laporan_potongan': 'Laporan Potongan',
                        'laporan_debitkredit': 'Laporan Debit & Kredit'
                    };

                    $('#preview-type').text(reportNames[reportType] || 'Belum dipilih');

                    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];

                    let periodeText = '';
                    if (bulan && bulan !== 'all' && tahun) {
                        periodeText = `${monthNames[parseInt(bulan)]} ${tahun}`;
                    } else if (bulan === 'all' && tahun) {
                        periodeText = `Semua Bulan ${tahun}`;
                    } else if (tahun) {
                        periodeText = `Tahun ${tahun}`;
                    } else if (bulan && bulan !== 'all') {
                        periodeText = `Bulan ${monthNames[parseInt(bulan)]}`;
                    } else {
                        periodeText = 'Semua Periode';
                    }
                    $('#preview-periode').text(periodeText);

                    let filterText = '';
                    let hasFilter = false;

                    switch (reportType) {
                        case 'laporan_pinjaman':
                            const status = $('#status_approval').val();
                            if (status && status !== 'all') {
                                const statusNames = {
                                    'approve': 'Approved',
                                    'pending': 'Pending',
                                    'rejected': 'Rejected'
                                };
                                filterText = `Status: ${statusNames[status] || status}`;
                                hasFilter = true;
                            } else if (status === 'all') {
                                filterText = 'Semua Status';
                                hasFilter = true;
                            }
                            break;
                        case 'laporan_potongan':
                            const jenisPotongan = $('#jenis_potongan').val();
                            if (jenisPotongan && jenisPotongan !== 'all') {
                                const jenisNames = {
                                    'simpanan': 'Simpanan Saja',
                                    'cicilan': 'Cicilan Saja'
                                };
                                filterText = jenisNames[jenisPotongan] || jenisPotongan;
                                hasFilter = true;
                            } else if (jenisPotongan === 'all') {
                                filterText = 'Semua Jenis Potongan';
                                hasFilter = true;
                            }
                            break;
                        case 'laporan_debitkredit':
                            const typeTransaksi = $('#type').val();
                            if (typeTransaksi && typeTransaksi !== 'all') {
                                const typeNames = {
                                    'debit': 'Debit',
                                    'kredit': 'Kredit'
                                };
                                filterText = `Transaksi ${typeNames[typeTransaksi] || typeTransaksi}`;
                                hasFilter = true;
                            } else if (typeTransaksi === 'all') {
                                filterText = 'Semua Transaksi';
                                hasFilter = true;
                            }
                            break;
                    }

                    if (hasFilter) {
                        $('#preview-filter').text(filterText);
                        $('#preview-filter-container').show();
                    } else {
                        $('#preview-filter-container').hide();
                    }
                }


                function resetForm() {
                    $('#form-generate-report')[0].reset();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                    updateReportPreview();
                }


                function formatCurrency(amount) {
                    return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
                }


                function showSuccessMessage(message) {

                    $('.alert-success, .alert-danger').remove();


                    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;


                    $('.container-fluid .row:first').prepend('<div class="col-12">' + alertHtml + '</div>');


                    setTimeout(function() {
                        $('.alert-success').fadeOut();
                    }, 5000);
                }


                function showErrorMessage(message) {

                    $('.alert-success, .alert-danger').remove();


                    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;


                    $('.container-fluid .row:first').prepend('<div class="col-12">' + alertHtml + '</div>');


                    setTimeout(function() {
                        $('.alert-danger').fadeOut();
                    }, 10000);
                }


                function showProcessingModal() {

                    $('#processing-title').text('Memproses Laporan...');
                    $('#processing-subtitle').text('Mohon tunggu sebentar');
                    $('#btn-cancel-processing').hide();


                    $('.spinner-border').show();


                    $('#processingModal').modal('show');


                    setTimeout(function() {
                        if ($('#processingModal').hasClass('show')) {
                            $('#btn-cancel-processing').fadeIn();
                        }
                    }, 10000);
                }


                function hideProcessingModal() {
                    $('#processingModal').modal('hide');


                    $('#btn-cancel-processing').hide().off('click');
                    $('.spinner-border').show();


                    setTimeout(function() {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }, 300);
                }


                function cleanupExistingDataTables() {

                    $('[id^="list_"]').each(function() {
                        if ($.fn.DataTable.isDataTable('#' + this.id)) {
                            try {
                                $('#' + this.id).DataTable().destroy();
                            } catch (error) {

                                $('#' + this.id).removeData('DataTable');
                            }
                        }
                    });


                    hideStuckModals();
                }


                function hideStuckModals() {

                    if ($('#processingModal').hasClass('show') || $('#processingModal').is(':visible')) {
                        hideProcessingModal();
                    }


                    if ($('#generateModal').hasClass('show') || $('#generateModal').is(':visible')) {
                        $('#generateModal').modal('hide');
                    }


                    $('.modal-backdrop').remove();


                    $('body').removeClass('modal-open');


                    $('body').css({
                        'overflow': '',
                        'padding-right': ''
                    });


                    $('#btn-cancel-processing').hide().off('click');
                }


                function handlePageVisibility() {

                    document.addEventListener('visibilitychange', function() {
                        if (!document.hidden) {
                            cleanupExistingDataTables();
                            hideStuckModals();
                        }
                    });


                    window.addEventListener('pageshow', function(event) {
                        if (event.persisted) {
                            cleanupExistingDataTables();
                            hideStuckModals();
                        }
                    });


                    window.addEventListener('focus', function() {
                        hideStuckModals();
                    });


                    $(document).ready(function() {
                        setTimeout(function() {
                            hideStuckModals();
                        }, 100);
                    });
                }


                window.ReportRekapJS = {
                    hideStuckModals: hideStuckModals,
                    cleanupExistingDataTables: cleanupExistingDataTables,
                    showProcessingModal: showProcessingModal,
                    hideProcessingModal: hideProcessingModal,
                    forceCleanup: function() {
                        hideStuckModals();
                        cleanupExistingDataTables();
                    }
                };
            </script>
        @endpush
