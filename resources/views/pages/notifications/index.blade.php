@extends('layout.dashboard')

@section('header')
<div class="row">
    <div class="col-sm-6">
        <h3 class="mt-0">Notifications</h3>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                Notifications
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
                <h3 class="card-title">Notification List</h3>
                <div class="card-tools
                    d-flex
                    flex-row
                    align-items-center">

                    <button type="button" onclick="toggleFiltration()" class="btn btn-info" style="margin-right: 20px">
                        <i class="bi bi-filter"></i> Filter
                    </button>

                    <a href="{{ route('notifications.create') }}" class="btn btn-primary" >
                        <i class="bi bi-plus-lg"></i> Create
                    </a>
                </div>
                <div class="filtration hidden" id="filtrationBlock" style="padding: 50px 0 20px">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    @livewire('form.text', ['name' => 'title', 'label' => 'Title', 'value' => request()->get('title')])
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    @livewire('form.select', ['name' => 'category_id', 'nullable' => true, 'label' => 'Category', 'options' => $notificationCategories, 'selected' => request()->get('category_id'), 'option_value' => 'key'], key('notification-category'))
                                </div>
                            </div>
                        </div>

                        <div class="filter-btns">

                            <button type="button" onclick="clearFilters()" class="btn btn-secondary table-header-btn filter-btn">
                                Clear
                            </button>
                            <button type="submit" class="btn btn-info table-header-btn filter-btn">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th style="width: 100px; text-align: center">Active</th>
                            <th style="width: 100px; text-align: center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            <tr class="align-middle">
                                <td>{{ $notification->title }}</td>
                                <td style="text-align: center">
                                    @if ($notification->active)
                                        <i class="bi bi-check text-success"></i>
                                    @else
                                        <i class="bi bi-x text-danger"></i>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    <a style="padding: 0 3px 0 3px"  href="{{ route('notifications.edit', ['notification' => $notification]) }}"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('notifications.destroy', ['notification' => $notification]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button style="padding: 0 3px 0 3px"  type="submit" class="btn btn-link text-danger" onclick="alert('Are you sure you want to delete this notification?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-end">
                    {{ $notifications->links('components.pagination') }}
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    function toggleFiltration() {
        var element = document.getElementById("filtrationBlock");
        element.classList.toggle("hidden");
    }
    function clearFilters() {
        window.location = location.protocol + '//' + location.host + location.pathname;
    }
</script>
@endsection

@section('styles')

<style>
    .hidden {
        display: none;
    }

    .filter-btns {
        margin-top: 20px;
        text-align:right
    }
</style>

@endsection
