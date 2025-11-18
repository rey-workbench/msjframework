<div class="px-4 pt-2">
    @if (Session::has('message'))
        <div class="alert alert-{{ Session::get('class') }} alert-dismissible fade show text-light p-2" role="alert">
            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
            <span class="alert-text"><strong>{{ Session::get('class') }}</strong>
                {{ Session::get('message') }}</span>
            <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
</div>
