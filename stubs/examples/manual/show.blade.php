@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $title_menu])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Detail {{ $title_menu }}</h6>
                            <div class="ms-auto">
                                @if($authorize->edit == '1')
                                    <a href="/{{ $url_menu }}/edit/{{ encrypt($item->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
                                @if($authorize->delete == '1')
                                    <form action="/{{ $url_menu }}/destroy/{{ encrypt($item->id) }}" 
                                          method="POST" 
                                          class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm delete-button">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                                <a href="/{{ $url_menu }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information --}}
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-sm font-weight-bolder mb-3">
                                    <i class="fas fa-info-circle"></i> Basic Information
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">ID</label>
                                    <p class="form-control-static">{{ $item->id }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Name</label>
                                    <p class="form-control-static">{{ $item->name }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Email</label>
                                    <p class="form-control-static">
                                        <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Status</label>
                                    <p class="form-control-static">
                                        @if($item->status == '1')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Additional Information --}}
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-sm font-weight-bolder mb-3">
                                    <i class="fas fa-clipboard"></i> Additional Information
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Description</label>
                                    <p class="form-control-static">{{ $item->description ?? '-' }}</p>
                                </div>

                                @if($item->image)
                                    <div class="mb-3">
                                        <label class="form-label text-xs font-weight-bold text-uppercase">Image</label>
                                        <div>
                                            <img src="{{ asset('storage/' . $item->image) }}" 
                                                 alt="Item Image" 
                                                 class="img-thumbnail"
                                                 style="max-width: 300px; cursor: pointer;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal">
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Notes</label>
                                    <p class="form-control-static">{{ $item->notes ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Audit Information --}}
                            <div class="col-12 mt-4">
                                <hr>
                                <h6 class="text-uppercase text-sm font-weight-bolder mb-3">
                                    <i class="fas fa-history"></i> Audit Information
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Created By</label>
                                    <p class="form-control-static">{{ $item->user_create ?? '-' }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Created At</label>
                                    <p class="form-control-static">
                                        {{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Updated By</label>
                                    <p class="form-control-static">{{ $item->user_update ?? '-' }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-xs font-weight-bold text-uppercase">Updated At</label>
                                    <p class="form-control-static">
                                        {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i:s') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    @if($item->image)
        <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Image Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $item->image) }}" 
                             alt="Full Image" 
                             class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Delete confirmation with SweetAlert
        $('.delete-button').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('.delete-form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
