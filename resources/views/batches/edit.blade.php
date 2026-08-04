@extends('layouts.app')

@section('title', 'Edit Penerima - Generator Kuitansi BOSP')
@section('page_title', 'Edit Data Kuitansi')
@section('page_hint', 'Lengkapi nama penerima dan koreksi transaksi hasil import sebelum cetak.')

@section('actions')
    @php $printRoute = ($settings['export']['layout'] ?? 'v1') === 'v2' ? route('batches.print-v2', $batch) : route('batches.print', $batch); @endphp
    <a class="btn primary" href="{{ $printRoute }}" target="_blank"><i class="fa-solid fa-print"></i>Cetak</a>
    <form method="post" action="{{ route('batches.destroy', $batch) }}" onsubmit="return confirm('Hapus batch ini?')">
        @csrf
        @method('delete')
        <button class="btn danger" type="submit"><i class="fa-solid fa-trash"></i>Hapus</button>
    </form>
@endsection

@section('content')
    @php
        $transactionGroups = $batch->items->groupBy('proof_number')->map(
            fn($items, $proof) => [
                'proof' => $proof,
                'items' => $items->values(),
                'count' => $items->count(),
                'total' => $items->sum('amount'),
                'receiver' => $items->pluck('receiver_name')->first(fn($name) => filled($name)),
            ],
        );
    @endphp

    <form method="post" action="{{ route('batches.update', $batch) }}" class="grid">
        @csrf
        @method('put')

        <div class="panel">
            <div class="panel-head">
                <h2>Opsi Batch: {{ $months[$batch->month] }} {{ $batch->year }}</h2>
                <span class="badge">{{ $batch->source_file ?: 'Tanpa nama file' }}</span>
            </div>
            <div class="panel-body form-grid">
                <div class="field">
                    <label>Gabung Item</label>
                    <select name="merge_mode">
                        <option value="by-proof" @selected($batch->merge_mode === 'by-proof')>No bukti + kegiatan + rekening</option>
                        <option value="by-proof-only" @selected($batch->merge_mode === 'by-proof-only')>No bukti saja</option>
                        <option value="none" @selected($batch->merge_mode === 'none')>Satu baris satu kuitansi</option>
                    </select>
                </div>
                <div class="field">
                    <label>Batas Materai</label>
                    <input type="number" name="stamp_limit" value="{{ old('stamp_limit', $batch->stamp_limit) }}" required>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Edit Penerima dan Transaksi</h2>
                <div class="actions">
                    <span class="badge">{{ $transactionGroups->count() }} no bukti</span>
                    <span class="badge">{{ $batch->items->count() }} baris</span>
                    <button class="btn good" type="submit"><i class="fa-solid fa-floppy-disk"></i>Simpan Perubahan</button>
                </div>
            </div>
            <div class="panel-body">
                <div id="deletedItems"></div>
                <div class="table-wrap">
                    <table style="min-width:1180px">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No Bukti</th>
                                <th>Penerima</th>
                                <th>Kegiatan</th>
                                <th>Rekening</th>
                                <th>Rincian</th>
                                <th>Uraian</th>
                                <th>Pengeluaran</th>
                                <th></th>
                                <th title="Tampilkan lembar kelengkapan lampiran SPJ"><i class="fa-solid fa-paperclip"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactionGroups as $group)
                                @foreach ($group['items'] as $item)
                                    <tr data-item-row="{{ $item->id }}">
                                        <td style="min-width:118px;width:118px">
                                            <input type="date" name="items[{{ $item->id }}][transaction_date]"
                                                value="{{ old('items.' . $item->id . '.transaction_date', optional($item->transaction_date)->format('Y-m-d')) }}">
                                        </td>
                                        <td style="min-width:104px;width:104px">
                                            <input name="items[{{ $item->id }}][proof_number]"
                                                value="{{ old('items.' . $item->id . '.proof_number', $item->proof_number) }}"
                                                required>
                                        </td>
                                        @if ($loop->first)
                                            <td rowspan="{{ $group['count'] }}"
                                                style="min-width:220px;width:220px;background:#172238">
                                                <div class="grid" style="gap:8px">
                                                    <span class="badge">{{ $group['proof'] }} / {{ $group['count'] }}
                                                        baris</span>
                                                    <span class="text-xs font-bold text-slate-400">Rp.
                                                        {{ number_format($group['total'], 0, ',', '.') }}</span>
                                                    <input name="receivers[{{ $group['proof'] }}]"
                                                        value="{{ old('receivers.' . $group['proof'], $group['receiver']) }}"
                                                        placeholder="Nama penerima">
                                                </div>
                                            </td>
                                        @endif
                                        <td style="min-width:104px;width:104px">
                                            <input name="items[{{ $item->id }}][activity_code]"
                                                value="{{ old('items.' . $item->id . '.activity_code', $item->activity_code) }}">
                                        </td>
                                        <td style="min-width:142px;width:142px">
                                            <input name="items[{{ $item->id }}][account_code]"
                                                value="{{ old('items.' . $item->id . '.account_code', $item->account_code) }}">
                                        </td>
                                        <td style="min-width:72px;width:72px">
                                            <input name="items[{{ $item->id }}][detail_code]"
                                                value="{{ old('items.' . $item->id . '.detail_code', $item->detail_code) }}">
                                        </td>
                                        <td style="min-width:250px">
                                            <textarea name="items[{{ $item->id }}][description]" style="min-height:42px">{{ old('items.' . $item->id . '.description', $item->description) }}</textarea>
                                        </td>
                                        <td style="min-width:116px;width:116px">
                                            <input type="number" name="items[{{ $item->id }}][amount]"
                                                value="{{ old('items.' . $item->id . '.amount', $item->amount) }}"
                                                required>
                                        </td>
                                        <td style="min-width:94px;width:94px">
                                            <button class="btn danger" type="button"
                                                data-delete-item="{{ $item->id }}"><i
                                                    class="fa-solid fa-trash"></i>Hapus</button>
                                        </td>
                                        <td style="min-width:42px;width:42px;text-align:center">
                                            <label title="Lampiran SPJ">
                                                <input type="checkbox" name="items[{{ $item->id }}][show_attachment]"
                                                    value="1" @checked(old('items.' . $item->id . '.show_attachment', $item->show_attachment))>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-delete-item]').forEach((button) => {
            button.addEventListener('click', () => {
                const itemId = button.dataset.deleteItem;
                if (!confirm('Hapus baris transaksi ini?')) return;

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_items[]';
                input.value = itemId;
                document.getElementById('deletedItems').appendChild(input);
                document.querySelector(`[data-item-row="${itemId}"]`).remove();
            });
        });
    </script>
@endpush
