@extends('layouts.app')

@section('content')
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                @include('layouts.navbars.guest.navbar')
            </div>
        </div>
    </div>
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <img src="{{ asset('/storage' . '/' . @$setup_app->logo_large) }}"
                                class="navbar-brand-img pb-3 pt-5 h-50 w-50 rounded mx-auto d-block" alt="main_logo">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder">Selamat Datang</h4>
                                    <p class="mb-0">Masukkan Username Dan Password</p>
                                </div>
                                <div class="card-body">
                                    <form role="form" method="POST" action="{{ route('login.perform') }}">
                                        @csrf
                                        @method('post')
                                        <div class="flex flex-col mb-2 input-group">
                                            <input type="text" name="username"
                                                class="form-control form-control-lg lower notspace" key="true"
                                                placeholder="Username" aria-label="Name" value="">
                                            <span class="input-group-text" id="button-addon"
                                                style="border-color:#d2d6da;"><i class="fas fa-user"
                                                    style="cursor: pointer;"></i></span>
                                        </div>
                                        @error('username')
                                            <p class='text-danger text-xs'> {{ $message }} </p>
                                        @enderror
                                        <div class="flex flex-col mb-2 input-group pass">
                                            <input type="password" name="password" class="form-control form-control-lg"
                                                placeholder="Password" aria-label="Password" value=""
                                                aria-describedby="button-addon">
                                            <span class="input-group-text" id="button-addon"
                                                style="border-color:#d2d6da;"><i class="fas fa-eye showpass"
                                                    style="cursor: pointer;"></i></span>
                                        </div>
                                        @error('password')
                                            <p class="text-danger text-xs"> {{ $message }} </p>
                                        @enderror
                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Masuk</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                                style="background-image: url('{{ asset('/storage' . '/' . @$setup_app->cover_out) }}');
              background-size: cover;">
                                <span class="mask bg-gradient-primary opacity-6"></span>
                                <h4 class="mt-5 text-white font-weight-bolder position-relative">
                                    "{{ @$setup_app->company }}"
                                </h4>
                                <div class="list-group-item border-0 p-2 mb-2 bg-gray-100 border-radius-lg">
                                    <h6 class="mb-1 text-sm">{{ @$setup_app->address }}</h6>
                                    <h6 class="mb-1 text-sm">{{ @$setup_app->city . ' - ' . @$setup_app->province }}</h6>
                                </div>
                                <h6 class="mb-3 text-sm text-white position-relative">{{ @$setup_app->description }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
