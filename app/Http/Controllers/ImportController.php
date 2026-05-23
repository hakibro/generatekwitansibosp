<?php

namespace App\Http\Controllers;

use App\Models\ReceiptBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function create()
    {
        return view('imports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'source_file' => ['nullable', 'string', 'max:255'],
            'merge_mode' => ['required', 'in:by-proof,by-proof-only,none'],
            'stamp_limit' => ['required', 'integer', 'min:0'],
            'rows_json' => ['required', 'json'],
        ]);

        $rows = collect(json_decode($validated['rows_json'], true))
            ->filter(fn ($row) => filled($row['proof'] ?? null) && filled($row['description'] ?? null) && (int) ($row['amount'] ?? 0) > 0)
            ->values();

        abort_if($rows->isEmpty(), 422, 'Data transaksi dengan No. Bukti belum ada.');

        $batch = DB::transaction(function () use ($validated, $rows) {
            $batch = ReceiptBatch::query()->create([
                'month' => $validated['month'],
                'year' => $validated['year'],
                'source_file' => $validated['source_file'] ?? null,
                'start_proof' => $rows->first()['proof'],
                'number_mode' => 'preserve',
                'merge_mode' => 'by-proof-only',
                'stamp_limit' => $validated['stamp_limit'],
            ]);

            $rows->each(function (array $row) use ($batch) {
                $batch->items()->create([
                    'transaction_date' => $this->parseDate($row['date'] ?? null),
                    'proof_number' => $row['proof'] ?? null,
                    'activity_code' => $row['activity'] ?? null,
                    'account_code' => $row['account'] ?? null,
                    'detail_code' => $row['detail'] ?? null,
                    'description' => $row['description'],
                    'amount' => (int) ($row['amount'] ?? 0),
                    'warning' => $row['warning'] ?? null,
                ]);
            });

            return $batch;
        });

        return redirect()
            ->route('batches.edit', $batch)
            ->with('status', 'Data BKU disimpan. Silakan lengkapi nama penerima.');
    }

    private function parseDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d-m-Y', $date)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
