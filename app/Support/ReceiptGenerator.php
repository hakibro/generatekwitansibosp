<?php

namespace App\Support;

use App\Models\ReceiptBatch;
use App\Models\ReceiptItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ReceiptGenerator
{
    public function build(ReceiptBatch $batch, array $settings): Collection
    {
        $groups = $batch->items
            ->filter(fn(ReceiptItem $item) => filled($item->description) && $item->amount > 0)
            ->groupBy(fn(ReceiptItem $item, int $index) => $this->groupKey($batch, $item, $index));

        return $groups->values()->map(function (Collection $items, int $index) use ($batch, $settings) {
            $first = $items->first();
            $proof = $batch->number_mode === 'renumber'
                ? $this->nextProof($batch->start_proof, $index)
                : ($first->proof_number ?: $this->nextProof($batch->start_proof, $index));

            $amount = (int) $items->sum('amount');
            $descriptions = $items->pluck('description')->filter()->unique()->values();
            $accountKey = trim($first->account_code . ' ' . $first->detail_code);
            $program = CodeReferences::findProgramForActivity($first->activity_code);
            $activity = CodeReferences::findActivity($first->activity_code);
            $account = CodeReferences::findAccount($first->account_code, $first->detail_code);
            $activityName = $activity['name'] ?? '';
            $accountName = $account['name'] ?? '';
            $description = $descriptions->implode(' | ');
            $receiver = $items->pluck('receiver_name')->first(fn($name) => filled($name)) ?: '-';

            return [
                'key' => $this->groupKey($batch, $first, $index),
                'proof' => $proof,
                'source_proof' => $first->proof_number,
                'date' => $first->transaction_date,
                'date_text' => $this->formatDate($first->transaction_date),
                'program_code' => $program['reference_code'] ?? Str::before((string) $first->activity_code, '.'),
                'program_name' => $program['name'] ?? '',
                'activity_code' => $first->activity_code,
                'activity_name' => $activityName,
                'account_code' => $first->account_code,
                'detail_code' => $first->detail_code,
                'account_key' => $accountKey,
                'account_reference_code' => $account['reference_code'] ?? CodeReferences::accountReferenceCode($first->account_code, $first->detail_code),
                'account_name' => $accountName,
                'description' => $description,
                'items_count' => $items->count(),
                'amount' => $amount,
                'terbilang' => $this->terbilang($amount) . ' Rupiah',
                'receiver_name' => $receiver,
                'use_stamp' => $amount >= $batch->stamp_limit,
                'show_attachment' => $items->contains(fn($i) => $i->show_attachment),
                'checklist' => $this->checklist($settings, $first, $description, $activityName, $accountName),
            ];
        });
    }

    private function groupKey(ReceiptBatch $batch, ReceiptItem $item, int $index): string
    {
        return match ($batch->merge_mode) {
            'none' => 'row-' . $item->id . '-' . $index,
            'by-proof-only' => (string) $item->proof_number,
            default => implode('|', [
                $item->proof_number,
                $item->activity_code,
                $item->account_code,
                $item->detail_code,
            ]),
        };
    }

    private function nextProof(string $start, int $offset): string
    {
        if (!preg_match('/^([A-Za-z]*)(\d+)$/', $start, $match)) {
            return $start . ($offset + 1);
        }

        return Str::upper($match[1]) . str_pad((string) ((int) $match[2] + $offset), strlen($match[2]), '0', STR_PAD_LEFT);
    }

    private function checklist(array $settings, ReceiptItem $item, string $description, string $activityName, string $accountName): array
    {
        $text = Str::lower($item->detail_code . ' ' . $description . ' ' . $activityName . ' ' . $accountName);
        $template = $settings['template']['general_checklist'];

        if (preg_match('/honor|tenaga|pendidik|13|29|30|31/', $text)) {
            $template = $settings['template']['honor_checklist'];
        } elseif (preg_match('/siplah|atk|bahan habis|alat tulis|24/', $text)) {
            $template = $settings['template']['siplah_checklist'];
        }

        return collect(preg_split('/\r?\n/', $template))->map(fn($line) => trim($line))->filter()->values()->all();
    }

    private function formatDate($date): string
    {
        if (!$date) {
            return '';
        }

        return $date->day . ' ' . (ReceiptSettings::MONTHS[$date->month] ?? $date->format('F')) . ' ' . $date->year;
    }

    private function terbilang(int $number): string
    {
        $number = abs($number);
        $units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($number < 12) {
            return $units[$number] ?: 'Nol';
        }
        if ($number < 20) {
            return $this->terbilang($number - 10) . ' Belas';
        }
        if ($number < 100) {
            return $this->cleanNumberText($this->terbilang(intdiv($number, 10)) . ' Puluh ' . $this->terbilang($number % 10));
        }
        if ($number < 200) {
            return $this->cleanNumberText('Seratus ' . $this->terbilang($number - 100));
        }
        if ($number < 1000) {
            return $this->cleanNumberText($this->terbilang(intdiv($number, 100)) . ' Ratus ' . $this->terbilang($number % 100));
        }
        if ($number < 2000) {
            return $this->cleanNumberText('Seribu ' . $this->terbilang($number - 1000));
        }
        if ($number < 1000000) {
            return $this->cleanNumberText($this->terbilang(intdiv($number, 1000)) . ' Ribu ' . $this->terbilang($number % 1000));
        }
        if ($number < 1000000000) {
            return $this->cleanNumberText($this->terbilang(intdiv($number, 1000000)) . ' Juta ' . $this->terbilang($number % 1000000));
        }

        return $this->cleanNumberText($this->terbilang(intdiv($number, 1000000000)) . ' Miliar ' . $this->terbilang($number % 1000000000));
    }

    private function cleanNumberText(string $text): string
    {
        return trim(preg_replace('/\s*Nol\b/', '', $text));
    }
}
