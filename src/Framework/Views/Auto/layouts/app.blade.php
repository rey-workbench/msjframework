<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('/storage' . '/' . @$setup_app->icon) }}">
    <title>
        {{ @$setup_app->appname }} | {{ $title_menu }}
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('/') }}assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('/') }}assets/css/nucleo-svg.css" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link href="{{ asset('/') }}assets/css/fontawesome.css" rel="stylesheet">
    <link href="{{ asset('/') }}assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('/') }}assets/css/argon-dashboard.css" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('/') }}assets/css/sweetalert2.min.css">

    <!-- datatables -->
    <link rel="stylesheet" href="{{ asset('/') }}assets/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('/') }}assets/css/buttons.dataTables.min.css">

    {{-- Jquery --}}
    <script src="{{ asset('/') }}assets/js/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('/') }}assets/js/kitfontawesome.js" crossorigin="anonymous"></script>

    {{-- Select2 --}}
    <link rel="stylesheet" href="{{ asset('/') }}assets/css/select2.min.css">
    <script src="{{ asset('/') }}assets/js/select2.min.js" crossorigin="anonymous"></script>
</head>

<body class="{{ $class ?? '' }}">
    @guest
        @yield('content')
        @include('layouts.footers.auth.footer')
    @endguest

    @auth
        <div class="position-absolute w-100 min-height-300 top-0"
            style="background-image: url('{{ asset('/storage' . '/' . @$setup_app->cover_in) }}'); background-size: cover;">
            <span class="mask bg-primary opacity-4"></span>
        </div>
        @include('layouts.navbars.auth.sidenav')
        <main class="main-content border-radius-lg">
            @yield('content')
            @include('layouts.footers.auth.footer')
        </main>
        {{-- @include('components.fixed-plugin') --}}
    @endauth

    <!--   Core JS Files   -->
    <script src="{{ asset('/') }}assets/js/core/popper.min.js"></script>
    <script src="{{ asset('/') }}assets/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('/') }}assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="{{ asset('/') }}assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            //class readonly
            $('.readonly').attr('readonly', '');
            $('.not').css('background-color', '#ffe9ed');
            //class notspace
            $('.notspace').focusout(function() {
                var currentValue = $(this).val();
                var newValue = currentValue.replace(/\s/g, "");
                $(this).val(newValue);
            });
            //class notspace
            $('.notspace').keyup(function() {
                var currentValue = $(this).val();
                var newValue = currentValue.replace(/\s/g, "");
                $(this).val(newValue);
            });
            //custom select2
            $('.select-multiple').attr('multiple', 'multiple');
            $('.custom-select').select2();
            //default focus input attributes key
            $('input[key="true"]').focus();
            //delete start-end space
            $('input[type="text"]').focusout(function() {
                $(this).val($(this).val().trim());
            })
            //upper value
            $('.upper').keyup(function() {
                $(this).val($(this).val().toUpperCase());
            });
            $('.upper').focusout(function() {
                $(this).val($(this).val().toUpperCase());
            });
            //lower value
            $('.lower').keyup(function() {
                $(this).val($(this).val().toLowerCase());
            });
            $('.lower').focusout(function() {
                $(this).val($(this).val().toLowerCase());
            });
            // function set color row DataTable
            $('.odd').click(function() {
                $('.odd').css('background-color', '');
                $('.even').css('background-color', '');
                $('.exp').css('background-color', '#ffe768');
                $('.stock').css('background-color', '#f93c3c');
                $('.not').css('background-color', '#ffe9ed');
                $(this).css('background-color', '#f9f4ea');
            });
            $('.even').click(function() {
                $('.odd').css('background-color', '');
                $('.even').css('background-color', '');
                $('.exp').css('background-color', '#ffe768');
                $('.stock').css('background-color', '#f93c3c');
                $('.not').css('background-color', '#ffe9ed');
                $(this).css('background-color', '#f9f4ea');
            });
            var table = $('#list_{{ @$dmenu }}').DataTable();
            // Add the click event to all rows on all pages
            table.on('draw', function() {
                $('.odd').click(function() {
                    $('.odd').css('background-color', '');
                    $('.even').css('background-color', '');
                    $('.exp').css('background-color', '#ffe768');
                    $('.stock').css('background-color', '#f93c3c');
                    $('.not').css('background-color', '#ffe9ed');
                    $(this).css('background-color', '#f9f4ea');
                });
                $('.even').click(function() {
                    $('.odd').css('background-color', '');
                    $('.even').css('background-color', '');
                    $('.exp').css('background-color', '#ffe768');
                    $('.stock').css('background-color', '#f93c3c');
                    $('.not').css('background-color', '#ffe9ed');
                    $(this).css('background-color', '#f9f4ea');
                });
            });
            // end function set color row DataTable
            //show password
            $('.showpass').click(function() {
                var clas = $(this).attr('class');
                var inp = $(this).parents('.pass');

                if (clas == 'fas fa-eye showpass') {
                    $(this).attr('class', 'fas fa-eye-slash showpass');
                    inp.find('input').attr('type', 'text');
                    inp.find('input').focus();
                } else {
                    $(this).attr('class', 'fas fa-eye showpass');
                    inp.find('input').attr('type', 'password');
                    inp.find('input').focus();
                }
            });
            // Auto close the alert after 2,5 seconds (2500 milliseconds)
            window.setTimeout(function() {
                $(".alert").alert('close');
            }, 2500);
        })
    </script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('/') }}assets/js/argon-dashboard.js"></script>
    <!-- pdf make -->
    <script src="{{ asset('/') }}assets/js/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('/') }}assets/js/pdfmake/vfs_fonts.js"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('/') }}assets/js/sweetalert2.all.min.js"></script>
    <!-- Data table baru -->
    <script src="{{ asset('/') }}assets/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('/') }}assets/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('/') }}assets/js/buttons.html5.min.js"></script>
    <script src="{{ asset('/') }}assets/js/buttons.print.min.js"></script>
    <script src="{{ asset('/') }}assets/js/jszip.min.js"></script>

    <!-- Chart.js Global -->
    <script src="{{ asset('/') }}assets/js/chart.min.js"></script>

    {{-- add js --}}
    @stack('js')
    @stack('addjs')
</body>

</html>
