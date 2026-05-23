<?php

namespace App\Http\Controllers;

use App\Support\ReceiptSettings;
use Illuminate\Http\Request;

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
            'template.fund_name' => ['required', 'string', 'max:255'],
            'template.general_checklist' => ['nullable', 'string'],
            'template.honor_checklist' => ['nullable', 'string'],
            'template.siplah_checklist' => ['nullable', 'string'],
            'template.note' => ['nullable', 'string', 'max:500'],
            'program_codes_text' => ['nullable', 'string'],
            'activity_codes_text' => ['nullable', 'string'],
            'account_codes_text' => ['nullable', 'string'],
        ]);

        $validated['program_codes'] = $this->linesToMap($validated['program_codes_text'] ?? '');
        $validated['activity_codes'] = $this->linesToMap($validated['activity_codes_text'] ?? '');
        $validated['account_codes'] = $this->linesToMap($validated['account_codes_text'] ?? '');
        unset($validated['program_codes_text'], $validated['activity_codes_text'], $validated['account_codes_text']);

        ReceiptSettings::save($validated);

        return redirect()->route('settings.edit')->with('status', 'Settings disimpan.');
    }

    private function linesToMap(?string $text): array
    {
        return collect(preg_split('/\r?\n/', (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->mapWithKeys(function ($line) {
                [$code, $name] = array_pad(preg_split('/\s*=\s*/', $line, 2), 2, '');

                return trim($code) !== '' ? [trim($code) => trim($name)] : [];
            })
            ->filter(fn ($name) => $name !== '')
            ->all();
    }
}
