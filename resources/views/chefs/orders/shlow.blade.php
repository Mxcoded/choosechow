@extends('layouts.dashboard')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <h1 class="h2 fw-bold text-dark mb-0">Order #{{ $order->order_number }}</h1>
                    <span class="badge bg-{{ $order->status_color }} fs-6 rounded-pill">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <p class="text-muted mt-2 mb-0">
                    Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }} by {{ $order->customer->name }}
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                {{-- ROUTE FIX: chef.orders.index --}}
                <a href="{{ route('chef.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Actions & Items --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Update Status</h5></div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $transitions = [
                                'pending' => ['confirmed' => ['Confirm Order', 'btn-primary']],
                                'confirmed' => ['preparing' => ['Start Preparation', 'btn-info text-white']],
                                'preparing' => ['ready' => ['Mark Ready', 'btn-primary']],
                                'ready' => ['out_for_delivery' => ['Out for Delivery', 'btn-warning text-dark']],
                                'out_for_delivery' => ['delivered' => ['Mark Delivered', 'btn-success']],
                            ];
                            $actions = $transitions[$order->status] ?? [];
                        @endphp

                        @foreach($actions as $status => $details)
                            <button class="btn {{ $details[1] }} btn-lg" onclick="updateStatus('{{ $status }}', '{{ $details[0] }}')">
                                {{ $details[0] }}
                            </button>
                        @endforeach

                        @if(!in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                            <button class="btn btn-outline-danger btn-lg ms-auto" onclick="alert('Cancellation requires admin approval or customer request.')">
                                Cancel Order
                            </button>
                        @endif
                        
                        @if(empty($actions) && $order->status === 'delivered')
                            <div class="alert alert-success w-100 mb-0">
                                <i class="fas fa-check-circle me-2"></i> This order has been completed.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Order Items</h5></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($order->items as $item)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-3">
                                        <div class="bg-light rounded p-2 text-center" style="width: 50px; height: 50px;">
                                            <span class="fw-bold text-muted">x{{ $item->quantity }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $item->menu_name }}</h6>
                                            {{-- Safe check for deleted menu --}}
                                            <small class="text-muted">{{ $item->menu ? $item->menu->category->name : 'Item Removed' }}</small>
                                            
                                            @if(!empty($item->customizations))
                                                <div class="mt-1 p-2 bg-light rounded small text-secondary">
                                                    <strong>Note:</strong> {{ json_encode($item->customizations) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="fw-bold">₦{{ number_format($item->total_price) }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            @if($order->special_instructions)
                <div class="alert alert-warning border-0 shadow-sm">
                    <h6 class="fw-bold"><i class="fas fa-comment-alt me-2"></i>Customer Instructions</h6>
                    <p class="mb-0">{{ $order->special_instructions }}</p>
                </div>
            @endif
        </div>

        {{-- Right Column: Summary --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Customer Details</h5></div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $order->customer->name }}</h6>
                            <small class="text-muted">{{ $order->customer->phone }}</small>
                        </div>
                    </div>
                    <hr>
                    <h6 class="small text-uppercase text-muted fw-bold">Delivery Address</h6>
                    <p class="mb-0 small">
                        {{ $order->delivery_address['street'] ?? 'N/A' }}<br>
                        {{ $order->delivery_address['city'] ?? '' }}
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Payment Summary</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>₦{{ number_format($order->subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Service Fee</span>
                        <span>₦{{ number_format($order->service_fee) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Discount</span>
                        <span class="text-danger">-₦{{ number_format($order->discount_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-3 mb-4">
                        <span class="fw-bold h5 mb-0">Total</span>
                        <span class="fw-bold h5 mb-0 text-primary">₦{{ number_format($order->total_amount) }}</span>
                    </div>

                    <div class="bg-light p-3 rounded">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold">Payment Status</span>
                            <span class="badge bg-{{ $order->isPaid() ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Method</span>
                            <span class="small fw-bold text-uppercase">{{ $order->payment_method }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Status Update Script --}}
<script>
    function updateStatus(status, label) {
        if(!confirm(`Are you sure you want to change status to: ${label}?`)) return;

        fetch(`{{ route('chef.orders.update-status', $order) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('Something went wrong.'));
    }
</script>
@endsection