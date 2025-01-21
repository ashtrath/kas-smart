<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaction = Transaction::with(['user', 'paymentMethod', 'items.product'])->get();

        return $this->sendResponse('Transactions retrieved successfully.', $transaction);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        $transaction = Transaction::create([
            'author_id' => $validated['author_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'total_amount' => collect($validated['items'])->sum(fn($item) => $item['quantity'] * $item['price']),
        ]);
        $transaction->items()->createMany($validated['items']);

        return $this->sendResponse('Transaction created successfully.', $transaction->load('items.product'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        if (!$transaction) {
            return $this->sendError('Transaction not found.');
        }

        return $this->sendResponse('Transaction retrieved successfully.', $transaction->load(['items.product']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        if (!$transaction) {
            return $this->sendError('Transaction not found.');
        }

        $validated = $request->validated();

        $transaction->update([
            'payment_method_id' => $validated['payment_method_id'] ?? $transaction->payment_method_id,
        ]);

        if (isset($validated['items'])) {
            $transaction->items()->delete();
            $transaction->items()->createMany($validated['items']);
        }

        return $this->sendResponse('Transaction updated successfully.', $transaction->load('items.product'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        if (!$transaction) {
            return $this->sendError('Transaction not found.');
        }

        $transaction->delete();

        return $this->sendResponse('Transaction deleted successfully.', null, 204);
    }
}
