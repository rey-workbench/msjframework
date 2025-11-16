<?php

namespace MSJFramework\LaravelGenerator\Services\Templates\Views\Auto;

class MasterListView
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
            <div class="col-md-12">
                <div class="row mx-1">
                    <div class="card">
                        <div class="row">
                            <div class="card-header col-md-auto">
                                <h5 class="mb-0">List {{ $title_menu }}</h5>
                            </div>
                            <div class="col">
                                {{-- alert --}}
                                @include('components.alert')
                            </div>
                        </div>
                        <hr class="horizontal dark mt-0">
                        <div class="row px-4 py-2">
                            <div class="col-lg">
                                <div class="nav-wrapper">
                                    {{-- check authorize add --}}
                                    @if ($authorize->add == '1')
                                        {{-- button add --}}
                                        <button class="btn btn-primary mb-0"
                                            onclick="window.location='{{ URL::to($url_menu . '/add') }}'"><i
                                                class="fas fa-plus me-1"> </i><span class="font-weight-bold">Tambah</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="list_{{ $dmenu }}">
                                    <thead class="thead-light" style="background-color: #00b7bd4f;">
                                        <tr>
                                            <th width="110">Action</th>
                                            <th>No</th>
                                            {{-- retrieve table header --}}
                                            @foreach ($table_header as $header)
                                                <th>{{ $header->alias }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- retrieve table detail --}}
                                        @foreach ($table_detail as $detail)
                                            @php
                                                $primary = '';
                                                foreach ($table_primary as $p) {
                                                    $primary == ''
                                                        ? ($primary = $detail->{$p->field})
                                                        : ($primary = $primary . ':' . $detail->{$p->field});
                                                }
                                                $vcount = $loop->iteration;
                                            @endphp
                                            <tr
                                                {{ $detail->isactive == '0' ? 'class=not style=background-color:#ffe9ed;' : '' }}>
                                                <td class="text-sm font-weight-normal">
                                                    <div class="btn-group">
                                                        <button class="btn btn-primary btn-sm mb-0 px-3" type="button"
                                                            title="View Data"
                                                            onclick="window.location='{{ url($url_menu . '/show' . '/' . encrypt($primary)) }}'">
                                                            <i class="fas fa-eye"> </i><span class="font-weight-bold"> View
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary mb-0 px-3 dropdown-toggle dropdown-toggle-split"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            {{-- check authorize edit --}}
                                                            @if ($authorize->edit == '1')
                                                                {{-- check status active --}}
                                                                @if ($detail->isactive == 1)
                                                                    {{-- button edit --}}
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-warning mx-2 mb-0 w-90"
                                                                        title="Edit Data"
                                                                        onclick="window.location='{{ url($url_menu . '/edit' . '/' . encrypt($primary)) }}'">
                                                                        <i class="fas fa-edit"></i><span
                                                                            class="font-weight-bold">
                                                                            Edit</span>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                            {{-- check authorize delete and data primary key not "msjit" --}}
                                                            @if ($authorize->delete == '1' && $primary != 'msjit')
                                                                <form
                                                                    action="{{ url($url_menu . '/' . encrypt($primary)) }}"
                                                                    method="POST" style="display: inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    {{-- button delete --}}
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-{{ $detail->isactive == '0' ? 'success' : 'danger' }} mx-2 mb-0 w-90"
                                                                        title="Hapus Data"
                                                                        onclick="return deleteData(event, '{{ $primary }}','{{ $detail->isactive == '0' ? 'Aktifkan' : 'Non Aktifkan' }}')">
                                                                        <i class="fas fa-random"></i><span
                                                                            class="font-weight-bold">
                                                                            {{ $detail->isactive == '0' ? 'Aktifkan' : 'Non Aktifkan' }}</span>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>{{ $table_detail->firstItem() + $loop->index }}</td>
                                                @foreach ($table_header as $field)
                                                    @php
                                                        $string = $field->field;
                                                    @endphp
                                                    {{-- field type enum --}}
                                                    @if ($field->type == 'enum')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            @if ($field->link != '')
                                                                <a target="_blank"
                                                                    href="{{ url($field->link . '/?id=' . encrypt($detail->$string)) }}">
                                                                    @if ($field->query != '')
                                                                        @php
                                                                            $data_query = DB::select($field->query);
                                                                        @endphp
                                                                        @foreach ($data_query as $q)
                                                                            <?php $sAsArray = array_values((array) $q); ?>
                                                                            {{ $detail->$string == $sAsArray[0] ? $sAsArray[1] : '' }}
                                                                        @endforeach
                                                                    @endif
                                                                    &nbsp;&nbsp;
                                                                    <i aria-hidden="true" class="fas fa-external-link-alt">
                                                                    </i>
                                                                </a>
                                                            @else
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
                                                                                        $text =
                                                                                            $text . ',' . $sAsArray[1];
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
                                                                    data-bs-target="#imageModal{{ $field->field }}{{ $vcount }}">
                                                            </span>
                                                            <span
                                                                style="display: none;">{{ asset('/storage' . '/' . $detail->$string) }}
                                                            </span>
                                                            <!-- Modal -->
                                                            <div class="modal fade"
                                                                id="imageModal{{ $field->field }}{{ $vcount }}"
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
                                                                                data-bs-target="#imageModalJoin{{ $field->field }}{{ $vcount }}">
                                                                        </span>
                                                                        <span
                                                                            style="display: none;">{{ asset('/storage' . '/' . $sAsArray[0]) }}
                                                                        </span>
                                                                        <!-- Modal -->
                                                                        <div class="modal fade"
                                                                            id="imageModalJoin{{ $field->field }}{{ $vcount }}"
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
                                                        {{-- field type date --}}
                                                    @elseif ($field->type == 'date')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }} {{ $field->class == 'check-date' ? $detail->$string : '' }}">
                                                            {{ $detail->$string }}
                                                        </td>
                                                        @if ($field->note != '')
                                                            <p id="datenote" style="display: none">
                                                                {{ $field->note }}
                                                            </p>
                                                        @else
                                                            <p id="datenote" style="display: none">Date Expired <=
                                                                    {{ $field->length }} Day </p>
                                                        @endif
                                                        @if ($field->class == 'check-date')
                                                            <script>
                                                                var inputDate = new Date('{{ $detail->$string }}');
                                                                var currentDate = new Date();
                                                                var futureDate = new Date(currentDate.setDate(currentDate.getDate() + parseInt('{{ $field->length }}')));

                                                                if (inputDate <= futureDate) {
                                                                    $('.{{ $detail->$string }}').parents('tr').addClass('exp');
                                                                    $('.{{ $detail->$string }}').parents('tr').css('background-color', '#ffe768');
                                                                }
                                                            </script>
                                                        @endif
                                                    @elseif ($field->type == 'number')
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }} text-number text-end {{ $field->class == 'check-stock' ? $detail->$string . $detail->{$field->sub} : '' }}">
                                                            {{ $detail->$string }}
                                                        </td>
                                                        @if ($field->note != '')
                                                            <p id="stocknote" style="display: none">
                                                                {{ $field->note }}
                                                            </p>
                                                        @else
                                                            <p id="stocknote" style="display: none">Stock < minimal stock
                                                                    </p>
                                                        @endif
                                                        @if ($field->class == 'check-stock')
                                                            <script>
                                                                var vstock = {{ $detail->$string }};
                                                                var vminstock = {{ $detail->{$field->sub} }};

                                                                if (vstock < vminstock) {
                                                                    $('.{{ $detail->$string . $detail->{$field->sub} }}').parents('tr').addClass('stock');
                                                                    $('.{{ $detail->$string . $detail->{$field->sub} }}').parents('tr').css('background-color', '#f93c3c');
                                                                    $('.{{ $detail->$string . $detail->{$field->sub} }}').parents('tr').css('color', '#000');
                                                                }
                                                            </script>
                                                        @endif
                                                    @else
                                                        <td
                                                            class="text-sm font-weight-{{ $field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                            @if ($field->link != '')
                                                                <a target="_blank"
                                                                    href="{{ url($field->link . '/?id=' . encrypt($detail->$string)) }}">
                                                                    {{ $detail->$string }}&nbsp;&nbsp;
                                                                    <i aria-hidden="true"
                                                                        class="fas fa-external-link-alt">
                                                                    </i>
                                                                </a>
                                                            @else
                                                                {{ $detail->$string }}
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Data {{ $table_detail->firstItem() }} - {{ $table_detail->lastItem() }}
                                        dari {{ $table_detail->total() }} data.
                                    </div>
                                    <div>
                                        {{ $table_detail->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row px-4 py-2">
                            <div class="col-lg">
                                <div class="nav-wrapper" id="noted"><code>Note : <i aria-hidden="true"
                                            style="color: #ffc2cd;" class="fas fa-circle"></i> Data not active</code>
                                </div>
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
            //check note
            if ($('*').hasClass('exp')) {
                $('#noted').html(`<code>Note :( <i aria-hidden="true" style="color: #ffc2cd;"
                class="fas fa-circle"></i> Data not active ), ( <i aria-hidden="true"
                style="color: #ffe768;" class="fas fa-circle"></i> ` + $('#datenote').text() + ` )</code>`)
            }
            if ($('*').hasClass('stock')) {
                $('#noted').html(`<code>Note :( <i aria-hidden="true" style="color: #ffc2cd;"
                class="fas fa-circle"></i> Data not active ), ( <i aria-hidden="true"
                style="color: #f93c3c;" class="fas fa-circle"></i> ` + $('#stocknote').text() + ` )</code>`)
            }
            if ($('*').hasClass('exp') && $('*').hasClass('stock')) {
                $('#noted').html(`<code>Note :( <i aria-hidden="true" style="color: #ffc2cd;"
                class="fas fa-circle"></i> Data not active ), ( <i aria-hidden="true"
                style="color: #ffe768;" class="fas fa-circle"></i> ` + $('#datenote').text() + ` ), ( <i aria-hidden="true"
                style="color: #f93c3c;" class="fas fa-circle"></i> ` + $('#stocknote').text() + ` )</code>`)
            }
        });
        //set table into datatables
        $('#list_{{ $dmenu }}').DataTable({
            paging: false,
            info: false,
            searching: true,
            responsive: true,
            language: {
                search: "Cari :",
                lengthMenu: "Tampilkan _MENU_ baris",
                zeroRecords: "Maaf - Data tidak ada",
                info: "Data _START_ - _END_ dari _TOTAL_",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(pencarian dari _MAX_ data)"
            },
            responsive: true,
            // dom: 'Bfrtip',
            dom: '<"row d-flex justify-content-between align-items-center"<"col-lg-12 d-flex justify-content-between align-items-center"Bf>>rtip',
            buttons: [{
                    text: '<i class="fas fa-file-excel me-1 text-lg text-success"></i><span class="font-weight-bold"> Excel</span>',
                    action: function() {
                        // Arahkan ke server untuk ekspor Excel
                        window.location.href = "{{ url($url_menu) }}?export=excel";
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf me-1 text-lg text-danger"></i><span class="font-weight-bold"> PDF</span>',
                    action: function() {
                        // Arahkan ke server untuk ekspor PDF
                        window.location.href = "{{ url($url_menu) }}?export=pdf";
                    }
                },
            ],
            initComplete: function() {
                // Tambahkan custom search bar
                var searchBarHtml = `
                    <div class="dataTables_filter d-flex justify-content-end">
                        <form method="GET" action="{{ url($url_menu) }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search..." value="{{ request('search') }}">
                        </form>
                    </div>
                `;
                $('.dataTables_filter').remove(); // Hapus input pencarian default DataTables
                $(searchBarHtml).insertAfter('.dt-buttons'); // Tambahkan search bar setelah tombol
            }
        });
        //set color button datatables
        $('.dt-button').addClass('btn btn-secondary');
        $('.dt-button').removeClass('dt-button');
        //check authorize button datatables
        <?= $authorize->excel == '0' ? "$('.buttons-excel').remove();" : '' ?>
        <?= $authorize->pdf == '0' ? "$('.buttons-pdf').remove();" : '' ?>
        <?= $authorize->print == '0' ? "$('.buttons-print').remove();" : '' ?>
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
    </script>
@endpush
BLADE;
    }
}
