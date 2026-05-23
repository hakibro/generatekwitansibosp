@extends('layouts.app')

@section('title', 'Beranda Utama - SIBUK BOS')
@section('page_title', 'Beranda Utama')
@section('page_hint', 'Sistem Generate Kuitansi BOS')

@section('content')
    @php
        $hour = now()->hour;
        $greeting =
            $hour < 11
                ? 'Selamat Pagi'
                : ($hour < 15
                    ? 'Selamat Siang'
                    : ($hour < 18
                        ? 'Selamat Sore'
                        : 'Selamat Malam'));
    @endphp

    <section class="rounded-[32px] border border-slate-700/70 bg-[#111c2f] px-7 py-8 shadow-2xl shadow-slate-950/20">
        <div class="flex flex-col gap-7">
            <div>
                <h2 class="text-3xl font-black tracking-tight text-white md:text-4xl">{{ $greeting }}, Pengguna!</h2>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <form method="get" action="{{ route('dashboard') }}" id="dashboardFilter"
                        class="flex flex-wrap items-center gap-3">
                        <select name="year"
                            class="h-11 w-[152px] rounded-2xl border-slate-700 bg-[#172238] px-5 text-sm font-black text-blue-400">
                            @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                                <option value="{{ $optionYear }}" @selected($year === $optionYear)>Tahun {{ $optionYear }}
                                </option>
                            @endfor
                        </select>
                        <select name="month"
                            class="h-11 w-[174px] rounded-2xl border-slate-700 bg-[#172238] px-5 text-sm font-black text-blue-400">
                            <option value="0">Semua Bulan</option>
                            @foreach ($months as $value => $label)
                                <option value="{{ $value }}" @selected($month === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn primary" type="submit"><i class="fa-solid fa-filter"></i>Terapkan</button>
                    </form>
                    <span
                        class="inline-flex h-11 items-center gap-3 rounded-2xl border border-slate-700 bg-[#172238] px-5 text-sm font-black text-slate-300">
                        <i class="fa-solid fa-bolt text-yellow-400"></i>
                        Nomor Bukti Terakhir :
                        <b class="text-blue-400">{{ $latestProof ?: '-' }}</b>
                    </span>
                    <span
                        class="inline-flex h-11 items-center gap-3 rounded-2xl border border-slate-700 bg-[#172238] px-5 text-sm font-black text-slate-300">
                        <i class="fa-solid fa-chart-simple text-emerald-400"></i>
                        Progres Penerima :
                        <b class="rounded-lg bg-emerald-500/15 px-2 py-1 text-emerald-300">{{ $completionPercent }}%</b>
                    </span>
                </div>
            </div>

            <div class="relative pt-3">
                <div class="absolute left-8 right-8 top-[31px] h-[2px] bg-slate-700"></div>
                <div class="relative grid grid-cols-6 gap-5 lg:grid-cols-12">
                    @foreach ($monthStats as $stat)
                        @php
                            $isSelected = $month === $stat['month'];
                            $isFilled = $stat['receipts'] > 0;
                        @endphp
                        <a href="{{ route('dashboard', ['year' => $year, 'month' => $stat['month']]) }}"
                            class="group grid justify-items-center gap-3">
                            <span @class([
                                'grid h-7 w-7 place-items-center rounded-full border-2 text-xs font-black transition',
                                'border-white bg-white text-blue-600 shadow-lg shadow-white/20' => $isSelected,
                                'border-blue-400 bg-blue-500 text-white' => !$isSelected && $isFilled,
                                'border-slate-500 bg-[#1d2a3d] text-slate-500 group-hover:border-blue-300' =>
                                    !$isSelected && !$isFilled,
                            ])>
                                @if ($isSelected)
                                    <i class="fa-solid fa-check text-[11px]"></i>
                                @elseif ($isFilled)
                                    {{ $stat['receipts'] }}
                                @endif
                            </span>
                            <span @class([
                                'text-sm font-black',
                                'text-white' => $isSelected,
                                'text-blue-300' => !$isSelected && $isFilled,
                                'text-slate-400' => !$isSelected && !$isFilled,
                            ])>{{ $stat['short'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8 grid gap-8 xl:grid-cols-[minmax(0,.95fr)_minmax(0,1.35fr)]">
        <div>
            <h2 class="mb-5 text-2xl font-black text-white">Menu Utama</h2>
            <div class="grid gap-5">
                <a href="{{ route('imports.create') }}"
                    class="group flex items-center gap-4 rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5 transition hover:border-blue-400 hover:bg-[#20304a]">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-blue-500/20 text-xl text-blue-400"><i
                            class="fa-solid fa-upload"></i></span>
                    <span class="min-w-0 flex-1">
                        <b class="block text-xl font-black text-white">Upload BKU</b>
                        <span class="mt-1 block text-sm font-bold text-slate-400">Unggah BKU dari Arkas untuk diekstrak
                            otomatis.</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-2xl text-slate-600 transition group-hover:text-blue-300"></i>
                </a>
                <a href="{{ $editTargetBatch ? route('batches.edit', $editTargetBatch) : route('imports.create') }}"
                    class="group flex items-center gap-4 rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5 transition hover:border-indigo-400 hover:bg-[#20304a]">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-indigo-500/20 text-xl text-indigo-300"><i
                            class="fa-solid fa-pen-to-square"></i></span>
                    <span class="min-w-0 flex-1">
                        <b class="block text-xl font-black text-white">Sesuaikan Kuitansi</b>
                        <span class="mt-1 block text-sm font-bold text-slate-400">Validasi data, ubah uraian, dan isi
                            penerima.</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-2xl text-slate-600 transition group-hover:text-indigo-300"></i>
                </a>
                <div class="rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5">
                    <div class="flex items-center gap-4">
                        <span class="grid h-14 w-14 place-items-center rounded-2xl bg-rose-500/20 text-xl text-rose-300"><i
                                class="fa-solid fa-clipboard-check"></i></span>
                        <span>
                            <b class="block text-xl font-black text-white">{{ number_format($totalUnfilledReceivers, 0, ',', '.') }}
                                Penerima Kosong</b>
                            <span class="mt-1 block text-sm font-bold text-slate-400">Lengkapi sebelum pusat cetak dibuat
                                final.</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-5 text-2xl font-black text-white">Ringkasan {{ $selectedMonthName }}</h2>
            <div class="grid gap-5 md:grid-cols-2">
                <div class="rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5">
                    <span
                        class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-500/20 text-lg text-emerald-300"><i
                            class="fa-solid fa-file-invoice"></i></span>
                    <b
                        class="mt-4 block text-2xl font-black text-white">{{ number_format($totalReceipts, 0, ',', '.') }}</b>
                    <span class="text-sm font-bold text-slate-400">Kuitansi siap cetak</span>
                </div>
                <div class="rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-500/20 text-lg text-blue-300"><i
                            class="fa-solid fa-list-check"></i></span>
                    <b class="mt-4 block text-2xl font-black text-white">{{ number_format($totalItems, 0, ',', '.') }}</b>
                    <span class="text-sm font-bold text-slate-400">Baris transaksi terbaca</span>
                </div>
                <div class="rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-500/20 text-lg text-orange-300"><i
                            class="fa-solid fa-folder-open"></i></span>
                    <b
                        class="mt-4 block text-2xl font-black text-white">{{ number_format($totalBatches, 0, ',', '.') }}</b>
                    <span class="text-sm font-bold text-slate-400">Batch pada filter ini</span>
                </div>
                <div class="rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-rose-500/20 text-lg text-rose-300"><i
                            class="fa-solid fa-rupiah-sign"></i></span>
                    <b class="mt-4 block text-xl font-black text-white">Rp.
                        {{ number_format($totalAmount, 0, ',', '.') }}</b>
                    <span class="text-sm font-bold text-slate-400">Total pengeluaran</span>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-black text-white">Daftar Batch</h2>
            <span
                class="rounded-full border border-slate-700 bg-[#172238] px-4 py-2 text-sm font-black text-slate-300">{{ $selectedMonthName }}
                {{ $year }}</span>
        </div>
        @if ($summaries->isEmpty())
            <div class="empty">Belum ada batch pada filter ini. Mulai dari menu Upload BKU.</div>
        @else
            <div class="grid gap-4">
                @foreach ($summaries as $summary)
                    <article class="rounded-3xl border border-slate-700 bg-[#1d2a3d] p-5">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <b class="text-xl font-black text-white">{{ $months[$summary['batch']->month] }}
                                        {{ $summary['batch']->year }}</b>
                                    <span @class([
                                        'badge',
                                        'warn' => $summary['unfilled_receivers'] > 0,
                                        'good' => $summary['unfilled_receivers'] === 0,
                                    ])>
                                        {{ $summary['unfilled_receivers'] }} penerima kosong
                                    </span>
                                </div>
                                <p class="mt-2 truncate text-sm font-bold text-slate-400">
                                    {{ $summary['batch']->source_file ?: 'Tanpa nama file' }}</p>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[470px]">
                                <div class="rounded-2xl bg-[#172238] px-4 py-3">
                                    <span class="text-xs font-black uppercase text-slate-500">Transaksi</span>
                                    <b
                                        class="block text-lg font-black text-white">{{ number_format($summary['items'], 0, ',', '.') }}</b>
                                </div>
                                <div class="rounded-2xl bg-[#172238] px-4 py-3">
                                    <span class="text-xs font-black uppercase text-slate-500">Kuitansi</span>
                                    <b
                                        class="block text-lg font-black text-white">{{ number_format($summary['receipts'], 0, ',', '.') }}</b>
                                </div>
                                <div class="rounded-2xl bg-[#172238] px-4 py-3">
                                    <span class="text-xs font-black uppercase text-slate-500">Total</span>
                                    <b class="block text-lg font-black text-white">Rp.
                                        {{ number_format($summary['total'], 0, ',', '.') }}</b>
                                </div>
                            </div>
                            <div class="actions">
                                <a class="btn" href="{{ route('batches.edit', $summary['batch']) }}"><i
                                        class="fa-solid fa-pen"></i>Edit</a>
                                <a class="btn primary" href="{{ route('batches.print', $summary['batch']) }}"
                                    target="_blank"><i class="fa-solid fa-print"></i>Cetak</a>
                                <form method="post" action="{{ route('batches.destroy', $summary['batch']) }}"
                                    onsubmit="return confirm('Hapus batch {{ $months[$summary['batch']->month] }} {{ $summary['batch']->year }}?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        const filterForm = document.getElementById('dashboardFilter');
        let filterTimer = null;

        filterForm.querySelectorAll('select').forEach((field) => {
            field.addEventListener('change', () => {
                clearTimeout(filterTimer);
                filterTimer = setTimeout(() => filterForm.requestSubmit(), 100);
            });
        });
    </script>
@endpush
