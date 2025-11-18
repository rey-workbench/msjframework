@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $title_menu])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- Card Header --}}
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">{{ $title_menu }}</h5>
                                <p class="text-sm mb-0">
                                    {{ $title_group }} - List of {{ $title_menu }}
                                </p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                @if($authorize->add == '1')
                                    <a href="/{{ $url_menu }}/add" class="btn btn-sm btn-primary mb-0">
                                        <i class="fas fa-plus"></i> Add New
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body px-0 pb-0">
                        <div class="table-responsive">
                            <table class="table table-flush" id="datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $index => $item)
                                        <tr>
                                            <td class="text-sm">{{ $items->firstItem() + $index }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->name }}</td>
                                            <td class="text-sm">{{ $item->email }}</td>
                                            <td class="text-sm">{{ $item->description }}</td>
                                            <td class="text-sm">
                                                @if($item->isactive == '1')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="/{{ $url_menu }}/show/{{ encrypt($item->id) }}" 
                                                   class="btn btn-link text-info px-2 mb-0">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($authorize->edit == '1')
                                                    <a href="/{{ $url_menu }}/edit/{{ encrypt($item->id) }}" 
                                                       class="btn btn-link text-warning px-2 mb-0">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($authorize->delete == '1')
                                                    <form action="/{{ $url_menu }}/destroy/{{ encrypt($item->id) }}" 
                                                          method="POST" 
                                                          class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-link text-danger px-2 mb-0 delete-button">
                                                            @if($item->isactive == '1')
                                                                <i class="fas fa-trash"></i>
                                                            @else
                                                                <i class="fas fa-check"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="px-3 py-3">
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    // Delete confirmation
    $(document).on('click', '.delete-button', function(e) {
        e.preventDefault();
        const form = $(this).closest('.delete-form');
        const isActive = $(this).find('.fa-trash').length > 0;
        
        Swal.fire({
            title: 'Are you sure?',
            text: isActive ? "This will deactivate the data!" : "This will activate the data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#d33' : '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isActive ? 'Yes, deactivate it!' : 'Yes, activate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection
