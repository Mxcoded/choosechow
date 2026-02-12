<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        // For now, return a placeholder view
        return view('subscriptions.index');
    }

    public function plans()
    {
        return view('subscriptions.plans');
    }
}