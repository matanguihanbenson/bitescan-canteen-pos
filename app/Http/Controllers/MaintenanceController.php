<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenance = Maintenance::first();
        return view('maintenance', compact('maintenance'));
    }

    public function toggleMaintenance(Request $request)
    {
       $maintenance = new Maintenance();
       $maintenance = Maintenance::first() ?? $maintenance;

       $maintenance->is_maintenance = $request->input('is_maintenance');
       $maintenance->admin_notes = $request->input('admin_notes');
       $maintenance->save();

       session()->flash('status', 'Maintenance mode updated successfully.');

       return redirect()->back();
    }
}
