<?php

namespace App\Http\Controllers;

use App\Models\ReceiptBatch;
use App\Support\ReceiptGenerator;
use App\Support\ReceiptSettings;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ReceiptGenerator $generator)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $settings = ReceiptSettings::get();

        $yearBatches = ReceiptBatch::query()
            ->with('items')
            ->where('year', $year)
            ->get();

        $batches = ReceiptBatch::query()
            ->with('items')
            ->where('year', $year)
            ->when($month, fn ($query) => $query->where('month', $month))
            ->latest()
            ->get();

        $summarize = function (ReceiptBatch $batch) use ($generator, $settings) {
            $receipts = $generator->build($batch, $settings);

            return [
                'batch' => $batch,
                'items' => $batch->items->count(),
                'receipts' => $receipts->count(),
                'total' => $receipts->sum('amount'),
                'unfilled_receivers' => $receipts->filter(fn ($receipt) => $receipt['receiver_name'] === '-')->count(),
            ];
        };

        $summaries = $batches->map($summarize);
        $yearSummaries = $yearBatches->map($summarize);
        $totalYearReceipts = $yearSummaries->sum('receipts');
        $totalYearUnfilled = $yearSummaries->sum('unfilled_receivers');
        $completionPercent = $totalYearReceipts > 0
            ? (int) round((($totalYearReceipts - $totalYearUnfilled) / $totalYearReceipts) * 100)
            : 0;
        $latestProof = optional($yearBatches->flatMap->items->sortByDesc('id')->first())->proof_number;
        $monthStats = collect(ReceiptSettings::MONTHS)->map(function ($label, $value) use ($yearSummaries) {
            $items = $yearSummaries->filter(fn ($summary) => $summary['batch']->month === $value);

            return [
                'month' => $value,
                'label' => $label,
                'short' => substr($label, 0, 3),
                'batches' => $items->count(),
                'receipts' => $items->sum('receipts'),
                'total' => $items->sum('total'),
            ];
        });

        return view('dashboard', [
            'months' => ReceiptSettings::MONTHS,
            'year' => $year,
            'month' => $month,
            'summaries' => $summaries,
            'totalReceipts' => $summaries->sum('receipts'),
            'totalItems' => $summaries->sum('items'),
            'totalAmount' => $summaries->sum('total'),
            'totalBatches' => $summaries->count(),
            'totalUnfilledReceivers' => $summaries->sum('unfilled_receivers'),
            'latestProof' => $latestProof,
            'completionPercent' => $completionPercent,
            'monthStats' => $monthStats,
            'selectedMonthName' => $month ? ReceiptSettings::MONTHS[$month] : 'Semua Bulan',
            'editTargetBatch' => $summaries->first()['batch'] ?? null,
        ]);
    }
}
