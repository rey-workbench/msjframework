@props([
    'chartId' => 'chart-' . uniqid(),
    'title' => 'Chart',
    'chartType' => 'bar', 
    'labels' => [],
    'data' => [], 
    'colors' => [],
    'height' => 300,
    'showTypeSelector' => false,
    'availableTypes' => ['bar', 'line', 'pie', 'doughnut', 'radar', 'polarArea'],
    'unit' => '',
    'showPercentage' => false,
    'description' => '',
    'showFilters' => false,
    'dataType' => '',
    'ajaxUrl' => '',
    'currentMonth' => date('m'),
    'currentYear' => date('Y'),
    
    'showDataSelector' => false,
    'dataOptions' => [],
])

<div class="card">
    <div class="card-header pb-0 p-3">
        @if ($showFilters)
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="mb-0">{{ $title }}</h6>
                </div>
                @if ($showTypeSelector)
                    <div class="col-md-3">
                        <label class="form-label text-xs mb-1">Tipe Chart</label>
                        <select id="{{ $chartId }}-type-selector" class="form-select form-select-sm">
                            @foreach ($availableTypes as $type)
                                <option value="{{ $type }}" {{ $type === $chartType ? 'selected' : '' }}>
                                    {{ ucfirst($type) }} Chart
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-{{ $showDataSelector ? '3' : '5' }}">
                    <label class="form-label text-xs mb-1">
                        <span>Filter Periode</span>
                        <span class="badge bg-gradient-info ms-2" id="{{ $chartId }}-period-label">
                            <i class="fas fa-calendar-day me-1"></i>
                            <span id="{{ $chartId }}-period-text">
                                @if ($currentMonth)
                                    {{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }}
                                    {{ $currentYear }}
                                @else
                                    Tahun {{ $currentYear }}
                                @endif
                            </span>
                        </span>
                    </label>
                    <div class="d-flex gap-2">
                        <select id="{{ $chartId }}-filter-month" class="form-select form-select-sm">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                    {{ $currentMonth == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <select id="{{ $chartId }}-filter-year" class="form-select form-select-sm">
                            @for ($i = 2020; $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                @if ($showDataSelector && !empty($dataOptions))
                    <div class="col-md-2">
                        <label class="form-label text-xs mb-1">Pilih Data</label>
                        <select id="{{ $chartId }}-data-selector" class="form-select form-select-sm">
                            @foreach ($dataOptions as $key => $option)
                                <option value="{{ $key }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        @else
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $title }}</h6>
                @if ($showTypeSelector)
                    <select id="{{ $chartId }}-type-selector" class="form-select form-select-sm"
                        style="width: auto;">
                        @foreach ($availableTypes as $type)
                            <option value="{{ $type }}" {{ $type === $chartType ? 'selected' : '' }}>
                                {{ ucfirst($type) }} Chart
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endif
    </div>
    <div class="card-body p-3">
        <div style="height: {{ $height }}px; position: relative;">
            <canvas id="{{ $chartId }}"></canvas>
        </div>
        @if ($description)
            <div class="text-center mt-3">
                <p class="text-xs text-muted mb-0">{{ $description }}</p>
            </div>
        @endif
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            let chart_{{ Str::slug($chartId, '_') }} = null;
            const ctx_{{ Str::slug($chartId, '_') }} = document.getElementById('{{ $chartId }}').getContext(
                '2d');

            let chartData_{{ Str::slug($chartId, '_') }} = {
                labels: {!! json_encode($labels) !!},
                values: {!! json_encode($data) !!}
            };

            const defaultColors = [
                'rgba(67, 97, 238, 0.8)', 
                'rgba(251, 99, 64, 0.8)', 
                'rgba(66, 186, 150, 0.8)', 
                'rgba(234, 84, 85, 0.8)', 
                'rgba(251, 207, 51, 0.8)', 
                'rgba(155, 89, 182, 0.8)', 
                'rgba(52, 152, 219, 0.8)', 
                'rgba(46, 204, 113, 0.8)', 
                'rgba(241, 196, 15, 0.8)', 
                'rgba(149, 165, 166, 0.8)', 
                'rgba(230, 126, 34, 0.8)', 
                'rgba(192, 57, 43, 0.8)' 
            ];

            const chartColors = {!! json_encode($colors) !!}.length > 0 ? {!! json_encode($colors) !!} : defaultColors;
            const borderColors = chartColors.map(color => color.replace('0.8', '1'));

            function createChart_{{ Str::slug($chartId, '_') }}(type) {
                
                if (chart_{{ Str::slug($chartId, '_') }}) {
                    chart_{{ Str::slug($chartId, '_') }}.destroy();
                }

                
                let datasets = [];
                const data = chartData_{{ Str::slug($chartId, '_') }}.values;

                
                if (typeof data === 'object' && !Array.isArray(data)) {
                    
                    let index = 0;
                    for (let key in data) {
                        datasets.push({
                            label: key.charAt(0).toUpperCase() + key.slice(1),
                            data: data[key],
                            backgroundColor: type === 'line' ? 'transparent' : (chartColors[index] ||
                                defaultColors[index]),
                            borderColor: borderColors[index] || defaultColors[index].replace('0.8', '1'),
                            borderWidth: 2,
                            fill: type === 'line' ? false : true,
                            tension: 0.4
                        });
                        index++;
                    }
                } else {
                    
                    datasets.push({
                        label: '{{ $title }}',
                        data: data,
                        backgroundColor: type === 'line' ? 'transparent' : chartColors,
                        borderColor: type === 'line' ? (chartColors[0] || defaultColors[0]).replace('0.8',
                            '1') : borderColors,
                        borderWidth: 2,
                        fill: type === 'line' ? false : true,
                        tension: 0.4,
                        borderRadius: type === 'bar' ? 4 : 0,
                        borderSkipped: false
                    });
                }

                
                chart_{{ Str::slug($chartId, '_') }} = new Chart(ctx_{{ Str::slug($chartId, '_') }}, {
                    type: type,
                    data: {
                        labels: chartData_{{ Str::slug($chartId, '_') }}.labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: datasets.length > 1 || type === 'pie' || type === 'doughnut' ||
                                    type === 'polarArea',
                                position: type === 'pie' || type === 'doughnut' || type === 'polarArea' ?
                                    'bottom' : 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        family: 'Open Sans'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }

                                        
                                        let value;
                                        if (context.parsed.y !== undefined && context.parsed.y !==
                                            null) {
                                            value = context.parsed.y;
                                        } else if (typeof context.parsed === 'number') {
                                            value = context.parsed;
                                        } else if (context.parsed.r !== undefined) {
                                            value = context.parsed.r;
                                        } else {
                                            value = context.formattedValue || context.raw;
                                        }

                                        label += value !== undefined ? value.toLocaleString() : '';

                                        @if ($unit)
                                            label += ' {{ $unit }}';
                                        @endif

                                        @if ($showPercentage)
                                            
                                            if (Array.isArray(chartData_{{ Str::slug($chartId, '_') }}
                                                    .values) &&
                                                (type === 'pie' || type === 'doughnut' || type ===
                                                    'polarArea')) {
                                                const total = chartData_{{ Str::slug($chartId, '_') }}
                                                    .values.reduce((a, b) => {
                                                        return typeof b === 'number' ? a + b : a;
                                                    }, 0);
                                                const percentage = total > 0 ? ((value / total) * 100)
                                                    .toFixed(1) : 0;
                                                label += ' (' + percentage + '%)';
                                            }
                                        @endif

                                        return label;
                                    }
                                }
                            }
                        },
                        scales: type === 'bar' || type === 'line' ? {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false,
                                    display: true,
                                    drawOnChartArea: true,
                                    drawTicks: false,
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    display: true,
                                    padding: 10,
                                    color: '#b2b9bf',
                                    font: {
                                        size: 11,
                                        family: "Open Sans",
                                        style: 'normal',
                                        lineHeight: 2
                                    },
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    drawBorder: false,
                                    display: false,
                                    drawOnChartArea: false,
                                    drawTicks: false
                                },
                                ticks: {
                                    display: true,
                                    color: '#b2b9bf',
                                    padding: 20,
                                    font: {
                                        size: 11,
                                        family: "Open Sans",
                                        style: 'normal',
                                        lineHeight: 2
                                    }
                                }
                            }
                        } : {}
                    }
                });
            }

            @if ($showFilters && $ajaxUrl)
                
                @if ($dataType === 'shu')
                    $('#{{ $chartId }}-filter-month').prop('disabled', true).val('');
                @endif

                
                function updatePeriodLabel_{{ Str::slug($chartId, '_') }}() {
                    const month = $('#{{ $chartId }}-filter-month').val();
                    const year = $('#{{ $chartId }}-filter-year').val();
                    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];

                    let periodText = '';
                    if (month) {
                        periodText = monthNames[parseInt(month)] + ' ' + year;
                    } else {
                        periodText = 'Tahun ' + year;
                    }

                    $('#{{ $chartId }}-period-text').text(periodText);
                }

                
                function loadChartData_{{ Str::slug($chartId, '_') }}() {
                    const month = $('#{{ $chartId }}-filter-month').val();
                    const year = $('#{{ $chartId }}-filter-year').val();
                    const chartType = $('#{{ $chartId }}-type-selector').val() || '{{ $chartType }}';
                    const selectedDataType =
                        @if ($showDataSelector && !empty($dataOptions))
                            $('#{{ $chartId }}-data-selector').val()
                        @else
                            '{{ $dataType }}'
                        @endif ;

                    updatePeriodLabel_{{ Str::slug($chartId, '_') }}();

                    $.ajax({
                        url: '{{ $ajaxUrl }}',
                        method: 'GET',
                        data: {
                            dataType: selectedDataType,
                            month: month,
                            year: year
                        },
                        success: function(response) {
                            chartData_{{ Str::slug($chartId, '_') }}.labels = response.labels;
                            chartData_{{ Str::slug($chartId, '_') }}.values = response.data;
                            createChart_{{ Str::slug($chartId, '_') }}(chartType);
                        },
                        error: function(xhr) {
                        }
                    });
                }

                
                $('#{{ $chartId }}-filter-month, #{{ $chartId }}-filter-year').on('change', function() {
                    loadChartData_{{ Str::slug($chartId, '_') }}();
                });

                @if ($showDataSelector)
                    
                    $('#{{ $chartId }}-data-selector').on('change', function() {
                        loadChartData_{{ Str::slug($chartId, '_') }}();
                    });
                @endif

                @if ($showTypeSelector)
                    
                    $('#{{ $chartId }}-type-selector').on('change', function() {
                        if ('{{ $ajaxUrl }}') {
                            loadChartData_{{ Str::slug($chartId, '_') }}();
                        } else {
                            createChart_{{ Str::slug($chartId, '_') }}($(this).val());
                        }
                    });
                @endif

                
                loadChartData_{{ Str::slug($chartId, '_') }}();
            @else
                @if ($showTypeSelector)
                    
                    $('#{{ $chartId }}-type-selector').on('change', function() {
                        createChart_{{ Str::slug($chartId, '_') }}($(this).val());
                    });
                @endif

                
                createChart_{{ Str::slug($chartId, '_') }}('{{ $chartType }}');
            @endif
        });
    </script>
@endpush
