@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
{{-- section content --}}
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Profile'])
    <div class="card shadow-lg mx-4 card-profile">
        <div class="card-body p-3">
            <div class="row gx-4">
                <div class="col-auto">
                    <div class="avatar avatar-xl position-relative">
                        {{-- photo profile --}}
                        <img src="{{ asset('storage/' . $user_login->image) }}" alt="profile_image"
                            class="w-100 border-radius-lg shadow-sm">
                    </div>
                </div>
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            {{ $user_login->firstname }}
                        </h5>
                        <p class="mb-0 font-weight-bold text-sm">
                            {{ session('username') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md">
                <form role="form" method="post" action="{{ URL::to('profile/update') }}" id="profile-form"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <p class="text-uppercase text-sm">Informasi Akun</p>
                            <hr class="horizontal dark mt-0">
                            {{-- alert --}}
                            @include('components.alert')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="form-control-label">Username</label>

                                        <p class="px-2"><label for="example-text-input"
                                                class="form-control-label text-primary">{{ session('username') }}</label>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="form-control-label">Email address</label>
                                        <input class="form-control" type="email" name="profile_email"
                                            value="{{ $user_login->email }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="form-control-label">First name</label>
                                        <input class="form-control" type="text" name="profile_firstname"
                                            value="{{ $user_login->firstname }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="form-control-label">Last name</label>
                                        <input class="form-control" type="text" name="profile_lastname"
                                            value="{{ $user_login->lastname }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-sm-auto">
                                        <div class="position-relative">
                                            <div>
                                                <label for="file-input" style="left: -5px !important;" id="ProfileImageedit"
                                                    class="btn btn-xxl btn-icon-only bg-gradient-primary position-absolute bottom-0 mb-n2">
                                                    <i class="fa fa-pen top-0" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="" aria-hidden="true"
                                                        data-bs-original-title="Edit Image" aria-label="Edit Image"></i>
                                                    <span class="sr-only">Edit Image</span>
                                                </label>
                                                <span class="h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                                    <img src="{{ asset('storage/' . $user_login->image) }}"
                                                        id="preview" alt="image" data-bs-toggle="modal"
                                                        data-bs-target="#imageModal"
                                                        class="w-30 border-radius-lg shadow-sm">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <input class="form-control" type="file" value="{{ $user_login->image }}"
                                        id="ProfileImage" name="profile_image" style="display: none;">
                                    <p class='text-primary text-xs pt-2 mb-0'>Maksimal Size :
                                        <b>2048 KB</b>
                                    </p>
                                    <p class='text-primary text-xs pt-1'>Format Image :
                                        <b>PNG,JPG,JPEG</b>
                                    </p>
                                    <script>
                                        ProfileImage.onchange = evt => {
                                            const [file] = ProfileImage.files
                                            if (file) {
                                                preview.src = URL.createObjectURL(file)
                                            }
                                        }
                                        $('#ProfileImageedit').click(function() {
                                            $('input[name="profile_image"]').click();

                                        });
                                    </script>
                                </div>
                            </div>
                            <hr class="horizontal dark">
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-primary btn-sm"
                                    onclick="event.preventDefault(); document.getElementById('profile-form').submit();">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection