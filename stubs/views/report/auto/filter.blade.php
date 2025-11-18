@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
{{-- section content --}}
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="card shadow-lg mx-4">
        <div class="card-body p-3">
            <div class="row gx-4">
                <div class="col-lg">
                    <div class="nav-wrapper">
                        {{-- check authorize add --}}
                        @if ($authorize->add == '1')
                            {{-- button execute --}}
                            <button class="btn btn-primary mb-0"
                                onclick="event.preventDefault(); document.getElementById('{{ $dmenu }}-form').submit();"><i
                                    class="fas fa-bolt me-1"> </i><span class="font-weight-bold">Execute</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <form role="form" method="POST" action="{{ URL::to($url_menu) }}" id="{{ $dmenu }}-form">
                        @csrf
                        <div class="card-body">
                            <p class="text-uppercase text-sm">Filter {{ $title_menu }}</p>
                            <hr class="horizontal dark mt-0">
                            <div class="row">
                                {{-- retrieve table filter --}}
                                @foreach ($table_filter as $filter)
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{-- display label alias on type field not hidden --}}
                                            @if ($filter->type != 'hidden')
                                                <label for="example-text-input"
                                                    class="form-control-label">{{ $filter->alias }}</label>
                                            @endif
                                            {{-- field type char and string --}}
                                            @if ($filter->type == 'char' || $filter->type == 'string')
                                                <input class="form-control  {{ $filter->class }}" type="text"
                                                    value="{{ old($filter->field) }}" name="{{ $filter->field }}"
                                                    maxlength="{{ $filter->length }}">
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                                {{-- field type email --}}
                                            @elseif ($filter->type == 'email')
                                                <input class="form-control {{ $filter->class }}" type="email"
                                                    value="{{ old($filter->field) }}" name="{{ $filter->field }}"
                                                    maxlength="{{ $filter->length }}">
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                                {{-- field type hidden --}}
                                            @elseif ($filter->type == 'hidden')
                                                <input class="form-control {{ $filter->class }}" type="hidden"
                                                    value="{{ old($filter->field) }}" name="{{ $filter->field }}">
                                                {{-- field type date --}}
                                            @elseif ($filter->type == 'date')
                                                <input class="form-control {{ $filter->class }}" type="date"
                                                    value="{{ old($filter->field) }}" name="{{ $filter->field }}">
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                                {{-- field type date2 on between format --}}
                                            @elseif ($filter->type == 'date2')
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input class="form-control {{ $filter->class }}" type="date"
                                                            value="{{ old($filter->field) }}"
                                                            name="fr{{ $filter->field }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <input class="form-control {{ $filter->class }}" type="date"
                                                            value="{{ old($filter->field) }}"
                                                            name="to{{ $filter->field }}">
                                                    </div>
                                                </div>
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                                {{-- field type text --}}
                                            @elseif ($filter->type == 'text')
                                                <textarea class="form-control {{ $filter->class }}" name="{{ $filter->field }}" maxlength="{{ $filter->length }}">{{ old($filter->field) }}</textarea>
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                                {{-- field type number --}}
                                            @elseif ($filter->type == 'number')
                                                <input class="form-control {{ $filter->class }}" type="number"
                                                    value="{{ old($filter->field) }}" name="{{ $filter->field }}"
                                                    max="{{ $filter->length }}">
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                                {{-- field type search --}}
                                            @elseif ($filter->type == 'search')
                                                <div class="flex flex-col mb-2 input-group">
                                                    <input type="text" name="{{ $filter->field }}"
                                                        class="form-control {{ $filter->class }}"
                                                        value="{{ old($filter->field) ? old($filter->field) : $filter->default }}">
                                                    <span class="input-group-text bg-primary text-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#searchModal{{ $filter->field }}"
                                                        style="border-color:#d2d6da;border-left:3px solid #d2d6da;cursor: pointer;"><i
                                                            class="fas fa-search"></i></span>
                                                </div>
                                                <!-- Modal -->
                                                <div class="modal fade" id="searchModal{{ $filter->field }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="searchModalLabel">
                                                                    List Data
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            @if ($filter->query != '')
                                                                @php
                                                                    $table_result = DB::select($filter->query);
                                                                @endphp
                                                            @endif
                                                            <div class="modal-body table-responsive">
                                                                <table class="table display"
                                                                    id="list_{{ $dmenu }}_{{ $filter->field }}">
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
                                                                                    <td width="20px"><span
                                                                                            class="btn badge bg-primary badge-lg"
                                                                                            onclick="select_modal{{ $filter->field }}('{{ $modal_d->{$field[0]} }}')"><i
                                                                                                class="bi bi-check-circle me-1"></i>
                                                                                            Select</span>
                                                                                    </td>
                                                                                    @foreach ($field as $filter_field)
                                                                                        @php
                                                                                            $string = $filter_field;
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
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                                <script>
                                                    $('document').ready(function() {
                                                        $('#list_{{ @$dmenu }}_{{ $filter->field }}').DataTable();
                                                    })

                                                    function select_modal{{ $filter->field }}(id, name) {
                                                        $('input[name="{{ $filter->field }}"]').val(id);
                                                        $('#searchModal{{ $filter->field }}').modal('hide');
                                                    }
                                                </script>
                                                {{-- field type enum --}}
                                            @elseif ($filter->type == 'enum')
                                                <select class="form-select {{ $filter->class }}"
                                                    name="{{ $filter->field }}">
                                                    <option value=""></option>
                                                    @if ($filter->query != '')
                                                        @php
                                                            $data_query = DB::select($filter->query);
                                                        @endphp
                                                        @foreach ($data_query as $q)
                                                            <?php $sAsArray = array_values((array) $q); ?>
                                                            <option value="{{ $sAsArray[0] }}"
                                                                where="{{ $filter->sub != '' ? $sAsArray[2] : '' }}">
                                                                {{ $sAsArray[0] }} - {{ $sAsArray[1] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($filter->sub != '')
                                                    <script>
                                                        $('select[name="{{ $filter->field }}"] option').hide();
                                                        $('select[name="{{ $filter->field }}"] option[where="' + $('select[name="{{ $filter->sub }}"]').val() + '"]')
                                                            .show();
                                                        $('select[name="{{ $filter->sub }}"]').change(function() {
                                                            $('select[name="{{ $filter->field }}"] option').hide();
                                                            $('select[name="{{ $filter->field }}"] option[where="' + $(this).val() + '"]').show();
                                                        });
                                                    </script>
                                                @endif
                                                @if ($filter->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $filter->note }}
                                                    </p>
                                                @endif
                                            @endif
                                            @error($filter->field)
                                                <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr class="horizontal dark">
                        </div>
                        <div class="card-footer align-items-center pt-0 pb-2">

                        </div>
                    </form>
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
        $(document).ready(function() {});
    </script>
@endpush
