<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kuitansi v2 {{ $batch->month }}-{{ $batch->year }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #cfd7df;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 3;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            padding: 12px 16px;
            background: #102436;
            color: #fff;
        }

        .toolbar a,
        .toolbar button {
            border: 1px solid rgba(255, 255, 255, .24);
            background: #146c94;
            color: #fff;
            border-radius: 8px;
            padding: 9px 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .pages {
            display: grid;
            justify-content: center;
            gap: 14px;
            padding: 18px;
        }

        .receipt-page {
            width: 210mm;
            min-height: 297mm;
            padding: 8mm 9mm;
            background: #fff;
            font-size: 9pt;
            line-height: 1.35;
            page-break-after: always;
            box-shadow: 0 8px 24px rgba(16, 24, 40, .18);
        }

        .receipt-frame {
            border: 2px solid #111;
            padding: 6mm 8mm;
        }

        .meta-top {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }

        .meta-table {
            border-collapse: collapse;
            width: 105mm;
            font-size: 8.5pt;
        }

        .meta-table td {
            border: 1px solid #111;
            padding: 2px 5px;
            vertical-align: middle;
        }

        .meta-table td:first-child {
            width: 42mm;
            font-weight: 700;
        }

        .meta-table td:nth-child(2) {
            width: 4mm;
            text-align: center;
            font-weight: 700;
        }

        .meta-table td:last-child {
            text-align: center;
            font-weight: 700;
        }

        .receipt-title {
            text-align: center;
            margin-bottom: 12px;
        }

        .receipt-title h1 {
            margin: 0;
            font-size: 20pt;
            font-weight: 900;
            letter-spacing: 8px;
        }

        .kv {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .kv td {
            border: 0;
            padding: 4px 4px;
            font-size: 9pt;
            vertical-align: top;
        }

        .kv td:first-child {
            width: 52mm;
        }

        .kv td:nth-child(2) {
            width: 5mm;
            text-align: center;
        }

        .kv td:last-child {
            width: auto;
        }

        .boxed-value {
            border: 1px solid #111;
            padding: 5px 8px;
            font-weight: 600;
            display: inline-block;
            width: 100%;
        }

        .program-block {
            margin: 10px 0 14px 0;
        }

        .program-block .kv td:first-child {
            width: 52mm;
        }

        .program-block .kv .indent-content {
            padding-left: 57mm;
        }

        .amount-row {
            margin: 14px 0 18px 0;
        }

        .amount-row .boxed-value {
            font-weight: 400;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 18px;
            font-size: 10pt;
        }

        .signatures>div {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-height: 64mm;
        }

        .sig-label {
            margin-bottom: auto;
            line-height: 1.45;
        }

        .sig-name {
            font-weight: 900;
            margin-bottom: 3px;
        }

        .sig-nip {
            font-weight: 700;
        }

        .sig-space {
            min-height: 72px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 6px;
            position: relative;
        }

        .sig-img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
        }

        .stamp-img {
            max-height: 120px;
            max-width: 120px;
            object-fit: contain;
            position: absolute;
            opacity: 0.75;
        }

        .sig-row {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .sig-row .sig-img {
            position: relative;
            z-index: 2;
        }

        .sig-row .stamp-img {
            position: absolute;
            z-index: 1;
            left: -14px;
            bottom: -8px;
            transform: rotate(-12deg);
        }

        .attachment-separator {
            margin: 14px 0;
            border-top: 1px dashed #555;
        }

        .attachment {
            border: 1px solid #111;
            padding: 11px;
        }

        .attachment h2 {
            margin: 0 0 10px;
            text-align: center;
            font-size: 12.5pt;
        }

        .attachment .kv {
            border-bottom: 1px solid #d0d5dd;
            padding-bottom: 8px;
            margin-bottom: 9px;
        }

        .checklist {
            margin: 10px 0 0;
            padding: 0;
            list-style: none;
        }

        .checklist li {
            margin: 5px 0;
        }

        .box {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            margin-right: 7px;
            vertical-align: -1px;
        }

        .attachment-note {
            margin: 10px 0 0;
            padding-top: 8px;
            border-top: 1px solid #d0d5dd;
            color: #667085;
            font-size: 8.8pt;
            font-style: italic;
        }

        @media print {
            body {
                background: #fff;
            }

            .toolbar {
                display: none;
            }

            .pages {
                display: block;
                padding: 0;
            }

            .receipt-page {
                width: auto;
                min-height: auto;
                box-shadow: none;
                padding: 10mm 11mm;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <div>{{ $receipts->count() }} kuitansi v2 - Total Rp. {{ number_format($receipts->sum('amount'), 0, ',', '.') }}
        </div>
        <div>
            <a href="{{ route('batches.edit', $batch) }}">Kembali Edit</a>
            <a href="{{ route('batches.print', $batch) }}">Versi Lama</a>
            <button onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
    </div>

    <main class="pages">
        @foreach ($receipts as $receipt)
            <section class="receipt-page">
                <div class="receipt-frame">
                    <div class="meta-top">
                        <table class="meta-table">
                            <tr>
                                <td>NO.BKU/HAL</td>
                                <td>:</td>
                                <td>{{ $receipt['proof'] }}</td>
                            </tr>
                            <tr>
                                <td>NO.PROGRAM/ KEG</td>
                                <td>:</td>
                                <td>{{ $receipt['activity_code'] }}</td>
                            </tr>
                            <tr>
                                <td>KODE REKENING</td>
                                <td>:</td>
                                <td>{{ $receipt['account_reference_code'] ?: $receipt['account_key'] }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="receipt-title">
                        <h1>KUITANSI</h1>
                    </div>

                    <table class="kv">
                        <tr>
                            <td>Terima dari</td>
                            <td>:</td>
                            <td>Bendahara BOSP {{ $settings['school']['school_name'] }}</td>
                        </tr>
                        <tr>
                            <td>Banyaknya Uang</td>
                            <td>:</td>
                            <td><span class="boxed-value">{{ number_format($receipt['amount'], 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Untuk Pembayaran</td>
                            <td>:</td>
                            <td>{{ $receipt['description'] }}</td>
                        </tr>
                    </table>

                    <div class="program-block">
                        <table class="kv">
                            <tr>
                                <td colspan="3" class="indent-content">tanggal {{ $receipt['date_text'] }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="indent-content">Program / Kegiatan :
                                    {{ $receipt['activity_code'] }} @if ($receipt['activity_name'])
                                        - {{ $receipt['activity_name'] }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="indent-content">Kode Rekening :
                                    {{ $receipt['account_reference_code'] ?: $receipt['account_key'] }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="indent-content">Nomor Rekening Penerima :</td>
                            </tr>
                        </table>
                    </div>

                    <div class="amount-row">
                        <table class="kv">
                            <tr>
                                <td>Jumlah</td>
                                <td>:</td>
                                <td><span class="boxed-value">Rp. &nbsp;&nbsp; {{ $receipt['terbilang'] }}</span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="signatures">
                        <div>
                            <div class="sig-label">Setuju dibayar<br>Kepala Sekolah
                                {{ $settings['school']['school_name'] }}</div>
                            <div class="sig-space">
                                @if (($settings['sign']['mode'] ?? 'ttd_stempel') === 'none')
                                    {{-- tanpa tanda tangan --}}
                                @elseif (($settings['sign']['mode'] ?? 'ttd_stempel') === 'ttd_stempel' && !empty($settings['sign']['principal_sign']))
                                    <div class="sig-row">
                                        <img src="{{ asset('storage/' . $settings['sign']['principal_sign']) }}"
                                            class="sig-img" alt="TTD Kepsek">
                                        @if (!empty($settings['sign']['school_stamp']))
                                            <img src="{{ asset('storage/' . $settings['sign']['school_stamp']) }}"
                                                class="stamp-img" alt="Stempel">
                                        @endif
                                    </div>
                                @elseif (($settings['sign']['mode'] ?? 'ttd_stempel') === 'qr_code' && !empty($settings['sign']['principal_qr']))
                                    <img src="{{ asset('storage/' . $settings['sign']['principal_qr']) }}"
                                        class="sig-img" alt="QR Kepsek">
                                @endif
                            </div>
                            <div class="sig-name">{{ $settings['sign']['principal_name'] }}</div>
                            <div class="sig-nip">NIP. {{ $settings['sign']['principal_nip'] }}</div>
                        </div>
                        <div>
                            <div class="sig-label">Lunas dibayar<br>Tanggal : {{ $receipt['date_text'] }}<br>Bendahara
                                Pengeluaran</div>
                            <div class="sig-space">
                                @if (($settings['sign']['mode'] ?? 'ttd_stempel') === 'none')
                                    {{-- tanpa tanda tangan --}}
                                @elseif (($settings['sign']['mode'] ?? 'ttd_stempel') === 'ttd_stempel' && !empty($settings['sign']['treasurer_sign']))
                                    <img src="{{ asset('storage/' . $settings['sign']['treasurer_sign']) }}"
                                        class="sig-img" alt="TTD Bendahara">
                                @elseif (($settings['sign']['mode'] ?? 'ttd_stempel') === 'qr_code' && !empty($settings['sign']['treasurer_qr']))
                                    <img src="{{ asset('storage/' . $settings['sign']['treasurer_qr']) }}"
                                        class="sig-img" alt="QR Bendahara">
                                @endif
                            </div>
                            <div class="sig-name">{{ $settings['sign']['treasurer_name'] }}</div>
                            <div class="sig-nip">NIP. {{ $settings['sign']['treasurer_nip'] }}</div>
                        </div>
                        <div>
                            <div class="sig-label">{{ $settings['sign']['place'] }},
                                {{ $receipt['date_text'] }}<br>Yang menerima</div>
                            <div class="sig-name">{{ $receipt['receiver_name'] }}</div>
                            <div class="sig-nip">NIP. -</div>
                        </div>
                    </div>
                </div>

                @if ($receipt['show_attachment'])
                    <div class="attachment-separator"></div>
                    <div class="attachment">
                        <h2>LEMBAR KELENGKAPAN LAMPIRAN SPJ</h2>
                        <table class="kv">
                            <tr>
                                <td>No Bukti</td>
                                <td>:</td>
                                <td>{{ $receipt['proof'] }}</td>
                            </tr>
                            <tr>
                                <td>Uraian</td>
                                <td>:</td>
                                <td>{{ $receipt['description'] }}</td>
                            </tr>
                        </table>
                        <b>Checklist Dokumen Pendukung :</b>
                        <ul class="checklist">
                            @foreach ($receipt['checklist'] as $item)
                                <li><span class="box"></span>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <p class="attachment-note"><b>Catatan :</b> {{ $settings['template']['note'] }}</p>
                    </div>
                @endif
            </section>
        @endforeach
    </main>
</body>

</html>
