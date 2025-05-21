@extends('layout.dashboard')

@section('header')
<div class="row">
    <div class="col-sm-6">
        <h3 class="mb-0 mt-0">Symlink</h3>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('symlinks.index') }}">Symlinks</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                Create
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Symlink</h3>
            </div>
            <form action="{{ route('symlinks.update', ['symlink' => $symlink]) }}" method="POST">
                <div class="card-body row">
                    @csrf
                    @method('PUT')
                    @include('components.field-text', [
                        "label" => 'Title',
                        "placeholder" => 'Title',
                        "required" => 'true',
                        "class" => 'col-md-12 col-sm-12',
                        "name" => 'title',
                        "value" => $symlink->title,
                    ])
                    @include('components.field-text', [
                        "label" => 'Link',
                        "placeholder" => 'Link',
                        "required" => 'true',
                        "class" => 'col-md-12 col-sm-12',
                        "name" => 'link',
                        "value" => $symlink->original,
                    ])

                    @include('components.field-text', [
                        "label" => 'Symlink',
                        "placeholder" => 'Symlink',
                        "required" => 'true',
                        "class" => 'col-md-12 col-sm-12 mt-2',
                        "name" => 'symlink',
                        "disabled" => 'true',
                        "value" => $symlink->symlink,
                    ])

                    @include('components.field-text', [
                        "label" => 'Usage Count',
                        "placeholder" => 'Count',
                        "required" => 'true',
                        "class" => 'col-md-12 col-sm-12 mt-2',
                        "name" => 'usage_count',
                        "disabled" => 'true',
                        "value" => $symlink->usage_count,
                    ])

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-end">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
