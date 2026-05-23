<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kuitansi {{ $batch->month }}-{{ $batch->year }}</title>
    <style>
        body { margin:0; background:#cfd7df; color:#000; font-family:Arial, Helvetica, sans-serif; }
        .toolbar { position:sticky; top:0; z-index:3; display:flex; justify-content:space-between; gap:10px; align-items:center; padding:12px 16px; background:#102436; color:#fff; }
        .toolbar a,.toolbar button { border:1px solid rgba(255,255,255,.24); background:#146c94; color:#fff; border-radius:8px; padding:9px 12px; font-weight:700; text-decoration:none; cursor:pointer; }
        .pages { display:grid; justify-content:center; gap:14px; padding:18px; }
        .receipt-page { width:210mm; min-height:297mm; padding:12mm 13mm; background:#fff; font-size:10pt; line-height:1.28; page-break-after:always; box-shadow:0 8px 24px rgba(16,24,40,.18); }
        .receipt-frame { border:2px solid #111; padding:10mm 10mm 8mm; }
        .receipt-title { display:flex; justify-content:space-between; align-items:center; gap:18px; margin-bottom:9px; border-bottom:2px solid #111; padding-bottom:7px; }
        .receipt-title h1 { margin:0; font-size:18pt; font-weight:900; letter-spacing:1px; }
        .receipt-title b { border:1px solid #111; padding:6px 10px; min-width:46mm; text-align:center; }
        .subtitle { font-weight:700; margin-bottom:10px; padding:7px 9px; border:1px solid #111; background:#f6f6f6; }
        .kv { width:100%; margin-bottom:9px; border-collapse:collapse; }
        .kv td { border:0; padding:2.8px 4px; font-size:9.7pt; vertical-align:top; }
        .kv td:first-child { width:38mm; font-weight:700; }
        .kv td:nth-child(2) { width:5mm; text-align:center; }
        .money { font-weight:800; }
        .spell { font-style:italic; }
        .amount-lines { width:96mm; margin:12px 0 15px auto; }
        .amount-line { display:grid; grid-template-columns:1fr auto; gap:12px; border-bottom:1px dashed #111; padding:5px 0 4px; }
        .amount-line:first-child { display:block; font-weight:700; }
        .amount-line.total { font-weight:800; }
        .signatures { display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; text-align:center; margin-top:16px; }
        .signatures > div { border-top:1px solid #111; padding-top:8px; }
        .sig-space { height:58px; display:flex; align-items:center; justify-content:center; color:#555; }
        .sig-name { font-weight:700; text-decoration:underline; }
        .materai { border:1px solid #777; padding:9px; width:72px; margin:0 auto; font-size:9pt; }
        .spj-separator { margin:14px 0; border-top:1px dashed #555; }
        .attachment { border:1px solid #111; padding:11px; }
        .attachment h2 { margin:0 0 10px; text-align:center; font-size:12.5pt; }
        .attachment .kv { border-bottom:1px solid #d0d5dd; padding-bottom:8px; margin-bottom:9px; }
        .checklist { margin:10px 0 0; padding:0; list-style:none; }
        .checklist li { margin:5px 0; }
        .box { display:inline-block; width:11px; height:11px; border:1px solid #000; margin-right:7px; vertical-align:-1px; }
        .attachment-note { margin:10px 0 0; padding-top:8px; border-top:1px solid #d0d5dd; color:#667085; font-size:8.8pt; font-style:italic; }
        @media print {
            body { background:#fff; }
            .toolbar { display:none; }
            .pages { display:block; padding:0; }
            .receipt-page { width:auto; min-height:auto; box-shadow:none; padding:10mm 11mm; }
            @page { size:A4 portrait; margin:0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>{{ $receipts->count() }} kuitansi - Total Rp. {{ number_format($receipts->sum('amount'), 0, ',', '.') }}</div>
        <div>
            <a href="{{ route('batches.edit', $batch) }}">Kembali Edit</a>
            <button onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
    </div>

    <main class="pages">
        @foreach ($receipts as $receipt)
            <section class="receipt-page">
                <div class="receipt-frame">
                    <div class="receipt-title">
                        <h1>KUITANSI</h1>
                        <b>No. Bukti : {{ $receipt['proof'] }}</b>
                    </div>
                    <div class="subtitle">
                        {{ $settings['template']['fund_name'] }}
                        <span style="float:right">Tahun Anggaran : {{ $batch->year }}</span>
                    </div>
                    <table class="kv">
                        <tr><td>Kode Program</td><td>:</td><td>{{ $receipt['program_code'] }} @if($receipt['program_name']) - {{ $receipt['program_name'] }} @endif</td></tr>
                        <tr><td>Kode Kegiatan</td><td>:</td><td>{{ $receipt['activity_code'] }} @if($receipt['activity_name']) - {{ $receipt['activity_name'] }} @endif</td></tr>
                        <tr><td>Kode Rekening</td><td>:</td><td>{{ $receipt['account_key'] }} @if($receipt['account_name']) - {{ $receipt['account_name'] }} @endif</td></tr>
                        <tr><td>Sudah Terima Dari</td><td>:</td><td>Kepala {{ $settings['school']['school_name'] }} Kecamatan {{ $settings['school']['district'] }}</td></tr>
                        <tr><td>Jumlah Uang</td><td>:</td><td class="money">Rp. {{ number_format($receipt['amount'], 0, ',', '.') }} ,-</td></tr>
                        <tr><td>Terbilang</td><td>:</td><td class="spell">{{ $receipt['terbilang'] }}</td></tr>
                        <tr><td>Untuk Pembayaran</td><td>:</td><td>{{ $receipt['description'] }}</td></tr>
                    </table>
                    <div class="amount-lines">
                        <div class="amount-line"><span>Perincian Penerimaan</span></div>
                        <div class="amount-line"><span>Penerimaan</span><b>Rp. {{ number_format($receipt['amount'], 0, ',', '.') }} ,-</b></div>
                        <div class="amount-line total"><span>Jumlah Diterimakan</span><b>Rp. {{ number_format($receipt['amount'], 0, ',', '.') }} ,-</b></div>
                    </div>
                    <div class="signatures">
                        <div>
                            <div>Mengetahui,<br>Kepala Sekolah</div>
                            <div class="sig-space"></div>
                            <div class="sig-name">{{ $settings['sign']['principal_name'] }}</div>
                            <div>NIP. {{ $settings['sign']['principal_nip'] }}</div>
                        </div>
                        <div>
                            <div>Lunas Dibayar Oleh,<br>Bendahara BOS</div>
                            <div class="sig-space"></div>
                            <div class="sig-name">{{ $settings['sign']['treasurer_name'] }}</div>
                            <div>NIP. {{ $settings['sign']['treasurer_nip'] }}</div>
                        </div>
                        <div>
                            <div>{{ $settings['sign']['place'] }}, {{ $receipt['date_text'] }}<br>Penerima</div>
                            <div class="sig-space">
                                @if ($receipt['use_stamp'])
                                    <div class="materai">Materai<br>Rp 10.000</div>
                                @endif
                            </div>
                            <div class="sig-name">{{ $receipt['receiver_name'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="spj-separator"></div>
                <div class="attachment">
                    <h2>LEMBAR KELENGKAPAN LAMPIRAN SPJ</h2>
                    <table class="kv">
                        <tr><td>No Bukti</td><td>:</td><td>{{ $receipt['proof'] }}</td></tr>
                        <tr><td>Uraian</td><td>:</td><td>{{ $receipt['description'] }}</td></tr>
                    </table>
                    <b>Checklist Dokumen Pendukung :</b>
                    <ul class="checklist">
                        @foreach ($receipt['checklist'] as $item)
                            <li><span class="box"></span>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <p class="attachment-note"><b>Catatan :</b> {{ $settings['template']['note'] }}</p>
                </div>
            </section>
        @endforeach
    </main>
</body>
</html>
