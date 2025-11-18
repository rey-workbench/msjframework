@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $title_menu])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Edit {{ $title_menu }}</h6>
                            <a href="/{{ $url_menu }}" class="btn btn-secondary btn-sm ms-auto">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="/{{ $url_menu }}/update/{{ encrypt($item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- Left Column --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="name" 
                                               id="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $item->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $item->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" 
                                                id="status" 
                                                class="form-control @error('status') is-invalid @enderror">
                                            <option value="1" {{ old('status', $item->status) == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $item->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Right Column --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" 
                                                  id="description" 
                                                  rows="4" 
                                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $item->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="image" class="form-label">Image</label>
                                        @if($item->image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $item->image) }}" 
                                                     alt="Current Image" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 200px;">
                                            </div>
                                        @endif
                                        <input type="file" 
                                               name="image" 
                                               id="image" 
                                               class="form-control @error('image') is-invalid @enderror" 
                                               accept="image/*">
                                        <small class="form-text text-muted">Leave empty to keep current image</small>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Full Width Fields --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label for="notes" class="form-label">Additional Notes</label>
                                        <textarea name="notes" 
                                                  id="notes" 
                                                  rows="3" 
                                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $item->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <a href="/{{ $url_menu }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="reset" class="btn btn-warning">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Image preview
        $('#image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('.img-thumbnail').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        $('form').on('submit', function(e) {
            let isValid = true;
            
            // Check required fields
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill all required fields!',
                });
            }
        });

        // Remove invalid class on input
        $('.form-control').on('input change', function() {
            $(this).removeClass('is-invalid');
        });
    });
</script>
@endsection
