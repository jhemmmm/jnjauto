<?php

namespace App\Http\Controllers;

use App\Models\Service;

class HomeController extends Controller
{
    /**
     * Display the public home page.
     */
    public function index()
    {
        $services = Service::orderBy('price')->get();

        return view('welcome', compact('services'));
    }
}
