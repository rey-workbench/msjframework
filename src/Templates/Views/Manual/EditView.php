<?php

namespace MSJFramework\LaravelGenerator\Templates\Views\Manual;

class EditView
{
    public static function getTemplate(string $dmenu): string
    {
        return <<<'BLADE'
@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $title_menu])

    <div class="card shadow-lg mx-4">
        <div class="card-body p-3">
            <div class="row gx-4">
                <div class="col-lg">
                    <div class="nav-wrapper">
                        <button class="btn btn-secondary mb-0" onclick="history.back()">
                            <i class="fas fa-arrow-left me-1"></i>
                            <span class="font-weight-bold">Kembali</span>
                        </button>
                        @if ($authorize->edit == '1')
                            <button class="btn btn-warning mb-0"
                                onclick="event.preventDefault(); document.getElementById('{{ $dmenu }}-form').submit();">
                                <i class="fas fa-save me-1"></i>
                                <span class="font-weight-bold">Update {{ $title_menu }}</span>
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
                <form role="form" method="POST"
                    action="{{ URL::to($url_menu . '/' . encrypt($table_detail->{$table_primary[0]->field})) }}"
                    id="{{ $dmenu }}-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('components.alert')

                    @foreach ($table_primary as $primaryField)
                        <input type="hidden" name="{{ $primaryField->field }}"
                            value="{{ old($primaryField->field, $table_detail->{$primaryField->field} ?? '') }}">
                    @endforeach

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
                                            $fieldValue = old(
                                                $header->field,
                                                $table_detail->{$header->field} ?? $header->default,
                                            );
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

                                            @if ($primary)
                                                <input class="form-control" type="text" value="{{ $fieldValue }}"
                                                    readonly style="background-color: #f8f9fa; font-weight: bold;">
                                                <input type="hidden" name="{{ $header->field }}"
                                                    value="{{ $fieldValue }}">
                                            @elseif ($header->type == 'char' || $header->type == 'string')
                                                <input class="form-control {{ $header->class }}" type="text"
                                                    value="{{ $fieldValue }}" name="{{ $header->field }}"
                                                    maxlength="{{ $header->length }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'enum')
                                                @if ($header->query != '')
                                                    @php
                                                        $displayText = '';
                                                        $data_query = DB::select($header->query);
                                                        foreach ($data_query as $q) {
                                                            $sAsArray = array_values((array) $q);
                                                            if ($sAsArray[0] == $fieldValue) {
                                                                $displayText = $sAsArray[1];
                                                                break;
                                                            }
                                                        }
                                                    @endphp
                                                @endif
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
                                                                {{ old($header->field, $fieldValue) == $sAsArray[0] ? 'selected' : '' }}>
                                                                {{ $sAsArray[1] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            @elseif ($header->type == 'search')
                                                @php
                                                    $displayText = '';
                                                    if ($header->query != '') {
                                                        $data_query = DB::select($header->query);
                                                        foreach ($data_query as $q) {
                                                            $sAsArray = array_values((array) $q);
                                                            if ($sAsArray[0] == $fieldValue) {
                                                                $displayText = $sAsArray[1];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <div class="input-group">
                                                    <input type="text" name="{{ $header->field }}"
                                                        class="form-control" value="{{ $displayText }}"
                                                        {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}
                                                        readonly>
                                                    <span class="input-group-text bg-primary text-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#searchModal{{ $header->field }}"
                                                        style="border-color:#d2d6da;border-left:3px solid #d2d6da;cursor: pointer;">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                </div>
                                                <input type="hidden" name="{{ $header->field }}"
                                                    value="{{ $fieldValue }}">

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
                                                                    id="list_{{ $dmenu }}_{{ $header->field }}_edit">
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
                                                                                    <td><button type="button"
                                                                                            class="btn btn-sm btn-primary"
                                                                                            onclick="$('input[name=\'{{ $header->field }}\']').val('{{ $modal_d->{$field[0]} }}'); $('input[name=\'{{ $header->field }}\']').prev().val('{{ $modal_d->{$field[1]} }}'); $('#searchModal{{ $header->field }}').modal('hide');">
                                                                                            <i
                                                                                                class="fas fa-check"></i>
                                                                                            Pilih
                                                                                        </button></td>
                                                                                    @foreach ((array) $modal_d as $dat)
                                                                                        <td>{{ $dat }}
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
                                                    $(document).ready(function() {
                                                        $('#list_{{ $dmenu }}_{{ $header->field }}_edit').DataTable({
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
                                            @elseif ($header->type == 'date')
                                                <input class="form-control {{ $header->class }}" type="date"
                                                    value="{{ $fieldValue ? date('Y-m-d', strtotime($fieldValue)) : '' }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'datetime')
                                                <input class="form-control {{ $header->class }}"
                                                    type="datetime-local"
                                                    value="{{ $fieldValue ? date('Y-m-d\TH:i', strtotime($fieldValue)) : '' }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'number')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    value="{{ old($header->field, $fieldValue) }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'currency')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    step="0.01" value="{{ old($header->field, $fieldValue) }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'text')
                                                <textarea class="form-control {{ $header->class }}" name="{{ $header->field }}" maxlength="{{ $header->length }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>{{ $fieldValue }}</textarea>
                                            @elseif ($header->type == 'image')
                                                @if ($fieldValue && $fieldValue != $header->default)
                                                    <div class="mt-2">
                                                        <small class="text-muted">File saat ini:
                                                            {{ basename($fieldValue) }}</small>
                                                    </div>
                                                @else
                                                    <small class="text-muted">Tidak ada file</small>
                                                @endif
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}" accept=".jpg,.jpeg,.png"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'file')
                                                @if ($fieldValue && $fieldValue != $header->default)
                                                    <div class="mt-2">
                                                        <small class="text-muted">File saat ini:
                                                            {{ basename($fieldValue) }}</small>
                                                    </div>
                                                @else
                                                    <small class="text-muted">Tidak ada file</small>
                                                @endif
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'hidden')
                                                <input type="hidden" name="{{ $header->field }}"
                                                    value="{{ $fieldValue }}">
                                            @else
                                                <input class="form-control {{ $header->class }}" type="text"
                                                    value="{{ $fieldValue }}" name="{{ $header->field }}"
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
                                            $fieldValue = old(
                                                $header->field,
                                                $table_detail->{$header->field} ?? $header->default,
                                            );
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

                                            @if ($header->type == 'enum')
                                                @php
                                                    $displayText = '';
                                                    if ($header->query != '') {
                                                        $data_query = DB::select($header->query);
                                                        foreach ($data_query as $q) {
                                                            $sAsArray = array_values((array) $q);
                                                            if ($sAsArray[0] == $fieldValue) {
                                                                $displayText = $sAsArray[1];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                @endphp
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
                                                                {{ old($header->field, $fieldValue) == $sAsArray[0] ? 'selected' : '' }}>
                                                                {{ $sAsArray[1] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            @elseif ($header->type == 'currency')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    step="0.01" value="{{ old($header->field, $fieldValue) }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'text')
                                                <textarea class="form-control {{ $header->class }}" name="{{ $header->field }}"
                                                    maxlength="{{ $header->length }}" {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>{{ $fieldValue }}</textarea>
                                            @elseif ($header->type == 'image')
                                                @if ($fieldValue && $fieldValue != $header->default)
                                                    <div class="mt-2">
                                                        <small class="text-muted">File saat ini:
                                                            {{ basename($fieldValue) }}</small>
                                                    </div>
                                                @else
                                                    <small class="text-muted">Tidak ada file</small>
                                                @endif
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}" accept=".jpg,.jpeg,.png"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'file')
                                                @if ($fieldValue && $fieldValue != $header->default)
                                                    <div class="mt-2">
                                                        <small class="text-muted">File saat ini:
                                                            {{ basename($fieldValue) }}</small>
                                                    </div>
                                                @else
                                                    <small class="text-muted">Tidak ada file</small>
                                                @endif
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                                @if ($header->validate != '')
                                                    <small
                                                        class="text-muted">{{ str_replace('|', ', ', str_replace(['mimes:', 'max:'], ['Format: ', 'Max: '], $header->validate)) }}</small>
                                                @endif
                                            @elseif ($header->type == 'hidden')
                                                <input type="hidden" name="{{ $header->field }}"
                                                    value="{{ $fieldValue }}">
                                            @elseif ($header->type == 'date')
                                                <input class="form-control {{ $header->class }}" type="date"
                                                    value="{{ $fieldValue ? date('Y-m-d', strtotime($fieldValue)) : '' }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'datetime')
                                                <input class="form-control {{ $header->class }}"
                                                    type="datetime-local"
                                                    value="{{ $fieldValue ? date('Y-m-d\TH:i', strtotime($fieldValue)) : '' }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @elseif ($header->type == 'number')
                                                <input class="form-control {{ $header->class }}" type="number"
                                                    value="{{ old($header->field, $fieldValue) }}"
                                                    name="{{ $header->field }}"
                                                    {{ ($header->validate && str_contains($header->validate, 'required')) ? 'required' : '' }}>
                                            @else
                                                <input class="form-control {{ $header->class }}" type="text"
                                                    value="{{ $fieldValue }}" name="{{ $header->field }}"
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
