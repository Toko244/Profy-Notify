@extends('layout.dashboard')

@section('header')
<div class="row">
    <div class="col-sm-6">
        <h3 class="mb-0 mt-0">Edit Notifications</h3>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notifications</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                Edit
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary p-4 mb-4 shadow-sm rounded">
            <div class="card-header">
                <h3 class="card-title">Notification</h3>
            </div>
            <form action="{{ route('notifications.update', ['notification' => $notification]) }}" method="POST">
                <div class="card-body row">
                    @csrf
                    @method('PUT')
                    @livewire('notification.form', ['notification' => $notification])
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-end">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('styles')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
@endsection
