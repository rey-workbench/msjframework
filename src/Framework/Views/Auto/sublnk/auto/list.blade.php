@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
{{-- section content --}}
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-{{ $colomh > 1 ? '4' : '3' }}">
                <div class="row mb-2 mx-1">
                    <div class="card" style="min-height: 650px;">
                        <div class="card-header">
                            <h5 class="mb-0">List {{ $title_menu }}</h5>
                        </div>
                        <hr class="horizontal dark mt-0">
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="list_header">
                                    {{-- check result data --}}
                                    @if ($table_result)
                                        <thead class="thead-light" style="background-color: #00b7bd4f;">
                                            <tr>
                                                {{-- set table header --}}
                                                @foreach ($table_result as $result)
                                                    @php
                                                        $sAsArray = array_keys((array) $result);
                                                    @endphp
                                                @endforeach
                                                @foreach ($sAsArray as $header)
                                                    @if ($header == 'Detail')
                                                        <th style="width: 100%;">{{ $header }}</th>
                                                    @endif
                                                @endforeach
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- retrieve table result --}}
                                            @foreach ($table_result as $detail)
                                                <tr
                                                    {{ @$detail->isactive == '0' || @$detail->ISACTIVE == '0' ? 'style=background-color:#ffe9ed;' : '' }}>
                                                    @foreach ($table_result as $result)
                                                        @php
                                                            $field = array_keys((array) $result);
                                                            $vgmenu = '';
                                                            $vdmenu = '';
                                                            $vicon = '';
                                                            $vtabel = '';
                                                        @endphp
                                                    @endforeach
                                                    @foreach ($field as $header)
                                                        @if ($header == 'Detail')
                                                            <td class="text-sm font-weight-normal">
                                                                {{ $detail->$header }}</td>
                                                        @elseif ($header == 'gmenu')
                                                            @php
                                                                $vgmenu = $detail->$header;
                                                            @endphp
                                                        @elseif ($header == 'dmenu')
                                                            @php
                                                                $vdmenu = $detail->$header;
                                                            @endphp
                                                        @elseif ($header == 'icon')
                                                            @php
                                                                $vicon = $detail->$header;
                                                            @endphp
                                                        @elseif ($header == 'tabel')
                                                            @php
                                                                $vtabel = $detail->$header;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                    <td class="text-sm font-weight-normal">
                                                        {{-- button detail --}}
                                                        <button type="button" class="btn btn-dark mb-0 py-2 px-3"
                                                            title="View Data"
                                                            onclick="detail('{{ @$_GET['id'] }}','{{ $vgmenu }}','{{ $vdmenu }}','{{ $vtabel }}')">
                                                            <i class="ni {{ $vicon }}"></i> </i><span
                                                                class="font-weight-bold">
                                                                List</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-{{ $colomh > 1 ? '8' : '9' }}">
                <div class="row mx-1">
                    <div class="card" style="min-height: 650px;">
                        <div class="row">
                            <div class="card-header col-md-auto">
                                <h5 class="mb-0" id="label_detail">List Detail ->
                                    {{ @$_GET['id'] ? decrypt(@$_GET['id']) : '' }}</h5>
                            </div>
                            <div class="col">
                                {{-- alert --}}
                                @include('components.alert')
                            </div>
                        </div>
                        <hr class="horizontal dark mt-0">
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="list_detail">
                                    <thead class="thead-light" style="background-color: #00b7bd4f;">
                                        <tr>
                                            <th>Action</th>
                                            {{-- retrieve table header --}}
                                            @foreach ($table_header_d as $header_d)
                                                <th>{{ $header_d->alias }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- retrieve table detail --}}
                                        @foreach ($table_detail_d as $detail)
                                            @php
                                                $primary = '';
                                                foreach ($table_primary_h as $h) {
                                                    $primary == ''
                                                        ? ($primary = $detail->{$h->field})
                                                        : ($primary = $primary . ':' . $detail->{$h->field});
                                                }
                                                foreach ($table_primary_d as $p) {
                                                    $primary == ''
                                                        ? ($primary = $detail->{$p->field})
                                                        : ($primary = $primary . ':' . $detail->{$p->field});
                                                }
                                            @endphp
                                            <tr {{ $detail->isactive == '0' ? 'style=background-color:#ffe9ed;' : '' }}>
                                                <td class="text-sm font-weight-normal">
                                                    <button type="submit" class="btn btn-primary mb-0 py-1 px-2"
                                                        title="View Data"
                                                        onclick="window.location='{{ url($url_menu . '/show' . '/' . encrypt($primary) . '?gmenu=' . @$_GET['gmenu'] . '&dmenu=' . @$_GET['dmenu'] . '&tabel=' . @$_GET['tabel']) }}'">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    {{-- check authorize edit --}}
                                                    @if ($authorize->edit == '1')
                                                        {{-- check status active --}}
                                                        @if ($detail->isactive == 1)
                                                            {{-- button edit --}}
                                                            <button type="button" class="btn btn-warning mb-0 py-1 px-2"
                                                                title="Edit Data"
                                                                onclick="window.location='{{ url($url_menu . '/edit' . '/' . encrypt($primary) . '?gmenu=' . @$_GET['gmenu'] . '&dmenu=' . @$_GET['dmenu'] . '&tabel=' . @$_GET['tabel']) }}'">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                    {{-- check authorize delete --}}
                                                    @if ($authorize->delete == '1')
                                                        <form
                                                            onsubmit="return deleteData(event,'{{ $primary }}','{{ $detail->isactive == '0' ? 'Aktifkan' : 'Non Aktifkan' }}')"
                                                            action="{{ url($url_menu . '/' . encrypt($primary) . '?gmenu=' . @$_GET['gmenu'] . '&dmenu=' . @$_GET['dmenu'] . '&tabel=' . @$_GET['tabel']) }}"
                                                            method="POST" style="display: inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            {{-- button delete --}}
                                                            <button type="submit" class="btn btn-danger mb-0 py-1 px-2"
                                                                title="Hapus Data">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                                {{-- retrieve table detail --}}
                                                @foreach ($table_header_d as $field)
                                                    @php
                                                        $string = $field->field;
                                                    @endphp
                                                    {{-- field type enum --}}
                                                    @if ($field->type == 'enum')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            @if ($field->query != '')
                                                                @php
                                                                    $data_query = DB::select($field->query);
                                                                @endphp
                                                                @if (Str::contains($field->class, 'select-multiple'))
                                                                    <?php
                                                                    $multiple = array_map('trim', explode(',', $detail->$string));
                                                                    $text = '';
                                                                    ?>
                                                                    @foreach ($data_query as $q)
                                                                        @php
                                                                            $sAsArray = array_values((array) $q);
                                                                            if (in_array($sAsArray[0], $multiple)) {
                                                                                if ($text == '') {
                                                                                    $text = $sAsArray[1];
                                                                                } else {
                                                                                    $text = $text . ',' . $sAsArray[1];
                                                                                }
                                                                            }
                                                                        @endphp
                                                                    @endforeach
                                                                    {{ $text }}
                                                                @else
                                                                    @foreach ($data_query as $q)
                                                                        <?php $sAsArray = array_values((array) $q); ?>
                                                                        {{ $detail->$string == $sAsArray[0] ? $sAsArray[1] : '' }}
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        </td>
                                                        {{-- field type file --}}
                                                    @elseif ($field->type == 'file')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            @if ($detail->$string)
                                                                <a target="_blank"
                                                                    class="btn btn-sm btn-outline-success mb-0 py-1 px-2"
                                                                    href="{{ asset('/storage' . '/' . $detail->$string) }}">
                                                                    <i aria-hidden="true" class="fas fa-file-lines text-lg">
                                                                    </i>
                                                                    {{ $field->alias }}</a>
                                                            @endif
                                                        </td>
                                                        {{-- field type image --}}
                                                    @elseif($field->type == 'image')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            <span class="my-2 text-xs">
                                                                <img src="{{ file_exists(public_path('storage/' . $detail->$string . 'tumb.png')) ? asset('/storage' . '/' . $detail->$string . 'tumb.png') : asset('storage/' . $detail->$string) }}"
                                                                    alt="image" style="height: 35px;"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#imageModal{{ $field->field }}">
                                                            </span>
                                                            <span
                                                                style="display: none;">{{ asset('/storage' . '/' . $detail->$string) }}
                                                            </span>
                                                            <!-- Modal -->
                                                            <div class="modal fade" id="imageModal{{ $field->field }}"
                                                                tabindex="-1" role="dialog"
                                                                aria-labelledby="imageModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="imageModalLabel">
                                                                                Preview Image
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <img src="{{ asset('/storage' . '/' . $detail->$string) }}"
                                                                                id="preview" alt="image"
                                                                                class="w-100 border-radius-lg shadow-sm">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        {{-- field type join --}}
                                                    @elseif($field->type == 'join')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            @if ($field->query != '')
                                                                @php
                                                                    $query =
                                                                        $field->query . "'" . $detail->$string . "'";
                                                                    $data_query = DB::select($query);
                                                                @endphp
                                                                @foreach ($data_query as $q)
                                                                    <?php $sAsArray = array_values((array) $q); ?>
                                                                    @if ($field->default != 'image')
                                                                        {{ $sAsArray[0] != '' ? $sAsArray[0] : '' }}
                                                                    @else
                                                                        <span class="my-2 text-xs">
                                                                            <img src="{{ asset('/storage' . '/' . $sAsArray[0]) }}"
                                                                                alt="image" style="height: 40px;"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#imageModalJoin{{ $primary }}">
                                                                        </span>
                                                                        <span
                                                                            style="display: none;">{{ asset('/storage' . '/' . $sAsArray[0]) }}
                                                                        </span>
                                                                        <!-- Modal -->
                                                                        <div class="modal fade"
                                                                            id="imageModalJoin{{ $primary }}"
                                                                            tabindex="-1" role="dialog"
                                                                            aria-labelledby="imageModalJoinLabel"
                                                                            aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered"
                                                                                role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title"
                                                                                            id="imageModalJoinLabel">
                                                                                            Preview Image
                                                                                        </h5>
                                                                                        <button type="button"
                                                                                            class="btn-close"
                                                                                            data-bs-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span
                                                                                                aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <img src="{{ asset('/storage' . '/' . $sAsArray[0]) }}"
                                                                                            id="preview" alt="image"
                                                                                            class="w-100 border-radius-lg shadow-sm">
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        {{-- field type currency --}}
                                                    @elseif ($field->type == 'currency')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            {{ $format->CurrencyFormat($detail->$string, $field->decimals, $field->sub) }}
                                                        </td>
                                                    @else
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            {{ $detail->$string }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- check flag js on dmenu --}}
    @if ($jsmenu == '1')
        @if (view()->exists("js.{$dmenu}"))
            @push('addjs')
                {{-- file js in folder (resources/views/js) --}}
                @include('js.' . $dmenu);
            @endpush
        @else
            @push('addjs')
                <script>
                    Swal.fire({
                        title: 'JS Not Found!!',
                        text: 'Please Create File JS',
                        icon: 'error',
                        confirmButtonColor: '#028284'
                    });
                </script>
            @endpush
        @endif
    @endif
@endsection
@push('js')
    <script>
        let columnAbjad = '';
        $(document).ready(function() {
            let numColumns = $('#list_{{ $dmenu }}').DataTable().columns().count();
            let columnNames = '';

            for (let index = 0; index < numColumns; index++) {
                columnNames = $('#list_{{ $dmenu }}').DataTable().columns(index).header()[0].textContent;
                if (columnNames == 'Status' || columnNames == 'status') {
                    columnAbjad = String.fromCharCode(65 + index);
                }
            }
        });
        //set table header into datatables
        $('#list_header').DataTable({
            "language": {
                "search": "Cari :",
                "lengthMenu": "Tampilkan _MENU_ baris",
                "zeroRecords": "Maaf - Data tidak ada",
                "info": "Data _START_ - _END_ dari _TOTAL_",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(pencarian dari _MAX_ data)"
            },
            "pageLength": 15,
            responsive: true,
            dom: ''
        });
        //set table detail into datatables
        $('#list_detail').DataTable({
            "language": {
                "search": "Cari :",
                "lengthMenu": "Tampilkan _MENU_ baris",
                "zeroRecords": "Maaf - Data tidak ada",
                "info": "Data _START_ - _END_ dari _TOTAL_",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(pencarian dari _MAX_ data)"
            },
            "pageLength": 15,
            responsive: true,
            dom: 'Bfrtip',
            @php
            if(isset($_GET['gmenu']))
            {
                @endphp
                buttons: [{
                        text: '<i class="fas fa-plus me-1 text-lg btn-add"> </i><span class="font-weight-bold"> Tambah'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1 text-lg text-success"> </i><span class="font-weight-bold"> Excel',
                        autoFilter: true,
                        sheetName: 'Exported data',
                        // title: 'Nama File Excel',
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            // Loop over the cells in column
                            sheet.querySelectorAll('row c[r^="' + columnAbjad + '"]').forEach((row) => {
                                // Get the value
                                let cell = row.querySelector('is t');
                                if (cell && cell.textContent === 'Not Active') {
                                    row.setAttribute('s', '10'); //red background
                                }
                            });
                        },
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf me-1 text-lg text-danger"> </i><span class="font-weight-bold"> PDF',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1 text-lg text-info"> </i><span class="font-weight-bold"> Print',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                    },
                ]
                @php
            }
            else
            {
                @endphp
                buttons: []
                @php
            }
            @endphp
        });
        //set color button datatables
        $('.dt-button').addClass('btn btn-secondary');
        $('.dt-button').removeClass('dt-button');
        //setting button add
        var id = "{{ Session::has('idtrans') ? encrypt(Session::get('idtrans')) : '' }}";
        var btnadd = $('.btn-add').parents('.btn-secondary');
        btnadd.removeClass('btn-secondary');
        btnadd.addClass('btn btn-primary');
        btnadd.attr('onclick', "window.location='{{ URL::to($url_menu . '/add/') }}" + "/{{ $_GET['id'] }}" +
            "/?gmenu={{ @$_GET['gmenu'] }}&dmenu={{ @$_GET['dmenu'] }}&tabel={{ @$_GET['tabel'] }}" +
            "'");
        //check authorize button datatables
        <?= $authorize->add == '0' ? 'btnadd.remove();' : '' ?>
        <?= $authorize->excel == '0' ? "$('.buttons-excel').remove();" : '' ?>
        <?= $authorize->pdf == '0' ? "$('.buttons-pdf').remove();" : '' ?>
        <?= $authorize->print == '0' ? "$('.buttons-print').remove();" : '' ?>
        // function detail ajax
        function detail(id, gmenu, dmenu, tabel) {
            $.ajax({
                url: "{{ url($url_menu) . '/ajax' }}",
                type: "GET",
                dataType: "JSON",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                    gmenu: gmenu,
                    dmenu: dmenu,
                    tabel: tabel
                },
                success: function(data) {
                    // set title detail
                    $('#label_detail').text('List Detail -> ' + data['ajaxid']);
                    // reset datatables
                    $('#list_detail').DataTable().destroy();
                    $('#list_detail thead').remove();
                    $('#list_detail tbody').remove();
                    // retrieve data into tabel
                    $('#list_detail').append(
                        '<thead class="thead-light" style="background-color: #00b7bd4f;"></thead>');
                    $('#list_detail').append('<tbody></tbody>');
                    var i = 1;
                    var result_h = eval(data['table_header_d']);
                    var vth = '';
                    //looping data header
                    for (var index in result_h) {
                        vth += `<th>` + result_h[index].alias + `</th>`;
                    }
                    // add header into table
                    $('#list_detail thead').append(`<tr><th>Action</th>` + vth + `</tr>`);
                    var result = eval(data['table_detail_d_ajax']);
                    //looping data detail
                    for (var index in result) {
                        var result1 = eval(data['table_primary_d_ajax']);
                        var result2 = eval(data['table_header_d_ajax']);
                        var primary = '';
                        for (var index1 in result1) {
                            // initialized primary key
                            primary == '' ? (primary = result[index][result1[index1].field]) : (primary =
                                primary + ':' + result[index][result1[index1].field]);
                        }
                        // set variable columns
                        if (result[index].isactive == '1') {
                            var vtd = `
                                    <td class="text-sm font-weight-normal">
                                        <button type="submit" class="btn btn-primary mb-0 py-1 px-2"
                                            title="View Data"
                                            onclick="window.location='{{ url($url_menu . '/show/') }}` + '/' + data[
                                    'encrypt_primary'][
                                    index
                                ] + `/?gmenu=` + gmenu + `&dmenu=` + dmenu + `&tabel=` + tabel + `'">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        {{-- check authorize edit --}}
                                        @if ($authorize->edit == '1')
                                            {{-- button edit --}}
                                            <button type="button" class="btn btn-warning mb-0 py-1 px-2"
                                                title="Edit Data"
                                                onclick="window.location='{{ url($url_menu . '/edit/') }}` + '/' +
                                data[
                                    'encrypt_primary'][
                                    index
                                ] + `/?gmenu=` + gmenu + `&dmenu=` + dmenu + `&tabel=` + tabel + `'">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        {{-- check authorize delete --}}
                                        @if ($authorize->delete == '1')
                                            <form onsubmit="return deleteData(event,'` + primary + `','` + (result[
                                    index].isactive == '0' ? 'Aktifkan' : 'Non Aktifkan') + `')"
                                                action="{{ url($url_menu . '/') }}` + '/' + data['encrypt_primary'][
                                    index
                                ] + `?gmenu=` + gmenu + `&dmenu=` + dmenu + `&tabel=` + tabel + `"
                                                method="POST" style="display: inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger mb-0 py-1 px-2"
                                                    title="Hapus Data">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                            `;
                        } else {
                            var vtd = `
                                    <td class="text-sm font-weight-normal">
                                        <button type="submit" class="btn btn-primary mb-0 py-1 px-2"
                                            title="View Data"
                                            onclick="window.location='{{ url($url_menu . '/show/') }}` + '/' + data[
                                'encrypt_primary'][
                                index
                            ] + `/?gmenu=` + gmenu + `&dmenu=` + dmenu + `&tabel=` + tabel + `'">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        {{-- check authorize delete --}}
                                        @if ($authorize->delete == '1')
                                            <form onsubmit="return deleteData(event,'` + primary + `','` + (result[
                                index].isactive == '0' ? 'Aktifkan' : 'Non Aktifkan') + `')"
                                                action="{{ url($url_menu . '/') }}` + '/' + data['encrypt_primary'][
                                index
                            ] + `?gmenu=` + gmenu + `&dmenu=` + dmenu + `&tabel=` + tabel + `"
                                                method="POST" style="display: inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger mb-0 py-1 px-2"
                                                    title="Hapus Data">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                            `;
                        }
                        for (var index2 in result2) {
                            var primary_color = result2[index2].primary == '1' ? 'bold text-dark' : 'normal';
                            var vtex = result[index][result2[index2].field];
                            if (result2[index2].type == 'enum') {
                                if (result2[index2].query != '') {
                                    var query = result2[index2].query;
                                    var v_enum = eval(data[result2[index2].field]);
                                    $.each(v_enum, function(index, q) {
                                        var sAsArray = $.map(q, function(value,
                                            key) {
                                            return value;
                                        });
                                        if (vtex == sAsArray[0]) {
                                            vtd +=
                                                `<td class="text-sm font-weight-` +
                                                primary_color + `">` + sAsArray[1] +
                                                `</td>`;
                                        }
                                        return vtd;
                                    });
                                } else {
                                    vtd +=
                                        `<td class="text-sm font-weight-` + primary_color + `">` +
                                        result[index][result2[index2].field] + `</td>`;
                                }
                            } else if (result2[index2].type == 'join') {
                                if (result2[index2].query != '') {
                                    var query = result2[index2].query + "'" + result2[index2].field + "'";
                                    var v_enum = eval(data['data_join'][index]);
                                    $.each(v_enum, function(index, q) {
                                        var sAsArray = $.map(q, function(value,
                                            key) {
                                            return value;
                                        });
                                        if (result2[index2].default != 'image') {
                                            vtd +=
                                                `<td class="text-sm font-weight-` +
                                                primary_color + `">` + sAsArray[0] +
                                                `</td>`;
                                        } else {
                                            vtd +=
                                                `<td class="text-sm font-weight-` + primary_color + `">
                                                <span class="my-2 text-xs">
                                                    <img src="{{ asset('/storage') }}/` + sAsArray[0] + `"
                                                        alt="image" style="height: 35px;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageModal` + result2[index2].field.replace(
                                                    /:/g,
                                                    "") + index + `">
                                                </span>
                                                <span
                                                    style="display: none;">{{ asset('/storage') }}/ ` + sAsArray[0] + `
                                                </span>
                                                <!-- Modal -->
                                                <div class="modal fade" id="imageModal` + result2[index2].field
                                                .replace(
                                                    /:/g, "") + index + `"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="imageModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="imageModalLabel">
                                                                    Preview Image
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ asset('/storage') }}/` + sAsArray[0] + `"
                                                                    id="preview" alt="image"
                                                                    class="w-100 border-radius-lg shadow-sm">
                                                            </div>
                                                            <div class="modal-footer">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>`;
                                        }
                                        return vtd;
                                    });
                                } else {
                                    vtd +=
                                        `<td class="text-sm font-weight-` + primary_color + `">` +
                                        result[index][result2[index2].field] + `</td>`;
                                }
                            } else if (result2[index2].type == 'image') {
                                vtd +=
                                    `<td class="text-sm font-weight-` + primary_color + `">
                                            <span class="my-2 text-xs">
                                                <img src="{{ asset('/storage') }}/` + result[index][result2[
                                        index2].field] + `"
                                                    alt="image" style="height: 35px;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#imageModal` + result2[index2].field.replace(/:/g,
                                        "") + index + `">
                                            </span>
                                            <span
                                                style="display: none;">{{ asset('/storage') }}/ ` + result[index]
                                    [result2[index2].field] + `
                                            </span>
                                            <!-- Modal -->
                                            <div class="modal fade" id="imageModal` + result2[index2].field.replace(
                                        /:/g, "") + index + `"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="imageModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="imageModalLabel">
                                                                Preview Image
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="{{ asset('/storage') }}/` + result[index][
                                        result2[index2].field
                                    ] + `"
                                                                id="preview" alt="image"
                                                                class="w-100 border-radius-lg shadow-sm">
                                                        </div>
                                                        <div class="modal-footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>`;
                            } else if (result2[index2].type == 'currency') {
                                vtd +=
                                    `<td class="text-sm font-weight-` + primary_color +
                                    `">` + currencyFormat(result[index][result2[index2].field], result2[index2]
                                        .decimals, result2[index2].sub) + `</td>`;
                            } else {
                                vtd +=
                                    `<td class="text-sm font-weight-` + primary_color + `">` +
                                    result[index][result2[index2].field] + `</td>`;
                            }
                        }
                        i++;
                        // add rows into table
                        $('#list_detail tbody').append(
                            `<tr ` + (result[index].isactive == '0' ?
                                'class=not style=background-color:#ffe9ed;' : '') + `>` +
                            vtd + `</tr>`);
                    }
                    // initialized datatbles
                    let columnAbjad = '';
                    $(document).ready(function() {
                        let numColumns = $('#list_{{ $dmenu }}').DataTable().columns()
                            .count();
                        let columnNames = '';
                        // set color datatables
                        for (let index = 0; index < numColumns; index++) {
                            columnNames = $('#list_{{ $dmenu }}').DataTable().columns(index)
                                .header()[0].textContent;
                            if (columnNames == 'Status' || columnNames == 'status') {
                                columnAbjad = String.fromCharCode(65 + index);
                            }
                        }
                        // function set color row DataTable
                        $('.odd').click(function() {
                            $('.odd').css('background-color', '');
                            $('.even').css('background-color', '');
                            $('.not').css('background-color', '#ffe9ed');
                            $(this).css('background-color', '#f9f4ea');
                        });
                        $('.even').click(function() {
                            $('.odd').css('background-color', '');
                            $('.even').css('background-color', '');
                            $('.not').css('background-color', '#ffe9ed');
                            $(this).css('background-color', '#f9f4ea');
                        });
                        var table = $('#list_{{ @$dmenu }}').DataTable();
                        // Add the click event to all rows on all pages
                        table.on('draw', function() {
                            $('.odd').click(function() {
                                $('.odd').css('background-color', '');
                                $('.even').css('background-color', '');
                                $('.not').css('background-color', '#ffe9ed');
                                $(this).css('background-color', '#f9f4ea');
                            });
                            $('.even').click(function() {
                                $('.odd').css('background-color', '');
                                $('.even').css('background-color', '');
                                $('.not').css('background-color', '#ffe9ed');
                                $(this).css('background-color', '#f9f4ea');
                            });
                        });
                        // end function set color row DataTable
                    });
                    //redraw table into datatables
                    $('#list_detail').DataTable({
                        "language": {
                            "search": "Cari :",
                            "lengthMenu": "Tampilkan _MENU_ baris",
                            "zeroRecords": "Maaf - Data tidak ada",
                            "info": "Data _START_ - _END_ dari _TOTAL_",
                            "infoEmpty": "Tidak ada data",
                            "infoFiltered": "(pencarian dari _MAX_ data)"
                        },
                        "pageLength": 15,
                        responsive: true,
                        dom: 'Bfrtip',
                        buttons: [{
                                text: '<i class="fas fa-plus me-1 text-lg btn-add"> </i><span class="font-weight-bold"> Tambah'
                            },
                            {
                                extend: 'excelHtml5',
                                text: '<i class="fas fa-file-excel me-1 text-lg text-success"> </i><span class="font-weight-bold"> Excel',
                                autoFilter: true,
                                sheetName: 'Exported data',
                                // title: 'Nama File Excel',
                                customize: function(xlsx) {
                                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                    // Loop over the cells in column
                                    sheet.querySelectorAll('row c[r^="' + columnAbjad +
                                            '"]')
                                        .forEach((row) => {
                                            // Get the value
                                            let cell = row.querySelector('is t');
                                            if (cell && cell.textContent ===
                                                'Not Active') {
                                                row.setAttribute('s',
                                                    '10'); //red background
                                            }
                                        });
                                },
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                },
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="fas fa-file-pdf me-1 text-lg text-danger"> </i><span class="font-weight-bold"> PDF',
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                },
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print me-1 text-lg text-info"> </i><span class="font-weight-bold"> Print',
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                },
                            },
                        ]
                    }).draw();
                    //set color button datatables
                    $('.dt-button').addClass('btn btn-secondary');
                    $('.dt-button').removeClass('dt-button');
                    //setting button add
                    var btnadd = $('.btn-add').parents('.btn-secondary');
                    btnadd.removeClass('btn-secondary');
                    btnadd.addClass('btn btn-primary');
                    btnadd.attr('onclick', "window.location='{{ URL::to($url_menu . '/add/') }}" + "/" +
                        id +
                        "/?gmenu=" + gmenu + "&dmenu=" + dmenu + "&tabel=" + tabel + "'");
                    //check authorize button datatables
                    <?= $authorize->add == '0' ? 'btnadd.remove();' : '' ?>
                    <?= $authorize->excel == '0' ? "$('.buttons-excel').remove();" : '' ?>
                    <?= $authorize->pdf == '0' ? "$('.buttons-pdf').remove();" : '' ?>
                    <?= $authorize->print == '0' ? "$('.buttons-print').remove();" : '' ?>
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        title: 'Sorry!',
                        text: 'Error Get data!',
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        }
        //function delete
        function deleteData(event, name, msg) {
            event.preventDefault(); // Prevent default form submission
            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda Yakin ${msg} Data ${name} ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Ya, ${msg}`,
                cancelButtonText: 'Batal',
                confirmButtonColor: '#028284'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Find the closest form element and submit it manually
                    event.target.closest('form').submit();
                }
            });
        }
        // function currency
        function currencyFormat(nominal, decimal = 0, prefix = 'Rp.') {
            return prefix + ' ' + parseFloat(nominal).toFixed(decimal).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    </script>
@endpush
