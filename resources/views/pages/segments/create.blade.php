@extends('layout.dashboard')

@section('header')
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0 mt-0">Segments</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('segments.index') }}">Segments</a></li>
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
                    <h3 class="card-title">Segments</h3>
                </div>
                <form action="{{ route('segments.store') }}" method="POST" enctype="multipart/form-data">
                    <div class="card-body row">
                        @csrf
                        @include('components.field-text', [
                            'label' => 'Name',
                            'placeholder' => 'Name',
                            'required' => 'true',
                            'class' => 'col-md-12 col-sm-12',
                            'name' => 'name',
                        ])
                        @include('components.field-textarea', [
                            'label' => 'Description',
                            'placeholder' => 'Description',
                            'required' => 'true',
                            'class' => 'col-md-12 col-sm-12',
                            'name' => 'description',
                        ])
                        <div>
                            <label>File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-end">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
