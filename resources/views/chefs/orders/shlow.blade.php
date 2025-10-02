@extends('layouts.dashboard')

@section('title', 'Order Details #' . $order->order_number)

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title">Order #{{ $order->order_number }} Details </h1>
                <p class="dashboard-subtitle">Status: 
                    <span class="badge fs-6 bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
                    @if($order->payment_status !== 'paid')
                        <span class="badge bg-danger ms-2">{{ ucfirst($order->payment_status) }}</span>
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('chef.orders', ['status' => 'pending']) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Update Status</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Move the order to the next stage after completing a step.</p>
                    
                    <div class="d-flex gap-2 justify-content-start">
                        @php
                            $nextStatuses = match($order->status) {
                                'pending' => ['confirmed' => 'Confirm Order'],
                                'confirmed' => ['preparing' => 'Start Preparation'],
                                'preparing' => ['ready' => 'Mark as Ready'],
                                'ready' => ['out_for_delivery' => 'Out for Delivery'],
                                'out_for_delivery' => ['delivered' => 'Mark as Delivered'],
                                default => []
                            };
                        @endphp

                        @foreach($nextStatuses as $key => $label)
                            <button type="button" class="btn btn-{{ $key === 'delivered' ? 'success' : 'primary' }}" 
                                    onclick="updateOrderStatus('{{ $key }}', '{{ $label }}')">
                                <i class="fas fa-sync-alt me-2"></i>{{ $label }}
                            </button>
                        @endforeach

                        @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                            <button type="button" class="btn btn-outline-danger" onclick="showCancelModal()">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Items ({{ $order->items->count() }})</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($order->items as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $item->menu_name }} (x{{ $item->quantity }})</h6>
                                    <small class="text-muted">{{ $item->menu->cuisine_types[0] ?? '' }}</small>
                                    @if($item->customizations)
                                        <p class="small text-warning mb-0 mt-1">
                                            <i class="fas fa-tools me-1"></i>Customized
                                        </p>
                                    @endif
                                </div>
                                <span class="fw-bold">₦{{ number_format($item->total_price, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($order->special_instructions)
                <div class="dashboard-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Special Instructions</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->special_instructions }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Financial Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled fw-medium">
                        <li class="d-flex justify-content-between mb-1">
                            <span>Subtotal:</span>
                            <span>₦{{ number_format($order->subtotal, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between mb-1">
                            <span>Delivery Fee:</span>
                            <span>₦{{ number_format($order->delivery_fee, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between mb-1">
                            <span>Service Fee:</span>
                            <span>₦{{ number_format($order->service_fee, 2) }}</span>
                        </li>
                        @if($order->discount_amount > 0)
                        <li class="d-flex justify-content-between mb-1 text-danger">
                            <span>Discount:</span>
                            <span>-₦{{ number_format($order->discount_amount, 2) }}</span>
                        </li>
                        @endif
                        <li class="d-flex justify-content-between fw-bold pt-2 border-top mt-2">
                            <span>Total Payable:</span>
                            <span>₦{{ number_format($order->total_amount, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between pt-2 border-top mt-2">
                            <span>Your Net Earning:</span>
                            <span class="text-success">₦{{ number_format($order->getChefEarnings(), 2) }}</span>
                        </li>
                    </ul>
                    <p class="small mt-3 mb-0">
                        Payment: <strong>{{ ucfirst($order->payment_method) }}</strong>, 
                        Status: <span class="badge bg-{{ $order->isPaid() ? 'success' : 'danger' }}">{{ ucfirst($order->payment_status) }}</span>
                    </p>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Customer & Delivery</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Customer:</strong> {{ $order->customer->name }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Address:</strong></p>
                    <p class="small text-muted ps-2">
                        {{ $order->delivery_address['street'] ?? 'N/A' }}, 
                        {{ $order->delivery_address['city'] ?? 'N/A' }}, 
                        {{ $order->delivery_address['state'] ?? 'N/A' }}
                    </p>
                    @if($order->estimated_delivery_time)
                        <p class="mt-3 mb-0">
                            <i class="fas fa-clock me-1"></i>Est. Delivery: {{ $order->estimated_delivery_time->format('h:i A, D') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel">Confirm Status Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statusUpdateModalBody">
                Are you sure you want to change the order status to <strong id="newStatusLabel"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Confirm</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Global variable to hold the new status key
    let targetStatusKey = '';

    function updateOrderStatus(newStatus, label) {
        targetStatusKey = newStatus;
        document.getElementById('newStatusLabel').textContent = label;
        const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
        modal.show();
    }

    document.getElementById('confirmStatusChange').addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal'));
        modal.hide();

        fetch(`{{ route('chef.orders.update-status', $order) }}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                status: targetStatusKey
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload to see the new status and next available actions
            } else {
                alert('Update failed: ' + (data.message || 'Unknown error.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred during update.');
        });
    });

    function showCancelModal() {
        // A placeholder for a future modal that collects a cancellation reason
        alert('Cancellation logic needs a dedicated modal to collect the reason.');
    }
</script>
@endsection