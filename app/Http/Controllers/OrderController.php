<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Order $order): View
    {
        return view('orders.show', [
            'order' => $order->load('items'),
        ]);
    }
}
