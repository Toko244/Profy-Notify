@extends('layout.dashboard')

@section('header')
<div class="row">
    <div class="col-sm-6">
        <h3 class="mt-0">Notification Categories</h3>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                Notification Categories
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
                <h3 class="card-title">Notification Category List</h3>
                <div class="card-tools
                    d-flex
                    flex-row
                    align-items-center">
                    <a href="{{ route('notification-categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Create</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th style="width: 100px; text-align: center">Usage</th>
                            <th style="width: 100px; text-align: center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notificationCategories as $notificationCategory)
                            <tr class="align-middle">
                                <td>{{ $notificationCategory->title }}</td>
                                <td style="text-align: center">
                                   {{ $notificationCategory->notifications_count }}
                                </td>
                                <td style="text-align: center">
                                    <a style="padding: 0 3px 0 3px" href="{{ route('notifications.index', ['category_id' => $notificationCategory->id]) }}" class="text-info"><i class="bi bi-envelope-heart-fill"></i></a>
                                    <a style="padding: 0 3px 0 3px" href="{{ route('notification-categories.edit', ['notification_category' => $notificationCategory]) }}"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('notification-categories.destroy', ['notification_category' => $notificationCategory]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 0 3px 0 3px" class="btn btn-link text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-end">
                    {{ $notificationCategories->links('components.pagination') }}
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
