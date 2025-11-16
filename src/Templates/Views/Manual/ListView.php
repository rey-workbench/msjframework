<?php

namespace MSJFramework\LaravelGenerator\Templates\Views\Manual;

class ListView
{
    public static function getTemplate(string $dmenu): string
    {
        return <<<BLADE
@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="row mx-1">
                    <div class="card">
                        <div class="row">
                            <div class="card-header col-md-auto">
                                <h5 class="mb-0">{{ \$title_menu }}</h5>
                            </div>
                            <div class="col">
                            </div>
                        </div>
                        <hr class="horizontal dark mt-0">
                        <div class="row px-4 py-2">
                            <div class="col-lg-6">
                                <div class="nav-wrapper">
                                    @if (\$authorize->add == '1')
                                        <a href="{{ url(\$url_menu . '/add') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i>&nbsp;&nbsp;Tambah
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="list_{$dmenu}">
                                    <thead class="thead-light" style="background-color:#00b7bd4f;">
                                        <tr>
                                            <th>Action</th>
                                            <th>No</th>
                                            @foreach (\$table_header as \$header)
                                                @if (\$header->list == '1')
                                                    <th>{{ \$header->alias }}</th>
                                                @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (\$table_detail as \$detail)
                                            @php
                                                \$vcount = \$loop->iteration;
                                                \$primary = '';
                                                foreach (\$table_primary as \$p) {
                                                    \$primary == '' ? (\$primary = \$detail->{\$p->field}) : (\$primary = \$primary . ':' . \$detail->{\$p->field});
                                                }
                                            @endphp
                                            <tr class="{{ \$detail->isactive == '0' ? 'table-danger' : '' }}">
                                                <td class="text-sm font-weight-normal">
                                                    <div class="btn-group">
                                                        <button class="btn btn-primary btn-sm mb-0 px-3" type="button"
                                                            title="View Data"
                                                            onclick="window.location='{{ url(\$url_menu . '/show' . '/' . encrypt(\$primary)) }}'">
                                                            <i class="fas fa-eye"> </i><span class="font-weight-bold">
                                                                View
                                                        </button>

                                                        @if (\$authorize->edit == '1' || \$authorize->delete == '1')
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary mb-0 px-3 dropdown-toggle dropdown-toggle-split"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <span class="visually-hidden">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @if (\$authorize->edit == '1')
                                                                    @if (\$detail->isactive == 1)
                                                                        <li>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-warning mx-2 mb-0 w-90"
                                                                                title="Edit Data"
                                                                                onclick="window.location='{{ url(\$url_menu . '/edit' . '/' . encrypt(\$primary)) }}'">
                                                                                <i class="fas fa-edit"></i><span
                                                                                    class="font-weight-bold"> Edit</span>
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <hr class="dropdown-divider">
                                                                        </li>
                                                                    @endif
                                                                @endif

                                                                @if (\$authorize->delete == '1')
                                                                    <form
                                                                        action="{{ url(\$url_menu . '/' . encrypt(\$primary)) }}"
                                                                        method="POST" style="display: inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <li>
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-{{ \$detail->isactive == '0' ? 'success' : 'danger' }} mx-2 mb-0 w-90"
                                                                                title="Toggle Status"
                                                                                onclick="return deleteData(event, '{{ \$detail->{\$table_primary[0]->field} }}','{{ \$detail->isactive == '0' ? 'Aktifkan' : 'Non Aktifkan' }}')">
                                                                                <i
                                                                                    class="fas fa-{{ \$detail->isactive == '0' ? 'user-check' : 'user-slash' }} me-2"></i>
                                                                                <span class="font-weight-bold">
                                                                                    {{ \$detail->isactive == '0' ? 'Aktifkan' : 'Non Aktifkan' }}
                                                                                </span>
                                                                            </button>
                                                                        </li>
                                                                    </form>
                                                                @endif
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>{{ \$vcount }}</td>
                                                @foreach (\$table_header as \$field)
                                                    @if (\$field->list == '1')
                                                        @php
                                                            \$string = \$field->field;
                                                        @endphp
                                                        @if (\$field->field == 'isactive')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                                <span
                                                                    class="badge badge-sm bg-gradient-{{ \$detail->isactive == '1' ? 'success' : 'danger' }}">
                                                                    {{ \$detail->isactive == '1' ? 'Aktif' : 'Tidak Aktif' }}
                                                                </span>
                                                            </td>
                                                        @elseif (\$field->type == 'currency')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }} text-currency text-end">
                                                                {{ \$format->CurrencyFormat(\$detail->\$string, \$field->decimals ?? 0, \$field->sub ?: 'Rp.') }}
                                                            </td>
                                                        @elseif (\$field->type == 'date' || \$field->type == 'datetime')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }} {{ \$field->class == 'check-date' ? \$detail->\$string : '' }}">
                                                                {{ \$format->DateFormat(\$detail->\$string) }}
                                                            </td>
                                                            @if (\$field->note != '')
                                                                <p id="datenote" style="display: none">
                                                                    {{ \$field->note }}
                                                                </p>
                                                            @else
                                                                <p id="datenote" style="display: none">Date Expired <=
                                                                        {{ \$field->length }} Day</p>
                                                            @endif
                                                            @if (\$field->class == 'check-date')
                                                                <script>
                                                                    var inputDate = new Date('{{ \$detail->\$string }}');
                                                                    var currentDate = new Date();
                                                                    var futureDate = new Date(currentDate.setDate(currentDate.getDate() + parseInt('{{ \$field->length }}')));

                                                                    if (inputDate <= futureDate) {
                                                                        \$('.{{ \$detail->\$string }}').parents('tr').addClass('exp');
                                                                        \$('.{{ \$detail->\$string }}').parents('tr').css('background-color', '#ffe768');
                                                                    }
                                                                </script>
                                                            @endif
                                                        @elseif (\$field->type == 'enum')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                                @if (\$field->query != '')
                                                                    @php
                                                                        \$data_query = DB::select(\$field->query);
                                                                    @endphp
                                                                    @foreach (\$data_query as \$q)
                                                                        <?php \$sAsArray = array_values((array) \$q); ?>
                                                                        {{ \$detail->\$string == \$sAsArray[0] ? \$sAsArray[1] : '' }}
                                                                    @endforeach
                                                                @else
                                                                    {{ \$detail->\$string ?? '-' }}
                                                                @endif
                                                            </td>
                                                        @elseif(\$field->type == 'image')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                                @if (\$detail->\$string && \$detail->\$string !== 'noimage.png')
                                                                    <span class="my-2 text-xs">
                                                                        <img src="{{ file_exists(public_path('storage/' . \$detail->\$string . 'tumb.png')) ? asset('/storage' . '/' . \$detail->\$string . 'tumb.png') : asset('storage/' . \$detail->\$string) }}"
                                                                            alt="image" style="height: 35px;"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#imageModal{{ \$field->field }}{{ \$vcount }}">
                                                                    </span>

                                                                    <div class="modal fade"
                                                                        id="imageModal{{ \$field->field }}{{ \$vcount }}"
                                                                        tabindex="-1" role="dialog"
                                                                        aria-labelledby="imageModalLabel"
                                                                        aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered"
                                                                            role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="imageModalLabel">
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
                                                                                    <img src="{{ asset('/storage' . '/' . \$detail->\$string) }}"
                                                                                        id="preview" alt="image"
                                                                                        class="w-100 border-radius-lg shadow-sm">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        @elseif (\$field->type == 'file')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                                @if (\$detail->\$string)
                                                                    <a target="_blank"
                                                                        class="btn btn-sm btn-outline-success mb-0 py-1 px-2"
                                                                        href="{{ asset('/storage' . '/' . \$detail->\$string) }}">
                                                                        <i aria-hidden="true"
                                                                            class="fas fa-file-lines text-lg">
                                                                        </i>
                                                                        {{ \$field->alias }}</a>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        @elseif(\$field->type == 'join')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                                @if (\$field->query != '')
                                                                    @php
                                                                        \$query =
                                                                            \$field->query .
                                                                            "'" .
                                                                            \$detail->\$string .
                                                                            "'";
                                                                        \$data_query = DB::select(\$query);
                                                                    @endphp
                                                                    @foreach (\$data_query as \$q)
                                                                        <?php \$sAsArray = array_values((array) \$q); ?>
                                                                        {{ \$sAsArray[0] != '' ? \$sAsArray[0] : '-' }}
                                                                    @endforeach
                                                                @else
                                                                    {{ \$detail->\$string ?? '-' }}
                                                                @endif
                                                            </td>
                                                        @elseif (\$field->type == 'number')
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }} text-number text-end {{ \$field->class == 'check-stock' ? \$detail->\$string . \$detail->{\$field->sub} : '' }}">
                                                                {{ \$detail->\$string ?? '-' }}
                                                            </td>
                                                            @if (\$field->note != '')
                                                                <p id="stocknote" style="display: none">
                                                                    {{ \$field->note }}
                                                                </p>
                                                            @else
                                                                <p id="stocknote" style="display: none">Stock < minimal
                                                                        stock</p>
                                                            @endif
                                                            @if (\$field->class == 'check-stock')
                                                                <script>
                                                                    var vstock = {{ \$detail->\$string }};
                                                                    var vminstock = {{ \$detail->{\$field->sub} }};

                                                                    if (vstock < vminstock) {
                                                                        \$('.{{ \$detail->\$string . \$detail->{\$field->sub} }}').parents('tr').addClass('stock');
                                                                        \$('.{{ \$detail->\$string . \$detail->{\$field->sub} }}').parents('tr').css('background-color', '#f93c3c');
                                                                        \$('.{{ \$detail->\$string . \$detail->{\$field->sub} }}').parents('tr').css('color', '#000');
                                                                    }
                                                                </script>
                                                            @endif
                                                        @else
                                                            <td
                                                                class="text-sm font-weight-{{ \$field->primary == '1' ? 'bold text-dark' : 'normal' }}">
                                                                @if (\$field->link != '')
                                                                    <a target="_blank"
                                                                        href="{{ url(\$field->link . '/?id=' . encrypt(\$detail->\$string)) }}">
                                                                        {{ \$detail->\$string ?? '-' }}&nbsp;&nbsp;
                                                                        <i aria-hidden="true"
                                                                            class="fas fa-external-link-alt"></i>
                                                                    </a>
                                                                @else
                                                                    {{ \$detail->\$string ?? '-' }}
                                                                @endif
                                                            </td>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if (method_exists(\$table_detail, 'firstItem'))
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Data {{ \$table_detail->firstItem() }} - {{ \$table_detail->lastItem() }}
                                        dari {{ \$table_detail->total() }} data.
                                    </div>
                                    <div>
                                        {{ \$table_detail->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @else
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Total {{ \$table_detail->count() }} data.
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row px-4 py-2">
                            <div class="col-lg">
                                <div class="nav-wrapper" id="noted">
                                    <code>
                                        Note :
                                        <i aria-hidden="true" style="color: #ffc2cd;" class="fas fa-circle"></i> Data
                                        Atur Sendiri &nbsp;&nbsp;
                                        <i aria-hidden="true" style="color: #a5d6a7;" class="fas fa-circle"></i> Data
                                        Atur Sendiri &nbsp;&nbsp;
                                        <i aria-hidden="true" style="color: #ffffff;" class="fas fa-circle"></i> Data
                                        Atur Sendiri
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @if (\$jsmenu == '1')
        @if (view()->exists("js.{\$dmenu}"))
            @push('addjs')
                @include('js.' . \$dmenu)
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

    @push('js')
        <script>
            let columnAbjad = '';
            \$(document).ready(function() {
                let numColumns = \$('#list_{$dmenu}').DataTable().columns().count();
                let columnNames = '';
                for (let index = 0; index < numColumns; index++) {
                    columnNames = \$('#list_{$dmenu}').DataTable().columns(index).header()[0].textContent;
                    if (columnNames == 'Status' || columnNames == 'status') {
                        columnAbjad = String.fromCharCode(65 + index);
                    }
                }

                if (\$('*').hasClass('exp')) {
                    \$('#noted').html(`<code>Note :( <i aria-hidden="true" style="color: #ffc2cd;"
                class="fas fa-circle"></i> Data not active ), ( <i aria-hidden="true"
                style="color: #ffe768;" class="fas fa-circle"></i> ` + \$('#datenote').text() + ` )</code>`)
                }
                if (\$('*').hasClass('stock')) {
                    \$('#noted').html(`<code>Note :( <i aria-hidden="true" style="color: #ffc2cd;"
                class="fas fa-circle"></i> Data not active ), ( <i aria-hidden="true"
                style="color: #f93c3c;" class="fas fa-circle"></i> ` + \$('#stocknote').text() + ` )</code>`)
                }
                if (\$('*').hasClass('exp') && \$('*').hasClass('stock')) {
                    \$('#noted').html(`<code>Note :( <i aria-hidden="true" style="color: #ffc2cd;"
                class="fas fa-circle"></i> Data not active ), ( <i aria-hidden="true"
                style="color: #ffe768;" class="fas fa-circle"></i> ` + \$('#datenote').text() + ` ), ( <i aria-hidden="true"
                style="color: #f93c3c;" class="fas fa-circle"></i> ` + \$('#stocknote').text() + ` )</code>`)
                }
            });

            \$('#list_{$dmenu}').DataTable({
                "language": {
                    "search": "Cari :",
                    "lengthMenu": "Tampilkan _MENU_ baris",
                    "zeroRecords": "Maaf - Data tidak ada",
                    "info": "Data _START_ - _END_ dari _TOTAL_",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(pencarian dari _MAX_ data)"
                },
                paging: false,
                info: false,
                searching: true,
                responsive: true,
                order: [],
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    width: '100px'
                }],
                dom: '<"row d-flex justify-content-between align-items-center"<"col-lg-12 d-flex justify-content-between align-items-center"Bf>>rtip',
                buttons: @include('components.export.datatable-buttons')
            });

            \$('.dt-button').addClass('btn btn-secondary');
            \$('.dt-button').removeClass('dt-button');
        </script>
    @endpush
BLADE;
    }
}
