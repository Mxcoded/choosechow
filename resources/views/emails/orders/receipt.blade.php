<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px;">
        <h2 style="color: #dc2626; text-align: center;">Order Confirmed! 🥘</h2>
        <p>Hi {{ $order->user->first_name }},</p>
        <p>Your order <strong>#{{ $order->order_number }}</strong> has been received and sent to the kitchen.</p>
        
        <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Order Summary</h3>
            <ul style="padding-left: 20px;">
                @foreach($order->items as $item)
                    <li>{{ $item->quantity }}x {{ $item->menu_name }} - ₦{{ number_format($item->price * $item->quantity) }}</li>
                @endforeach
            </ul>
            <hr style="border: 0; border-top: 1px solid #eee;">
            <p style="text-align: right; font-size: 18px; font-weight: bold;">Total: ₦{{ number_format($order->total_amount) }}</p>
        </div>

        <p>You can track your order status in your dashboard.</p>
        <a href="{{ route('customer.orders') }}" style="display: block; width: 100%; text-align: center; background: #dc2626; color: white; padding: 12px; text-decoration: none; border-radius: 4px; font-weight: bold;">Track Order</a>
    </div>
</body>
</html>