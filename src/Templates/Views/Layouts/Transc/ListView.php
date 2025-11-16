<?php

namespace MSJFramework\LaravelGenerator\Templates\Views\Layouts\Transc;

class ListView
{
    public static function getTemplate(): string
    {
        return <<<'BLADE'
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
                                    <thead class="thead-light" style="background-color: #00b7bd4f;">
                                        <tr>
                                            {{-- retrieve table header --}}
                                            @foreach ($table_header_h as $header_h)
                                                <th>{{ $header_h->alias }}</th>
                                            @endforeach
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- retrieve table detail --}}
                                        @foreach ($table_detail_h as $detail_h)
                                            @php
                                                $primary = '';
                                                foreach ($table_primary_h as $p) {
                                                    $primary == ''
                                                        ? ($primary = $detail_h->{$p->field})
                                                        : ($primary = $primary . ':' . $detail_h->{$p->field});
                                                }
                                            @endphp
                                            <tr>
                                                @foreach ($table_header_h as $field_h)
                                                    @php
                                                        $string = $field_h->field;
                                                    @endphp
                                                    {{-- field type join --}}
                                                    @if ($field_h->type == 'join')
                                                        <td
                                                            class="text-sm font-weight-{{ $field_h->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            @if ($field_h->query != '')
                                                                @php
                                                                    $query =
                                                                        $field_h->query .
                                                                        "'" .
                                                                        $detail_h->$string .
                                                                        "'";
                                                                    $data_query = DB::select($query);
                                                                @endphp
                                                                @foreach ($data_query as $q)
                                                                    <?php $sAsArray = array_values((array) $q); ?>
                                                                    {{ $sAsArray[0] != '' ? $sAsArray[0] : '' }}
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        {{-- field type enum --}}
                                                    @elseif ($field_h->type == 'enum')
                                                        <td
                                                            class="text-sm font-weight-{{ $field_h->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            @if ($field_h->query != '')
                                                                @php
                                                                    $data_query = DB::select($field_h->query);
                                                                @endphp
                                                                @foreach ($data_query as $q)
                                                                    <?php $sAsArray = array_values((array) $q); ?>
                                                                    {{ $detail_h->$string == $sAsArray[0] ? $sAsArray[1] : '' }}
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td
                                                            class="text-sm font-weight-{{ $field_h->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            {{ $detail_h->$string }}</td>
                                                    @endif
                                                @endforeach
                                                <td class="text-sm font-weight-normal">
                                                    {{-- button detail --}}
                                                    <button type="button" class="btn btn-primary mb-0 py-1 px-3"
                                                        title="View Data"
                                                        onclick="detail('{{ encrypt($primary) }}','{{ $gmenuid }}','{{ $dmenu }}','{{ $detail_h->$string }}')">
                                                        <i class="fas fa-info-circle"></i> </i><span
                                                            class="font-weight-bold">
                                                            Detail</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-{{ $colomh > 1 ? '8' : '9' }}">
                <div class="row mx-1">
                    <div class="card" style="min-height: 650px;">
                        <div class="card-header">
                            <h5 class="mb-0" id="label_detail">List Detail</h5>
                        </div>
                        <hr class="horizontal dark mt-0">
                        {{-- alert --}}
                        @include('components.alert')
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
                                                        onclick="window.location='{{ url($url_menu . '/show' . '/' . encrypt($primary)) }}'">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    {{-- check authorize edit --}}
                                                    @if ($authorize->edit == '1')
                                                        {{-- button edit --}}
                                                        <button type="button" class="btn btn-warning mb-0 py-1 px-2"
                                                            title="Edit Data"
                                                            onclick="window.location='{{ url($url_menu . '/edit' . '/' . encrypt($primary)) }}'">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                    {{-- check authorize delete --}}
                                                    @if ($authorize->delete == '1')
                                                        <form
                                                            onsubmit="return deleteData(event,'{{ $primary }}','Hapus')"
                                                            action="{{ url($url_menu . '/' . encrypt($primary)) }}"
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
                                                                @foreach ($data_query as $q)
                                                                    <?php $sAsArray = array_values((array) $q); ?>
                                                                    {{ $detail->$string == $sAsArray[0] ? $sAsArray[1] : '' }}
                                                                @endforeach
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
                                                                <img src="{{ asset('/storage' . '/' . $detail->$string) }}"
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
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }} text-currency text-end">
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
            dom: 'frtip'
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
        });
        //set color button datatables
        $('.dt-button').addClass('btn btn-secondary');
        $('.dt-button').removeClass('dt-button');
        //setting button add
        var id = "{{ Session::has('idtrans') ? encrypt(Session::get('idtrans')) : '' }}";
        var btnadd = $('.btn-add').parents('.btn-secondary');
        btnadd.removeClass('btn-secondary');
        btnadd.addClass('btn btn-primary');
        btnadd.attr('onclick', "window.location='{{ URL::to($url_menu . '/add/') }}" + "/" + id +
            "'");
        //check authorize button datatables
        <?= $authorize->add == '0' ? 'btnadd.remove();' : '' ?>
        <?= $authorize->excel == '0' ? "$('.buttons-excel').remove();" : '' ?>
        <?= $authorize->pdf == '0' ? "$('.buttons-pdf').remove();" : '' ?>
        <?= $authorize->print == '0' ? "$('.buttons-print').remove();" : '' ?>
        // function detail ajax
        function detail(id, gmenu, dmenu) {
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
                    dmenu: dmenu
                },
                success: function(data) {
                    // set title detail
                    $('#label_detail').text('List Detail -> ' + data['ajaxid']);
                    // reset datatables
                    $('#list_detail').DataTable().destroy();
                    $('#list_detail tbody').remove();
                    // retrieve data into tabel
                    $('#list_detail').append('<tbody></tbody>');
                    var i = 1;
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
                        var vtd = `
                                    <td class="text-sm font-weight-normal">
                                        <button type="submit" class="btn btn-primary mb-0 py-1 px-2"
                                            title="View Data"
                                            onclick="window.location='{{ url($url_menu . '/show/') }}` + '/' + data[
                                'encrypt_primary'][
                                index
                            ] + `'">
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
                            ] + `'">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        {{-- check authorize delete --}}
                                        @if ($authorize->delete == '1')
                                            <form onsubmit="return deleteData(event,'` + primary + `','Hapus')"
                                                action="{{ url($url_menu . '/') }}` + '/' + data['encrypt_primary'][
                                index
                            ] + `"
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
                                        vtd +=
                                            `<td class="text-sm font-weight-` +
                                            primary_color + `">` + sAsArray[0] +
                                            `</td>`;
                                        return vtd;
                                    });
                                } else {
                                    vtd +=
                                        `<td class="text-sm font-weight-` + primary_color + `">` +
                                        result[index][result2[index2].field] + `</td>`;
                                }
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
                        $('#list_detail tbody').append(`<tr>` + vtd + `</tr>`);
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
                        "'");
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
BLADE;
    }
}
