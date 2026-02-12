<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    public function add($id)
    {
        $menu = Menu::findOrFail($id);
        $cart = session()->get('cart', []);

        // If item exists, increment quantity
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            // If new, add to cart with quantity = 1
            $cart[$id] = [
                "name" => $menu->name,
                "quantity" => 1,          // <--- Dashboard looks for this!
                "price" => $menu->price,
                "image" => $menu->image
            ];
        }

        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Added to cart successfully!');
    }

    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Item removed successfully');
        }
    }
}