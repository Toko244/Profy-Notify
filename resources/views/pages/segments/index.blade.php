@extends('layout.dashboard')

@section('header')
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mt-0">Segments</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Segments
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
                    <h3 class="card-title">Segments</h3>
                    <div
                        class="card-tools
                    d-flex
                    flex-row
                    align-items-center">
                        <a href="{{ route('segments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i>
                            Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Customers</th>
                                <th style="width: 100px; text-align: center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($segments as $segment)
                                <tr class="align-middle">
                                    <td>{{ $segment->name ?? '' }}</td>
                                    <td>{{ $segment->description }}</td>
                                    <td>{{ $segment->customers()->count() }}</td>
                                    <td style="text-align: center">
                                        <form action="{{ route('segments.destroy', ['segment' => $segment]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button style="padding: 0 3px 0 3px" type="submit"
                                                class="btn btn-link text-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-end">
                        {{ $segments->links('components.pagination') }}
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
