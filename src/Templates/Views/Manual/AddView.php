<?php

namespace MSJFramework\LaravelGenerator\Templates\Views\Manual;

class AddView
{
    public static function getTemplate(string $dmenu): string
    {
        return <<<'BLADE'
@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])

    <div class="card shadow-lg mx-4">
        <div class="card-body p-3">
            <div class="row gx-4">
                <div class="col-lg">
                    <div class="nav-wrapper">
                        <button class="btn btn-secondary mb-0" onclick="history.back()">
                            <i class="fas fa-arrow-left me-1"></i>
                            <span class="font-weight-bold">Kembali</span>
                        </button>
                        @if ($authorize->add == '1')
                            <button class="btn btn-primary mb-0"
                                onclick="event.preventDefault(); document.getElementById('{{ $dmenu }}-form').submit();">
                                <i class="fas fa-save me-1"></i>
                                <span class="font-weight-bold">Simpan Data {{ $title_menu }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <form role="form" method="POST" action="{{ URL::to($url_menu) }}" id="{{ $dmenu }}-form"
                    enctype="multipart/form-data">
                    @csrf

                    @include('components.alert')

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header pb-0">
                                    <h6 class="mb-0">Data Utama</h6>
                                    <p class="text-sm mb-0 text-muted">Informasi dasar</p>
                                </div>
                                <div class="card-body">
                                    @foreach ($table_header_l as $header)
                                        @php
                                            $primary = false;
                                            $generateid = false;
                                            foreach ($table_primary as $p) {
                                                $primary == false
                                                    ? ($p->field == $header->field
                                                        ? ($primary = true)
                                                        : ($primary = false))
                                                    : '';
                                                $generateid == false
                                                    ? ($p->generateid != ''
                                                        ? ($generateid = true)
                                                        : ($generateid = false))
                                                    : '';
                                            }
                                        @endphp
                                        <div class="form-group">
                                            @if ($header->type != 'hidden' && $header->field != 'isactive')
                                                <label class="form-control-label"
                                                    {{ $primary ? ($generateid ? ' style="display:none;"' : '') : '' }}>
                                                    {{ $header->alias }}
                                                    @if ($header->validate && str_contains($header->validate, 'required'))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                            @endif

                                            @if ($primary && $generateid)
                                                <input class="form-control" type="text" value="Auto Generate" readonly
                                                    style="background-color: #f8f9fa; font-style: italic;">
                                                <input type="hidden" name="{{ $header->field }}" key="true">
                                            @elseif ($header->type == 'char' || $header->type == 'string')
                                                <input class="form-control {{ $header->class }}" type="text"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}" maxlength="{{ $header->length }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'search')
                                                <div class="flex flex-col mb-2 input-group">
                                                    <input type="text" name="{{ $header->field }}"
                                                        class="form-control {{ $header->class }}"
                                                        value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                        {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}
                                                        readonly>
                                                    <span class="input-group-text bg-primary text-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#searchModal{{ $header->field }}"
                                                        style="border-color:#d2d6da;border-left:3px solid #d2d6da;cursor: pointer;">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                </div>

                                                <div class="modal fade" id="searchModal{{ $header->field }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="searchModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="searchModalLabel">
                                                                    List Data {{ $header->alias }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            @if ($header->query != '')
                                                                @php
                                                                    $table_result = DB::select($header->query);
                                                                @endphp
                                                            @endif
                                                            <div class="modal-body">
                                                                <table class="table display"
                                                                    id="list_{{ $dmenu }}_{{ $header->field }}">
                                                                    @if ($table_result)
                                                                        <thead class="thead-light"
                                                                            style="background-color: #00b7bd4f;">
                                                                            <tr>
                                                                                <th width="20px">Action</th>
                                                                                @foreach ($table_result as $result)
                                                                                    @php
                                                                                        $sAsArray = array_keys(
                                                                                            (array) $result,
                                                                                        );
                                                                                    @endphp
                                                                                @endforeach
                                                                                @foreach ($sAsArray as $modal_h)
                                                                                    <th>{{ $modal_h }}</th>
                                                                                @endforeach
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($table_result as $modal_d)
                                                                                <tr>
                                                                                    @foreach ($table_result as $result)
                                                                                        @php
                                                                                            $field = array_keys(
                                                                                                (array) $result,
                                                                                            );
                                                                                        @endphp
                                                                                    @endforeach
                                                                                    <td width="20px">
                                                                                        <span
                                                                                            class="btn badge bg-primary badge-lg"
                                                                                            onclick="select_modal_{{ $header->field }}('{{ $modal_d->{$field[0]} }}')">
                                                                                            <i
                                                                                                class="bi bi-check-circle me-1"></i>
                                                                                            Select
                                                                                        </span>
                                                                                    </td>
                                                                                    @foreach ($field as $header_field)
                                                                                        @php
                                                                                            $string = $header_field;
                                                                                        @endphp
                                                                                        <td
                                                                                            class="text-sm font-weight-normal">
                                                                                            {{ $modal_d->$string }}
                                                                                        </td>
                                                                                    @endforeach
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    @endif
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    function select_modal_{{ $header->field }}(id) {
                                                        $('input[name="{{ $header->field }}"]').val(id);
                                                        $('#searchModal{{ $header->field }}').modal('hide');
                                                    }

                                                    $(document).ready(function() {
                                                        $('#list_{{ $dmenu }}_{{ $header->field }}').DataTable({
                                                            "pageLength": 10,
                                                            "language": {
                                                                "search": "Cari:",
                                                                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                                                                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                                                "infoEmpty": "Tidak ada data",
                                                                "infoFiltered": "(difilter dari _MAX_ total data)",
                                                                "paginate": {
                                                                    "first": "Pertama",
                                                                    "last": "Terakhir",
                                                                    "next": "Selanjutnya",
                                                                    "previous": "Sebelumnya"
                                                                },
                                                                "zeroRecords": "Data tidak ditemukan"
                                                            }
                                                        });
                                                    });
                                                </script>
                                            @elseif ($header->type == 'enum')
                                                @if ($header->field == 'status')
                                                    <input type="hidden" name="{{ $header->field }}" value="0">
                                                @else
                                                    <select class="form-control {{ $header->class }}"
                                                        name="{{ $header->field }}"
                                                        {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                        <option value="">-- Pilih {{ $header->alias }} --</option>
                                                        @if ($header->query != '')
                                                            @php
                                                                $data_query = DB::select($header->query);
                                                            @endphp
                                                            @foreach ($data_query as $q)
                                                                <?php $sAsArray = array_values((array) $q); ?>
                                                                <option value="{{ $sAsArray[0] }}"
                                                                    {{ old($header->field) == $sAsArray[0] ? 'selected' : '' }}>
                                                                    {{ $sAsArray[1] }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                @endif
                                            @elseif ($header->type == 'date')
                                                <input class="form-control {{ $header->class }}" type="date"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'datetime')
                                                <input class="form-control {{ $header->class }}"
                                                    type="datetime-local"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'number')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'currency')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    step="0.01"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'text')
                                                <textarea class="form-control {{ $header->class }}" name="{{ $header->field }}" maxlength="{{ $header->length }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>{{ old($header->field) ? old($header->field) : $header->default }}</textarea>
                                            @elseif ($header->type == 'image')
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}" accept=".jpg,.jpeg,.png"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'file')
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'hidden')
                                                <input type="hidden" name="{{ $header->field }}"
                                                    value="{{ $header->default }}">
                                            @else
                                                <input class="form-control {{ $header->class }}" type="text"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    maxlength="{{ $header->length }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @endif

                                            @error($header->field)
                                                <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                            @enderror

                                            @if ($header->note != '')
                                                <p class='text-secondary text-xs pt-1 px-1'>
                                                    {{ '*) ' . $header->note }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header pb-0">
                                    <h6 class="mb-0">Data Tambahan</h6>
                                    <p class="text-sm mb-0 text-muted">Informasi tambahan</p>
                                </div>
                                <div class="card-body">
                                    @foreach ($table_header_r as $header)
                                        @php
                                            $primary = false;
                                            $generateid = false;
                                            foreach ($table_primary as $p) {
                                                $primary == false
                                                    ? ($p->field == $header->field
                                                        ? ($primary = true)
                                                        : ($primary = false))
                                                    : '';
                                                $generateid == false
                                                    ? ($p->generateid != ''
                                                        ? ($generateid = true)
                                                        : ($generateid = false))
                                                    : '';
                                            }
                                        @endphp
                                        <div class="form-group">
                                            @if ($header->type != 'hidden' && $header->field != 'isactive')
                                                <label class="form-control-label">
                                                    {{ $header->alias }}
                                                    @if ($header->validate && str_contains($header->validate, 'required'))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                            @endif

                                            @if ($header->type == 'search')
                                                <div class="flex flex-col mb-2 input-group">
                                                    <input type="text" name="{{ $header->field }}"
                                                        class="form-control {{ $header->class }}"
                                                        value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                        {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}
                                                        readonly>
                                                    <span class="input-group-text bg-primary text-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#searchModal{{ $header->field }}"
                                                        style="border-color:#d2d6da;border-left:3px solid #d2d6da;cursor: pointer;">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                </div>

                                                <div class="modal fade" id="searchModal{{ $header->field }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="searchModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="searchModalLabel">
                                                                    List Data {{ $header->alias }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            @if ($header->query != '')
                                                                @php
                                                                    $table_result_r = DB::select($header->query);
                                                                @endphp
                                                            @endif
                                                            <div class="modal-body">
                                                                <table class="table display"
                                                                    id="list_{{ $dmenu }}_{{ $header->field }}_r">
                                                                    @if ($table_result_r)
                                                                        <thead class="thead-light"
                                                                            style="background-color: #00b7bd4f;">
                                                                            <tr>
                                                                                <th width="20px">Action</th>
                                                                                @foreach ($table_result_r as $result)
                                                                                    @php
                                                                                        $sAsArray = array_keys(
                                                                                            (array) $result,
                                                                                        );
                                                                                    @endphp
                                                                                @endforeach
                                                                                @foreach ($sAsArray as $modal_h)
                                                                                    <th>{{ $modal_h }}</th>
                                                                                @endforeach
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($table_result_r as $modal_d)
                                                                                <tr>
                                                                                    @foreach ($table_result_r as $result)
                                                                                        @php
                                                                                            $field = array_keys(
                                                                                                (array) $result,
                                                                                            );
                                                                                        @endphp
                                                                                    @endforeach
                                                                                    <td width="20px">
                                                                                        <span
                                                                                            class="btn badge bg-primary badge-lg"
                                                                                            onclick="select_modal_{{ $header->field }}('{{ $modal_d->{$field[0]} }}')">
                                                                                            <i
                                                                                                class="bi bi-check-circle me-1"></i>
                                                                                            Select
                                                                                        </span>
                                                                                    </td>
                                                                                    @foreach ($field as $header_field)
                                                                                        @php
                                                                                            $string = $header_field;
                                                                                        @endphp
                                                                                        <td
                                                                                            class="text-sm font-weight-normal">
                                                                                            {{ $modal_d->$string }}
                                                                                        </td>
                                                                                    @endforeach
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    @endif
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    function select_modal_{{ $header->field }}(id) {
                                                        $('input[name="{{ $header->field }}"]').val(id);
                                                        $('#searchModal{{ $header->field }}').modal('hide');
                                                    }

                                                    $(document).ready(function() {
                                                        $('#list_{{ $dmenu }}_{{ $header->field }}_r').DataTable({
                                                            "pageLength": 10,
                                                            "language": {
                                                                "search": "Cari:",
                                                                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                                                                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                                                "infoEmpty": "Tidak ada data",
                                                                "infoFiltered": "(difilter dari _MAX_ total data)",
                                                                "paginate": {
                                                                    "first": "Pertama",
                                                                    "last": "Terakhir",
                                                                    "next": "Selanjutnya",
                                                                    "previous": "Sebelumnya"
                                                                },
                                                                "zeroRecords": "Data tidak ditemukan"
                                                            }
                                                        });

                                                        $('#searchModal{{ $header->field }}').on('hidden.bs.modal', function() {
                                                            $(this).find('*').blur();
                                                        });
                                                    });
                                                </script>
                                            @elseif ($header->type == 'enum')
                                                @if ($header->field == 'status')
                                                    <input type="hidden" name="{{ $header->field }}" value="0">
                                                @else
                                                    <select class="form-control {{ $header->class }}"
                                                        name="{{ $header->field }}"
                                                        {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                        <option value="">-- Pilih {{ $header->alias }} --</option>
                                                        @if ($header->query != '')
                                                            @php
                                                                $data_query = DB::select($header->query);
                                                            @endphp
                                                            @foreach ($data_query as $q)
                                                                <?php $sAsArray = array_values((array) $q); ?>
                                                                <option value="{{ $sAsArray[0] }}"
                                                                    {{ old($header->field) == $sAsArray[0] ? 'selected' : '' }}>
                                                                    {{ $sAsArray[1] }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                @endif
                                            @elseif ($header->type == 'currency')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    step="0.01"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'text')
                                                <textarea class="form-control {{ $header->class }}" name="{{ $header->field }}"
                                                    maxlength="{{ $header->length }}" {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>{{ old($header->field) ? old($header->field) : $header->default }}</textarea>
                                            @elseif ($header->type == 'image')
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}" accept=".jpg,.jpeg,.png"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'file')
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'hidden')
                                                <input type="hidden" name="{{ $header->field }}"
                                                    value="{{ $header->default }}">
                                            @elseif ($header->type == 'date')
                                                <input class="form-control {{ $header->class }}" type="date"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'datetime')
                                                <input class="form-control {{ $header->class }}"
                                                    type="datetime-local"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'number')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @else
                                                <input class="form-control {{ $header->class }}" type="text"
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}"
                                                    maxlength="{{ $header->length }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @endif

                                            @error($header->field)
                                                <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                            @enderror

                                            @if ($header->note != '')
                                                <p class='text-secondary text-xs pt-1 px-1'>
                                                    {{ '*) ' . $header->note }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="isactive" value="1">
                </form>
            </div>
        </div>
    </div>
@endsection

@if ($jsmenu == '1')
    @if (view()->exists("js.{{ $dmenu }}"))
        @push('addjs')
            @include('js.' . $dmenu)
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
BLADE;
    }
}
