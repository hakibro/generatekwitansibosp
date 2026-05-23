<?php

namespace App\Support;

use Illuminate\Support\Collection;

class CodeReferences
{
    public static function all(): Collection
    {
        static $references = null;

        if ($references !== null) {
            return $references;
        }

        $settings = ReceiptSettings::get();
        $programItems = self::settingsMapToItems($settings['program_codes'] ?? [], 'program', 'Kode Program');
        $activityItems = self::settingsMapToItems($settings['activity_codes'] ?? [], 'activity', 'Kode Kegiatan');
        $accountItems = self::settingsMapToItems($settings['account_codes'] ?? [], 'account', 'Kode Rekening');

        return $references = $programItems
            ->concat($activityItems)
            ->concat($accountItems)
            ->values();
    }

    public static function findActivity(?string $code): ?array
    {
        $normalized = self::normalizeActivityCode($code);

        return self::all()->first(function ($item) use ($normalized) {
            return in_array($item['type'] ?? null, ['activity', 'sub_activity'], true)
                && self::normalizeActivityCode($item['reference_code']) === $normalized;
        });
    }

    public static function findProgramForActivity(?string $activityCode): ?array
    {
        $normalized = self::normalizeActivityCode($activityCode);
        if ($normalized === '') {
            return null;
        }

        $parts = explode('.', $normalized);
        $programCode = $parts[0];

        return self::all()->first(function ($item) use ($programCode) {
            return ($item['type'] ?? null) === 'program'
                && self::normalizeActivityCode($item['reference_code']) === $programCode;
        });
    }

    public static function findAccount(?string $accountCode, ?string $detailCode = null): ?array
    {
        $accountCode = trim((string) $accountCode);
        $detailCode = trim((string) $detailCode);
        $referenceCode = self::accountReferenceCode($accountCode, $detailCode);

        return self::all()->first(function ($item) use ($accountCode, $detailCode, $referenceCode) {
            if (($item['type'] ?? 'account') !== 'account') {
                return false;
            }

            return $item['reference_code'] === $referenceCode
                || trim((string) $item['bku_code']) === trim($accountCode.' '.$detailCode);
        });
    }

    public static function accountReferenceCode(?string $accountCode, ?string $detailCode = null): string
    {
        $accountCode = trim((string) $accountCode, " .\t\n\r\0\x0B");
        $detailCode = trim((string) $detailCode);
        if ($accountCode === '') {
            return '';
        }

        $detail = $detailCode !== '' ? str_pad($detailCode, 4, '0', STR_PAD_LEFT) : '';

        return $detail ? $accountCode.'.'.$detail : $accountCode;
    }

    private static function normalizeActivityCode(?string $code): string
    {
        return trim((string) $code, " .\t\n\r\0\x0B");
    }

    private static function settingsMapToItems(array $map, string $type, string $category): Collection
    {
        return collect($map)->map(function ($name, $code) use ($type, $category) {
            return [
                'type' => $type,
                'category' => $category,
                'bku_code' => (string) $code,
                'reference_code' => self::normalizeReferenceCode($type, (string) $code),
                'name' => (string) $name,
                'description' => (string) $name,
                'source' => 'Settings manual',
            ];
        })->values();
    }

    private static function normalizeReferenceCode(string $type, string $code): string
    {
        return match ($type) {
            'program', 'activity' => self::normalizeActivityCode($code),
            default => trim($code),
        };
    }
}
