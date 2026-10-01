
public function getDetails($id)
{
    $transaction = Transaction::with(['items', 'processedBy', 'approvedBy'])
        ->where('id', $id)
        ->first();

    if (!$transaction) {
        return response()->json(['error' => 'Transaction not found'], 404);
    }

    $items = $transaction->items->map(function ($item) {
        return [
            'product_name' => $item->product_name ?? $item->menu_name ?? 'Unknown Item',
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'total_price' => $item->quantity * $item->unit_price
        ];
    });

return response()->json([
        'transaction_number' => $transaction->transaction_number,
        'created_at' => $transaction->created_at,
        'processed_by' => optional($transaction->processedBy)->name,
        'charge_to' => $transaction->charge_to,
        'charge_type' => $transaction->charge_type,
        'status' => $transaction->status,
        'approved_by' => $transaction->approved_by ? optional($transaction->approvedBy)->name : null,
        'approved_at' => $transaction->approved_at,
        'declined_by' => $transaction->declined_by ? optional($transaction->declinedBy)->name : null,
        'declined_at' => $transaction->declined_at,
        'decline_reason' => $transaction->decline_reason,
        'total_amount' => $transaction->total_amount,
        'items' => $items
    ]);
}
