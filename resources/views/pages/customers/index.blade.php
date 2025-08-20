@extends('layout.dashboard')

@section('header')
<div class="row">
    <div class="col-sm-6">
        <h3 class="mb-0 mt-0">Customers</h3>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                Customers
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
                <h3 class="card-title">Customer List</h3>
                <div class="card-tools
                    d-flex
                    flex-row
                    align-items-center">
                    <form action="{{ route('customers.index') }}" method="GET">
                        <form action="" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 70px; text-align: center">Profy Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th style="width: 100px; text-align: center">Notifications</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr class="align-middle">
                                <td style="text-align: center"> <a style="text-decoration: none" href="https://dev.profy.ge/admin/users/{{ $customer->profy_id }}/view" target="_blank">{{ $customer->profy_id }}</a></td>
                                <td>{{ $customer->first_name }}</td>
                                <td>{{ $customer->last_name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td style="text-align: center">
                                    @if ($customer->allow_notification)
                                        <i class="bi bi-check text-success"></i>
                                    @else
                                        <i class="bi bi-x text-danger"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-end">
                    {{ $customers->links('components.pagination') }}
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
