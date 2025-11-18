@php
    $exportButtons = [];

    if (isset($authorize) && $authorize->excel == '1') {
        $exportButtons[] = [
            'text' =>
                '<i class="fas fa-file-excel me-1 text-lg text-success"></i><span class="font-weight-bold"> Excel</span>',
            'className' => 'btn-export-excel',
            'url' => route('export.data', ['module' => $url_menu, 'type' => 'excel']),
        ];
    }

    if (isset($authorize) && $authorize->pdf == '1') {
        $exportButtons[] = [
            'text' =>
                '<i class="fas fa-file-pdf me-1 text-lg text-danger"></i><span class="font-weight-bold"> PDF</span>',
            'className' => 'btn-export-pdf',
            'url' => route('export.data', ['module' => $url_menu, 'type' => 'pdf']),
        ];
    }

    if (isset($authorize) && $authorize->print == '1') {
        $exportButtons[] = [
            'text' => '<i class="fas fa-print me-1 text-lg text-info"></i><span class="font-weight-bold"> Print</span>',
            'className' => 'btn-export-print',
            'url' => route('export.data', ['module' => $url_menu, 'type' => 'print']),
        ];
    }
@endphp

[
@foreach ($exportButtons as $index => $button)
    {
    text: '{!! $button['text'] !!}',
    className: '{{ $button['className'] }}',
    action: function (e, dt, node, config) {
    @if (isset($button['target']) && $button['target'] === '_blank')
        window.open('{{ $button['url'] }}', '_blank');
    @else
        window.location.href = '{{ $button['url'] }}';
    @endif
    }
    }{{ $index < count($exportButtons) - 1 ? ',' : '' }}
@endforeach
]
