@extends('layout.dashboard')

@section('header')
<div class="row">
    <div class="col-sm-6">
        <h3 class="mt-0">Symlinks</h3>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                SymLinks
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Symlink List</h3>
                <div class="card-tools
                    d-flex
                    flex-row
                    align-items-center">
                    <a href="{{ route('symlinks.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Create</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Original</th>
                            <th>Symlink</th>
                            <th style="width: 100px; text-align: center">Usage</th>
                            <th style="width: 100px; text-align: center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($symlinks as $symlink)
                            <tr class="align-middle">
                                <td>{{ $symlink->title ?? ''}}</td>
                                <td>{{ $symlink->original }}</td>
                                <td>
                                    {{ $symlink->symlink }}
                                    <span class="copy float-end" style="color: #0b5ed7; cursor: pointer; margin-right:10px" data-link="{{ $symlink->symlink }}"><i class="bi bi-clipboard"></i> Copy</span>
                                </td>
                                <td style="text-align: center">
                                    {{ $symlink->usage_count }}
                                </td>
                                <td style="text-align: center">
                                    <a style="padding: 0 3px 0 3px"  href="{{ route('symlinks.edit', ['symlink' => $symlink]) }}"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('symlinks.destroy', ['symlink' => $symlink]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button style="padding: 0 3px 0 3px"  type="submit" class="btn btn-link text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-end">
                    {{ $symlinks->links('components.pagination') }}
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(".copy").click(function() {
        navigator.clipboard.writeText($(this).data('link'));
        toastr.success('Link copied to clipboard')
    });
</script>
@endsection
