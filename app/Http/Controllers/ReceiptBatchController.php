<?php

namespace App\Http\Controllers;

use App\Models\ReceiptBatch;
use App\Support\ReceiptGenerator;
use App\Support\ReceiptSettings;
use Illuminate\Http\Request;

class ReceiptBatchController extends Controller
{
    public function edit(ReceiptBatch $batch, ReceiptGenerator $generator)
    {
        $batch->load('items');
        $settings = ReceiptSettings::get();
        $receipts = $generator->build($batch, $settings);

        return view('batches.edit', [
            'batch' => $batch,
            'receipts' => $receipts,
            'months' => ReceiptSettings::MONTHS,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, ReceiptBatch $batch)
    {
        $validated = $request->validate([
            'merge_mode' => ['required', 'in:by-proof,by-proof-only,none'],
            'stamp_limit' => ['required', 'integer', 'min:0'],
            'show_attachment' => ['nullable', 'boolean'],
            'receivers' => ['array'],
            'receivers.*' => ['nullable', 'string', 'max:255'],
            'items' => ['array'],
            'items.*.transaction_date' => ['nullable', 'date'],
            'items.*.show_attachment' => ['nullable', 'boolean'],
            'items.*.proof_number' => ['required', 'string', 'max:50'],
            'items.*.activity_code' => ['nullable', 'string', 'max:50'],
            'items.*.account_code' => ['nullable', 'string', 'max:50'],
            'items.*.detail_code' => ['nullable', 'string', 'max:50'],
            'items.*.description' => ['required', 'string'],
            'items.*.amount' => ['required', 'integer', 'min:0'],
            'items.*.receiver_name' => ['nullable', 'string', 'max:255'],
            'deleted_items' => ['array'],
            'deleted_items.*' => ['integer'],
        ]);

        $batch->update([
            'start_proof' => $batch->start_proof,
            'number_mode' => 'preserve',
            'merge_mode' => $validated['merge_mode'],
            'stamp_limit' => $validated['stamp_limit'],
            'show_attachment' => $validated['show_attachment'] ?? false,
        ]);

        $deletedItems = collect($validated['deleted_items'] ?? [])->map(fn($id) => (int) $id)->all();

        if ($deletedItems) {
            $batch->items()->whereIn('id', $deletedItems)->delete();
        }

        foreach ($validated['items'] ?? [] as $itemId => $payload) {
            if (in_array((int) $itemId, $deletedItems, true)) {
                continue;
            }

            $item = $batch->items()->whereKey($itemId)->first();
            if (!$item) {
                continue;
            }

            $receiverName = $validated['receivers'][$payload['proof_number']]
                ?? $validated['receivers'][$item->proof_number]
                ?? null;

            $item->update([
                'transaction_date' => $payload['transaction_date'] ?? null,
                'proof_number' => $payload['proof_number'],
                'activity_code' => $payload['activity_code'] ?? null,
                'account_code' => $payload['account_code'] ?? null,
                'detail_code' => $payload['detail_code'] ?? null,
                'description' => $payload['description'],
                'amount' => (int) $payload['amount'],
                'receiver_name' => $receiverName ?: null,
                'show_attachment' => $payload['show_attachment'] ?? false,
            ]);
        }

        foreach ($validated['receivers'] ?? [] as $proofNumber => $receiverName) {
            $batch->items()
                ->where('proof_number', $proofNumber)
                ->update(['receiver_name' => $receiverName ?: null]);
        }

        return redirect()
            ->route('batches.edit', $batch)
            ->with('status', 'Perubahan disimpan.');
    }

    public function print(ReceiptBatch $batch, ReceiptGenerator $generator)
    {
        $batch->load('items');

        return view('batches.print', [
            'batch' => $batch,
            'receipts' => $generator->build($batch, ReceiptSettings::get()),
            'settings' => ReceiptSettings::get(),
        ]);
    }

    public function printV2(ReceiptBatch $batch, ReceiptGenerator $generator)
    {
        $batch->load('items');

        return view('batches.print-v2', [
            'batch' => $batch,
            'receipts' => $generator->build($batch, ReceiptSettings::get()),
            'settings' => ReceiptSettings::get(),
        ]);
    }

    public function destroy(ReceiptBatch $batch)
    {
        $batch->delete();

        return redirect()->route('dashboard')->with('status', 'Batch kuitansi dihapus.');
    }
}
