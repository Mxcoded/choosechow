<!DOCTYPE html>
<html>
<head><title>New Order</title></head>
<body style="font-family: Arial, sans-serif; background-color: #fff7ed; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px; border-top: 4px solid #ea580c;">
        <h2 style="color: #ea580c;">🔥 You have a new order!</h2>
        <p><strong>Order #{{ $order->order_number }}</strong> just came in.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            @foreach($order->items as $item)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;"><strong>{{ $item->quantity }}x</strong></td>
                    <td style="padding: 10px;">{{ $item->menu_name }}</td>
                </tr>
            @endforeach
        </table>

        <p style="background: #eee; padding: 10px;">
            <strong>Customer Note:</strong> {{ $order->notes ?? 'None' }}
        </p>

        <a href="{{ route('chef.orders.show', $order->id) }}" style="display: block; text-align: center; background: #ea580c; color: white; padding: 15px; text-decoration: none; font-weight: bold; border-radius: 4px;">Accept & Start Cooking</a>
    </div>
</body>
</html>