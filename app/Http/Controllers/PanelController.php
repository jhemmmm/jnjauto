<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        return view('panel.dashboard');
    }

    /**
     * Show the appointments management page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function appointments()
    {
        return view('panel.appointments');
    }

    /**
     * Show the services & prices management page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function services()
    {
        return view('panel.services');
    }

    /**
     * Show the sales report page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function salesReport()
    {
        return view('panel.sales');
    }

    /**
     * Show the inventory management page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function inventory()
    {
        return view('panel.inventory');
    }

    /**
     * Show the notifications page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function notifications()
    {
        return view('panel.notifications');
    }

    /**
     * Show the settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function settings()
    {
        return view('panel.settings');
    }

    /**
     * Show the users management page (admin only).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users()
    {
        return view('panel.users');
    }
}
