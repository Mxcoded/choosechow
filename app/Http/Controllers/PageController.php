<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the home/landing page.
     */
    public function home()
    {
        return view('welcome');
    }

    /**
     * Display the how it works page.
     */
    public function howItWorks()
    {
        return view('pages.how-it-works');
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {


        return view('pages.contact');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Display the terms of service page.
     */
    public function terms()
    {
        return view('pages.terms');
    }
}
