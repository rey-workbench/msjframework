@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
{{-- section content --}}
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md">
                <form role="form" method="post" action="{{ URL::to('changepass/update') }}" id="changepass-form">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <p class="text-uppercase text-sm">Ganti Password</p>
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
                                        <label for="example-text-input" class="form-control-label">New Password</label>
                                        <div class="flex flex-col mb-2 input-group pass">
                                            <input type="password" name="new_password" class="form-control form-control-lg"
                                                placeholder="Password Baru" aria-label="Password" value="" required
                                                aria-describedby="button-addon">
                                            <span class="input-group-text" id="button-addon"
                                                style="border-color:#d2d6da;"><i class="fas fa-eye showpass"
                                                    style="cursor: pointer;"></i></span>
                                        </div>
                                        @error('new_password')
                                            <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="example-text-input" class="form-control-label">Confirm Password</label>
                                        <div class="flex flex-col mb-2 input-group pass">
                                            <input type="password" name="confirm_password"
                                                class="form-control form-control-lg" placeholder="Confirm Password"
                                                aria-label="Password" value="" required
                                                aria-describedby="button-addon">
                                            <span class="input-group-text" id="button-addon"
                                                style="border-color:#d2d6da;"><i class="fas fa-eye showpass"
                                                    style="cursor: pointer;"></i></span>
                                        </div>
                                        @error('confirm_password')
                                            <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="horizontal dark">
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-primary btn-sm"
                                    onclick="event.preventDefault(); document.getElementById('changepass-form').submit();">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
