@extends('layout.dashboard')

@section('header')
<div class="row">
    <div class="col-sm-6">
        <h3 class="mb-0 mt-0">Dashboard</h3>
    </div>
</div>
<style>
    .collapse-toggle .bi {
        transition: transform 0.3s ease;
    }

    .collapse-toggle[aria-expanded="true"] .bi {
        transform: rotate(180deg);
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Total Customers -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Total Customers</h6>
                <h3 class="mb-0">{{ $total_customers }}</h3>
            </div>
        </div>
    </div>

    <!-- Orders (collapsible details) -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Orders</h6>
                        <h3 class="mb-0">{{ $total_orders }}</h3>
                    </div>
                    <button class="btn btn-sm btn-light collapse-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#orderDetails" aria-expanded="false" aria-controls="orderDetails">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <div class="collapse mt-3" id="orderDetails">
                    <div class="border-top pt-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Active</span>
                            <span>{{ $active_orders }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Finished</span>
                            <span>{{ $finished_orders }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications (collapsible details) -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Notifications</h6>
                        <h3 class="mb-0">{{ $total_notifications }}</h3>
                    </div>
                    <button class="btn btn-sm btn-light collapse-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#notificationDetails" aria-expanded="false" aria-controls="notificationDetails">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <div class="collapse mt-3" id="notificationDetails">
                    <div class="border-top pt-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Active</span>
                            <span>{{ $active_notifications }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Inactive</span>
                            <span>{{ $inactive_notifications }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
