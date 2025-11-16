<?php

namespace MSJFramework\LaravelGenerator\Services\Templates\Views\Manual;

class ShowView
{
    public static function getTemplate(): string
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
                        @if ($authorize->edit == '1' && ($table_detail->isactive ?? '1') == '1')
                            <a class="btn btn-warning mb-0"
                                href="{{ URL::to($url_menu . '/edit/' . encrypt($table_detail->{$table_primary[0]->field})) }}">
                                <i class="fas fa-edit me-1"></i>
                                <span class="font-weight-bold">Edit</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                @include('components.alert')

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div>
                                    <h6 class="mb-0">Detail {{ $title_menu }}</h6>
                                    <p class="text-sm mb-0 text-muted">Informasi lengkap</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        @foreach ($table_header_l as $header)
                                            @php
                                                $fieldValue = $table_detail->{$header->field} ?? '';
                                                $primary = false;
                                                foreach ($table_primary as $p) {
                                                    if ($p->field == $header->field) {
                                                        $primary = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if ($header->show == '1' && $header->type != 'hidden' && $fieldValue !== '' && $fieldValue !== null)
                                                <div class="form-group">
                                                    <label class="form-control-label">{{ $header->alias }}</label>
                                                    @php
                                                        $displayValue = '';
                                                        if ($header->type == 'currency') {
                                                            $displayValue = $format->CurrencyFormat(
                                                                $fieldValue,
                                                                $header->decimals ?? 0,
                                                            );
                                                        } elseif (
                                                            $header->type == 'date' ||
                                                            $header->type == 'datetime'
                                                        ) {
                                                            $displayValue = $format->DateFormat($fieldValue);
                                                        } elseif ($header->type == 'enum') {
                                                            if ($header->query != '') {
                                                                $data_query = DB::select($header->query);
                                                                $displayValue = $fieldValue;
                                                                foreach ($data_query as $q) {
                                                                    $sAsArray = array_values((array) $q);
                                                                    if ($fieldValue == $sAsArray[0]) {
                                                                        $displayValue = $sAsArray[1];
                                                                        break;
                                                                    }
                                                                }
                                                            } else {
                                                                $displayValue = ucfirst($fieldValue);
                                                            }
                                                        } elseif ($header->type == 'text') {
                                                            $displayValue = $fieldValue;
                                                        } else {
                                                            $displayValue = $fieldValue;
                                                        }
                                                    @endphp
                                                    <input class="form-control {{ $primary ? 'bg-dark text-light' : '' }}"
                                                        type="text" disabled value="{{ $displayValue }}">

                                                    @if ($header->type == 'image' && $fieldValue && $fieldValue !== 'noimage.png')
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/' . $fieldValue) }}" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-image me-1"></i>
                                                                Lihat {{ $header->alias }}
                                                            </a>
                                                        </div>
                                                    @endif

                                                    @if ($header->note != '')
                                                        <p class='text-secondary text-xs pt-1 px-1'>
                                                            {{ '*) ' . $header->note }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="col-md-6">
                                        @foreach ($table_header_r as $header)
                                            @php
                                                $fieldValue = $table_detail->{$header->field} ?? '';
                                                $primary = false;
                                                foreach ($table_primary as $p) {
                                                    if ($p->field == $header->field) {
                                                        $primary = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if ($header->show == '1' && $header->type != 'hidden' && $fieldValue !== '' && $fieldValue !== null)
                                                <div class="form-group">
                                                    <label class="form-control-label">{{ $header->alias }}</label>

                                                    @if ($header->type == 'text')
                                                        <textarea class="form-control {{ $primary ? 'bg-dark text-light' : '' }}" disabled>{{ $fieldValue }}</textarea>
                                                    @else
                                                        @php
                                                            $displayValueR = '';
                                                            if ($header->type == 'currency') {
                                                                $displayValueR = $format->CurrencyFormat(
                                                                    $fieldValue,
                                                                    $header->decimals ?? 0,
                                                                );
                                                            } elseif (
                                                                $header->type == 'date' ||
                                                                $header->type == 'datetime'
                                                            ) {
                                                                $displayValueR = $format->DateFormat($fieldValue);
                                                            } elseif ($header->type == 'enum') {
                                                                if ($header->query != '') {
                                                                    $data_query = DB::select($header->query);
                                                                    $displayValueR = $fieldValue;
                                                                    foreach ($data_query as $q) {
                                                                        $sAsArray = array_values((array) $q);
                                                                        if ($fieldValue == $sAsArray[0]) {
                                                                            $displayValueR = $sAsArray[1];
                                                                            break;
                                                                        }
                                                                    }
                                                                } else {
                                                                    $displayValueR = ucfirst($fieldValue);
                                                                }
                                                            } else {
                                                                $displayValueR = $fieldValue;
                                                            }
                                                        @endphp
                                                        <input
                                                            class="form-control {{ $primary ? 'bg-dark text-light' : '' }}"
                                                            type="text" disabled value="{{ $displayValueR }}">
                                                    @endif

                                                    @if ($header->type == 'image' && $fieldValue && $fieldValue !== 'noimage.png')
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/' . $fieldValue) }}" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-image me-1"></i>
                                                                Lihat {{ $header->alias }}
                                                            </a>
                                                        </div>
                                                    @endif

                                                    @if ($header->note != '')
                                                        <p class='text-secondary text-xs pt-1 px-1'>
                                                            {{ '*) ' . $header->note }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div>
                                    <h6 class="mb-0">Informasi</h6>
                                    <p class="text-sm mb-0 text-muted">Status dan informasi tambahan</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-control-label">Status</label>
                                    <span
                                        class="badge badge-sm bg-gradient-{{ ($table_detail->isactive ?? '1') == '1' ? 'success' : 'danger' }}">
                                        {{ ($table_detail->isactive ?? '1') == '1' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>

                                @if (isset($table_detail->created_at))
                                    <div class="form-group">
                                        <label class="form-control-label">Dibuat Pada</label>
                                        <input class="form-control" type="text" disabled
                                            value="{{ $format->DateFormat($table_detail->created_at) }}">
                                    </div>
                                @endif

                                @if (isset($table_detail->updated_at))
                                    <div class="form-group">
                                        <label class="form-control-label">Diupdate Pada</label>
                                        <input class="form-control" type="text" disabled
                                            value="{{ $format->DateFormat($table_detail->updated_at) }}">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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
