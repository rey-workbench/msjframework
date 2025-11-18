@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
{{-- section content --}}
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="card shadow-lg mx-4">
        <div class="card-body p-3">
            <div class="row gx-4">
                <div class="col-lg">
                    <div class="nav-wrapper">
                        {{-- button back --}}
                        <button class="btn btn-secondary mb-0" onclick="history.back()"><i class="fas fa-circle-left me-1">
                            </i><span class="font-weight-bold">Kembali</button>
                        {{-- check authorize add --}}
                        @if ($authorize->add == '1')
                            {{-- button save --}}
                            <button class="btn btn-primary mb-0"
                                onclick="event.preventDefault(); document.getElementById('{{ $dmenu }}-form').submit();"><i
                                    class="fas fa-floppy-disk me-1"> </i><span class="font-weight-bold">Simpan</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <form role="form" method="POST" action="{{ URL::to($url_menu) }}" id="{{ $dmenu }}-form"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <p class="text-uppercase text-sm">Insert {{ $title_menu }}</p>
                            <hr class="horizontal dark mt-0">
                            <div class="row">
                                {{-- retrieve table header --}}
                                @foreach ($table_header as $header)
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{-- display label alias on type field not hidden --}}
                                            @if ($header->type != 'hidden')
                                                <label for="example-text-input" class="form-control-label"
                                                    {{ $primary ? ($generateid ? ' style=display:none;' : '') : '' }}>{{ $header->alias }}</label>
                                            @endif
                                            {{-- field type char and string --}}
                                            @if ($header->type == 'char' || $header->type == 'string')
                                                <input class="form-control  {{ $header->class }}"
                                                    {{ $primary ? ($generateid ? ' key=true type=hidden' : ' type=text') : ' type=text' }}
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}" maxlength="{{ $header->length }}">
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                                {{-- field type email --}}
                                            @elseif ($header->type == 'email')
                                                <input class="form-control {{ $header->class }}"
                                                    {{ $primary ? ($generateid ? ' key=true type=hidden' : ' type=email') : ' type=email' }}
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}" maxlength="{{ $header->length }}">
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                                {{-- field type text --}}
                                            @elseif ($header->type == 'text')
                                                <textarea class="form-control {{ $header->class }}" name="{{ $header->field }}" maxlength="{{ $header->length }}">{{ old($header->field) ? old($header->field) : $header->default }}</textarea>
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                                {{-- field type number --}}
                                            @elseif ($header->type == 'number')
                                                <input class="form-control {{ $header->class }}"
                                                    {{ $primary ? ($generateid ? ' key=true type=hidden' : ' type=number') : ' type=number' }}
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}" max="{{ $header->length }}">
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                                {{-- field type currency --}}
                                            @elseif ($header->type == 'currency')
                                                <input class="form-control {{ $header->class }}"
                                                    {{ $primary ? ($generateid ? ' key=true type=hidden' : ' type=number') : ' type=number' }}
                                                    value="{{ old($header->field) ? old($header->field) : $header->default }}"
                                                    name="{{ $header->field }}" max="{{ $header->length }}">
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                                {{-- field type file --}}
                                            @elseif ($header->type == 'file')
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    value="{{ old($header->field) }}" name="{{ $header->field }}">
                                                <p class='text-primary text-xs pt-3 mb-0'>Maksimal Size :
                                                    <b>{{ $header->length }} KB</b>
                                                </p>
                                                @if ($header->note != '')
                                                    <p class='text-primary text-xs pt-1'>
                                                        {{ $header->note }}
                                                    </p>
                                                @else
                                                    <p class='text-primary text-xs pt-1'>Format File :
                                                        <b>pdf,xls,xlsx</b>
                                                    </p>
                                                @endif
                                                {{-- field type search --}}
                                            @elseif ($header->type == 'search')
                                                <div class="flex flex-col mb-2 input-group">
                                                    <input
                                                        {{ $primary ? ($generateid ? ' key=true type=hidden' : ' type=text') : ' type=text' }}
                                                        name="{{ $header->field }}"
                                                        class="form-control {{ $header->class }}"
                                                        value="{{ old($header->field) ? old($header->field) : $header->default }}">
                                                    <span class="input-group-text bg-primary text-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#searchModal{{ $header->field }}"
                                                        style="border-color:#d2d6da;border-left:3px solid #d2d6da;cursor: pointer;"><i
                                                            class="fas fa-search"></i></span>
                                                </div>
                                                <!-- Modal -->
                                                <div class="modal fade" id="searchModal{{ $header->field }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
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
                                                            @if ($header->query != '')
                                                                @php
                                                                    $table_result = DB::select($header->query);
                                                                @endphp
                                                            @endif
                                                            <div class="modal-body">
                                                                <table class="table display" id="list_{{ $dmenu }}">
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
                                                                                            onclick="select_modal('{{ $modal_d->{$field[0]} }}')"><i
                                                                                                class="bi bi-check-circle me-1"></i>
                                                                                            Select</span>
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
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                                <script>
                                                    function select_modal(id, name) {
                                                        $('input[name="{{ $header->field }}"]').val(id);
                                                        $('#searchModal{{ $header->field }}').modal('hide');
                                                    }
                                                </script>
                                                {{-- field type image --}}
                                            @elseif ($header->type == 'image')
                                                <div class="col-sm-auto">
                                                    <div class="position-relative">
                                                        <div>
                                                            <label for="file-input" style="left: -5px !important;"
                                                                id="{{ $header->field }}edit"
                                                                class="btn btn-xxl btn-icon-only bg-gradient-primary position-absolute bottom-0 mb-n2">
                                                                <i class="fa fa-pen top-0" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" title=""
                                                                    aria-hidden="true" data-bs-original-title="Edit Image"
                                                                    aria-label="Edit Image"></i>
                                                                <span class="sr-only">Edit Image</span>
                                                            </label>
                                                            <span
                                                                class="h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                                                <img src="{{ asset('/storage/noimage.png') }}"
                                                                    id="{{ $header->field }}preview" alt="image"
                                                                    class="w-30 border-radius-lg shadow-sm">
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input class="form-control {{ $header->class }}" type="file"
                                                    value="{{ old($header->field) }}" id="{{ $header->field }}"
                                                    name="{{ $header->field }}" style="display: none;">
                                                <p class='text-primary text-xs pt-3 mb-0'>Maksimal Size :
                                                    <b>{{ $header->length }} KB</b>
                                                </p>
                                                @if ($header->note != '')
                                                    <p class='text-primary text-xs pt-1'>
                                                        {{ $header->note }}
                                                    </p>
                                                @else
                                                    <p class='text-primary text-xs pt-1'>Format Image :
                                                        <b>PNG,JPG,JPEG</b>
                                                    </p>
                                                @endif
                                                <script>
                                                    {{ $header->field }}.onchange = evt => {
                                                        const [file] = {{ $header->field }}.files
                                                        if (file) {
                                                            {{ $header->field . 'preview' }}.src = URL.createObjectURL(file)
                                                        }
                                                    }
                                                    $('#{{ $header->field }}edit').click(function() {
                                                        $('input[name="{{ $header->field }}"]').click();

                                                    });
                                                </script>
                                                {{-- field type password --}}
                                            @elseif ($header->type == 'password')
                                                <input class="form-control {{ $header->class }}" type="hidden"
                                                    value="{{ $header->default }}" name="{{ $header->field }}"
                                                    max="{{ $header->length }}">
                                                <p class='text-primary text-xs pt-1'>Default Password :
                                                    <b>{{ $header->default }}</b>
                                                </p>
                                                {{-- field type date --}}
                                            @elseif ($header->type == 'date')
                                                <input class="form-control {{ $header->class }}"
                                                    {{ $primary ? ($generateid ? ' key=true type=hidden' : ' type=date') : ' type=date' }}
                                                    value="{{ old($header->field) }}" name="{{ $header->field }}">
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                                {{-- field type hidden --}}
                                            @elseif ($header->type == 'hidden')
                                                <input class="form-control {{ $header->class }}" type="hidden"
                                                    value="{{ $header->default }}" name="{{ $header->field }}"
                                                    max="{{ $header->length }}">
                                                {{-- field type enum --}}
                                            @elseif ($header->type == 'enum')
                                                @if (@$wherekey[$header->field])
                                                    <br>
                                                    <span for="example-text-input" style="margin: 10px;"
                                                        class="form-control-label">{{ @$wherekey[$header->field] }}</span>
                                                @endif
                                                <select class="form-select {{ $header->class }}"
                                                    {{ @$wherekey[$header->field] ? ' key=true style=display:none' : '' }}
                                                    name="{{ $header->field }}{{ Str::contains($header->class, 'select-multiple') ? '[]' : '' }}">
                                                    <option value=""></option>
                                                    @if ($header->query != '')
                                                        @php
                                                            $data_query = DB::select($header->query);
                                                        @endphp
                                                        @foreach ($data_query as $q)
                                                            <?php $sAsArray = array_values((array) $q); ?>
                                                            <option value="{{ $sAsArray[0] }}"
                                                                where="{{ $header->sub != '' ? $sAsArray[2] : '' }}">
                                                                {{ $sAsArray[0] }} - {{ $sAsArray[1] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($header->sub != '')
                                                    <script>
                                                        $('select[name="{{ $header->field }}"] option').hide();
                                                        $('select[name="{{ $header->field }}"] option[where="' + $('select[name="{{ $header->sub }}"]').val() + '"]')
                                                            .show();
                                                        $('select[name="{{ $header->sub }}"]').change(function() {
                                                            $('select[name="{{ $header->field }}"] option').hide();
                                                            $('select[name="{{ $header->field }}"] option[where="' + $(this).val() + '"]').show();
                                                        });
                                                    </script>
                                                @endif
                                                @if (@$wherekey[$header->field])
                                                    <script>
                                                        $('document').ready(function() {
                                                            $('select[name="{{ $header->field }}"]').val('{{ $wherekey[$header->field] }}');
                                                            $('select[name="{{ $header->field }}"]').attr('readonly');
                                                        })
                                                    </script>
                                                @endif
                                                @if ($header->note != '')
                                                    <p class='text-secondary text-xs pt-1 px-1'>
                                                        {{ '*) ' . $header->note }}
                                                    </p>
                                                @endif
                                            @endif
                                            @error($header->field)
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
