<?php

namespace App\Http\Controllers;

use App\Support\ReceiptSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('settings.edit', [
            'settings' => ReceiptSettings::get(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school.school_name' => ['required', 'string', 'max:255'],
            'school.npsn' => ['nullable', 'string', 'max:50'],
            'school.district' => ['nullable', 'string', 'max:255'],
            'school.city' => ['nullable', 'string', 'max:255'],
            'school.province' => ['nullable', 'string', 'max:255'],
            'school.source' => ['nullable', 'string', 'max:255'],
            'sign.principal_name' => ['nullable', 'string', 'max:255'],
            'sign.principal_nip' => ['nullable', 'string', 'max:255'],
            'sign.treasurer_name' => ['nullable', 'string', 'max:255'],
            'sign.treasurer_nip' => ['nullable', 'string', 'max:255'],
            'sign.place' => ['nullable', 'string', 'max:255'],
            'sign.mode' => ['required', 'in:ttd_stempel,qr_code,none'],
            'sign.principal_sign' => ['nullable', 'string', 'max:255'],
            'sign.treasurer_sign' => ['nullable', 'string', 'max:255'],
            'sign.school_stamp' => ['nullable', 'string', 'max:255'],
            'sign.principal_qr' => ['nullable', 'string', 'max:255'],
            'sign.treasurer_qr' => ['nullable', 'string', 'max:255'],
            'template.fund_name' => ['required', 'string', 'max:255'],
            'template.general_checklist' => ['nullable', 'string'],
            'template.honor_checklist' => ['nullable', 'string'],
            'template.siplah_checklist' => ['nullable', 'string'],
            'template.note' => ['nullable', 'string', 'max:500'],
            'export.layout' => ['required', 'in:v1,v2'],
            'program_codes_text' => ['nullable', 'string'],
            'activity_codes_text' => ['nullable', 'string'],
            'account_codes_text' => ['nullable', 'string'],
            'sign_principal_sign_file' => ['nullable', 'image', 'max:2048'],
            'sign_treasurer_sign_file' => ['nullable', 'image', 'max:2048'],
            'sign_school_stamp_file' => ['nullable', 'image', 'max:2048'],
            'sign_principal_qr_file' => ['nullable', 'image', 'max:2048'],
            'sign_treasurer_qr_file' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle signature image uploads
        $current = ReceiptSettings::get();

        foreach ([
            'sign_principal_sign_file' => 'sign.principal_sign',
            'sign_treasurer_sign_file' => 'sign.treasurer_sign',
            'sign_school_stamp_file' => 'sign.school_stamp',
            'sign_principal_qr_file' => 'sign.principal_qr',
            'sign_treasurer_qr_file' => 'sign.treasurer_qr',
        ] as $fileKey => $settingKey) {
            if ($request->hasFile($fileKey)) {
                // Delete old file if exists
                $oldPath = $current['sign'][explode('.', $settingKey)[1]] ?? null;
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file($fileKey)->store('signatures', 'public');
                $validated['sign'][explode('.', $settingKey)[1]] = $path;
            }
        }

        $validated['program_codes'] = $this->linesToMap($validated['program_codes_text'] ?? '');
        $validated['activity_codes'] = $this->linesToMap($validated['activity_codes_text'] ?? '');
        $validated['account_codes'] = $this->linesToMap($validated['account_codes_text'] ?? '');
        unset($validated['program_codes_text'], $validated['activity_codes_text'], $validated['account_codes_text']);
        unset($validated['sign_principal_sign_file'], $validated['sign_treasurer_sign_file'], $validated['sign_school_stamp_file'], $validated['sign_principal_qr_file'], $validated['sign_treasurer_qr_file']);

        ReceiptSettings::save($validated);

        return redirect()->route('settings.edit')->with('status', 'Settings disimpan.');
    }

    private function linesToMap(?string $text): array
    {
        return collect(preg_split('/\r?\n/', (string) $text))
            ->map(fn($line) => trim($line))
            ->filter()
            ->mapWithKeys(function ($line) {
                [$code, $name] = array_pad(preg_split('/\s*=\s*/', $line, 2), 2, '');

                return trim($code) !== '' ? [trim($code) => trim($name)] : [];
            })
            ->filter(fn($name) => $name !== '')
            ->all();
    }
}
