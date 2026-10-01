<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class POSController extends Controller
{
    public function generateReceipt(Request $request)
    {
        try {
            $data = [
                'receiptNumber' => $request->transaction_number,
                'date' => now()->format('Y-m-d H:i:s'),
                'cashier' => auth()->user() ? auth()->user()->firstname . ' ' . auth()->user()->lastname : 'System User', 
                'items' => json_decode($request->items, true),
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'total' => $request->total_amount,
                'amountPaid' => $request->amount_paid ?? $request->total_amount,
                'change' => $request->change_amount ?? 0,
                'paymentType' => $request->payment_type,
                'chargeType' => $request->charge_type,
                'chargeTo' => $request->charge_to
            ];

            return view('components.pos.receipt-template', $data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

