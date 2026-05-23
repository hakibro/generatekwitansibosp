<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generator Kuitansi BOSP</title>
    <meta name="description" content="Aplikasi lokal untuk mengekstrak PDF BKU ARKAS dan membuat kuitansi BOSP massal.">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <style>
        :root {
            --ink: #162033;
            --muted: #667085;
            --line: #d8dee8;
            --soft: #f4f7fb;
            --panel: #ffffff;
            --brand: #146c94;
            --brand-strong: #0b4f6f;
            --good: #168a5b;
            --warn: #b25e09;
            --bad: #b42318;
            --shadow: 0 12px 28px rgba(16, 24, 40, .08);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; color: var(--ink); background: #eef3f8; font-family: Arial, Helvetica, sans-serif; }
        body { overflow-x: hidden; }
        button, input, select, textarea { font: inherit; }
        button { cursor: pointer; }
        .app { min-height: 100vh; display: grid; grid-template-columns: 264px minmax(0, 1fr); }
        .sidebar { background: #102436; color: #eef6fc; padding: 18px; position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; gap: 18px; }
        .brand { display: flex; align-items: center; gap: 12px; padding: 8px 6px 16px; border-bottom: 1px solid rgba(255,255,255,.14); }
        .brand-mark { width: 42px; height: 42px; border-radius: 8px; background: #2cb1d1; display: grid; place-items: center; box-shadow: inset 0 -10px 18px rgba(0,0,0,.12); }
        .brand h1 { margin: 0; font-size: 17px; line-height: 1.1; letter-spacing: 0; }
        .brand p { margin: 3px 0 0; color: #a9c3d3; font-size: 12px; }
        .nav { display: grid; gap: 8px; }
        .nav button { color: #cfe0eb; background: transparent; border: 1px solid transparent; border-radius: 8px; padding: 11px 12px; display: flex; align-items: center; gap: 10px; text-align: left; }
        .nav button:hover, .nav button.active { background: #18364f; border-color: rgba(255,255,255,.12); color: #fff; }
        .side-note { margin-top: auto; padding: 12px; border: 1px solid rgba(255,255,255,.12); border-radius: 8px; color: #cfe0eb; font-size: 12px; line-height: 1.45; }
        .main { min-width: 0; padding: 22px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
        .topbar h2 { margin: 0; font-size: 24px; letter-spacing: 0; }
        .topbar p { margin: 4px 0 0; color: var(--muted); font-size: 13px; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { border: 1px solid var(--line); background: var(--panel); color: var(--ink); border-radius: 8px; min-height: 38px; padding: 8px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 700; font-size: 13px; }
        .btn.primary { background: var(--brand); color: #fff; border-color: var(--brand); }
        .btn.primary:hover { background: var(--brand-strong); }
        .btn.good { background: var(--good); color: #fff; border-color: var(--good); }
        .btn.ghost { background: transparent; }
        .btn.danger { color: var(--bad); }
        .btn.icon { width: 38px; padding: 0; }
        .grid { display: grid; gap: 14px; }
        .grid.two { grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr); align-items: start; }
        .grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .panel { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; box-shadow: var(--shadow); }
        .panel-head { padding: 14px 16px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .panel-head h3 { margin: 0; font-size: 15px; }
        .panel-body { padding: 16px; }
        .drop { border: 2px dashed #a9bac9; background: #f8fbfd; border-radius: 8px; padding: 22px; display: grid; justify-items: center; text-align: center; gap: 10px; min-height: 174px; }
        .drop.drag { border-color: var(--brand); background: #e8f6fb; }
        .drop input { display: none; }
        .drop .big { width: 54px; height: 54px; border-radius: 8px; background: #d9edf5; display: grid; place-items: center; color: var(--brand); }
        .drop strong { font-size: 16px; }
        .drop span { color: var(--muted); font-size: 13px; max-width: 520px; line-height: 1.45; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
        .stat { border: 1px solid var(--line); border-radius: 8px; padding: 12px; background: #fbfdff; }
        .stat b { display: block; font-size: 20px; }
        .stat span { color: var(--muted); font-size: 12px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .field { display: grid; gap: 6px; }
        .field.full { grid-column: 1 / -1; }
        label { font-size: 12px; color: #344054; font-weight: 700; }
        input, select, textarea { width: 100%; border: 1px solid var(--line); border-radius: 8px; padding: 9px 10px; background: #fff; color: var(--ink); outline: none; }
        textarea { resize: vertical; min-height: 78px; }
        input:focus, select:focus, textarea:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(20, 108, 148, .14); }
        .tabs { display: flex; gap: 8px; margin-bottom: 14px; overflow-x: auto; }
        .tabs button { border: 1px solid var(--line); background: #fff; border-radius: 8px; padding: 9px 12px; font-weight: 700; color: var(--muted); white-space: nowrap; }
        .tabs button.active { color: #fff; background: var(--brand); border-color: var(--brand); }
        .view { display: none; }
        .view.active { display: block; }
        .table-wrap { overflow: auto; border: 1px solid var(--line); border-radius: 8px; background: #fff; max-height: 56vh; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid var(--line); padding: 8px; font-size: 12px; vertical-align: top; }
        th { position: sticky; top: 0; background: #edf3f8; z-index: 1; text-align: left; color: #344054; }
        td input, td textarea, td select { border-color: #edf0f4; border-radius: 6px; padding: 6px 7px; font-size: 12px; min-width: 90px; }
        td textarea { min-width: 260px; min-height: 38px; }
        .row-tools { display: flex; gap: 6px; }
        .badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 4px 8px; font-size: 12px; font-weight: 700; background: #edf3f8; color: #344054; }
        .badge.good { background: #e8f7ef; color: #067647; }
        .badge.warn { background: #fff4e5; color: #b25e09; }
        .list { display: grid; gap: 8px; }
        .receipt-card { border: 1px solid var(--line); border-radius: 8px; background: #fff; padding: 12px; display: grid; gap: 8px; }
        .receipt-card header { display: flex; justify-content: space-between; gap: 10px; }
        .receipt-card h4 { margin: 0; font-size: 14px; }
        .receipt-card p { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.45; }
        .preview-shell { background: #cfd7df; border-radius: 8px; padding: 14px; overflow: auto; max-height: 70vh; }
        .empty { border: 1px dashed var(--line); border-radius: 8px; padding: 20px; color: var(--muted); background: #fbfdff; text-align: center; font-size: 13px; }
        .toast { position: fixed; right: 18px; bottom: 18px; background: #102436; color: #fff; padding: 11px 14px; border-radius: 8px; box-shadow: var(--shadow); z-index: 50; opacity: 0; transform: translateY(10px); transition: .2s; font-size: 13px; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .progress { width: 100%; height: 8px; border-radius: 999px; background: #e7edf3; overflow: hidden; }
        .progress span { display: block; height: 100%; background: var(--brand); width: 0; transition: width .2s; }

        .receipt-page { width: 210mm; min-height: 297mm; padding: 13mm 14mm; background: #fff; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 11.5pt; line-height: 1.35; page-break-after: always; }
        .receipt-title { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; margin-bottom: 12px; }
        .receipt-title h1 { margin: 0; font-size: 17pt; text-decoration: underline; letter-spacing: 0; }
        .receipt-title b { font-size: 11.5pt; }
        .receipt-subtitle { font-weight: 700; margin-bottom: 14px; }
        .kv { width: 100%; margin-bottom: 10px; }
        .kv td { border: 0; padding: 2.5px 3px; font-size: 10.5pt; }
        .kv td:first-child { width: 38mm; }
        .kv td:nth-child(2) { width: 5mm; text-align: center; }
        .amount-box { display: grid; gap: 4px; margin: 10px 0 16px 48mm; width: 76mm; }
        .amount-row { display: grid; grid-template-columns: 1fr auto; gap: 10px; border-bottom: 1px solid #000; padding-bottom: 3px; }
        .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; text-align: center; margin-top: 18px; }
        .sig-space { height: 62px; display: flex; align-items: center; justify-content: center; color: #555; }
        .sig-name { font-weight: 700; text-decoration: underline; }
        .attachment { margin-top: 24px; border-top: 1px solid #000; padding-top: 12px; }
        .attachment h2 { margin: 0 0 12px; text-align: center; font-size: 13pt; }
        .checklist { margin: 10px 0 0 0; padding: 0; list-style: none; }
        .checklist li { margin: 5px 0; }
        .box { display: inline-block; width: 11px; height: 11px; border: 1px solid #000; margin-right: 7px; vertical-align: -1px; }
        .materai { border: 1px solid #777; padding: 9px; width: 72px; margin: 0 auto; font-size: 9pt; }
        #printArea { display: none; }

        @media (max-width: 980px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { height: auto; position: relative; }
            .nav { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .side-note { display: none; }
            .grid.two, .grid.three, .form-grid, .stats { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
        }

        @media print {
            body { background: #fff; }
            .app, .toast { display: none !important; }
            #printArea { display: block; }
            .receipt-page { box-shadow: none; margin: 0; width: auto; min-height: auto; padding: 11mm 12mm; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark" data-lucide="receipt-text"></div>
            <div>
                <h1>SIBUK BOS Lokal</h1>
                <p>Generator kuitansi BOSP</p>
            </div>
        </div>
        <nav class="nav" aria-label="Navigasi utama">
            <button class="active" data-nav="workspace"><i data-lucide="file-input"></i><span>Generate</span></button>
            <button data-nav="data"><i data-lucide="table-2"></i><span>Data BKU</span></button>
            <button data-nav="preview"><i data-lucide="printer"></i><span>Preview</span></button>
            <button data-nav="settings"><i data-lucide="settings-2"></i><span>Settings</span></button>
        </nav>
        <div class="side-note">
            Ekstraksi berjalan di browser. Setelah PDF dibaca, cek tabel BKU sebelum mencetak supaya baris yang bergeser dari ARKAS bisa dibetulkan cepat.
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <h2 id="pageTitle">Generate Kuitansi</h2>
                <p id="pageHint">Upload PDF BKU, gabungkan transaksi berdasarkan nomor bukti, lalu cetak kuitansi dan lampiran SPJ.</p>
            </div>
            <div class="actions">
                <button class="btn" id="saveJsonBtn"><i data-lucide="save"></i>Simpan JSON</button>
                <button class="btn" id="loadJsonBtn"><i data-lucide="folder-open"></i>Buka JSON</button>
                <input type="file" id="jsonInput" accept="application/json" hidden>
                <button class="btn primary" id="printBtn"><i data-lucide="printer"></i>Cetak</button>
                <button class="btn good" id="pdfBtn"><i data-lucide="download"></i>PDF</button>
            </div>
        </div>

        <section class="view active" id="workspaceView">
            <div class="grid two">
                <div class="grid">
                    <div class="panel">
                        <div class="panel-head">
                            <h3>1. Upload PDF BKU</h3>
                            <span class="badge" id="fileBadge">Belum ada file</span>
                        </div>
                        <div class="panel-body">
                            <label class="drop" id="dropZone">
                                <input type="file" id="pdfInput" accept="application/pdf">
                                <span class="big"><i data-lucide="upload-cloud"></i></span>
                                <strong>Klik atau seret PDF BKU ke sini</strong>
                                <span>Gunakan file BKU dari ARKAS. Aplikasi akan membaca teks PDF, mengambil baris transaksi, lalu mengelompokkan item per kuitansi.</span>
                            </label>
                            <div style="margin-top:14px" class="progress" aria-hidden="true"><span id="progressBar"></span></div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-head">
                            <h3>2. Opsi Generate</h3>
                            <button class="btn" id="demoBtn"><i data-lucide="sparkles"></i>Isi Demo</button>
                        </div>
                        <div class="panel-body form-grid">
                            <div class="field">
                                <label>Bulan Aktif</label>
                                <select id="monthSelect"></select>
                            </div>
                            <div class="field">
                                <label>Tahun Anggaran</label>
                                <input id="yearInput" type="number" value="2025">
                            </div>
                            <div class="field">
                                <label>Nomor Bukti Awal</label>
                                <input id="startNoInput" value="BPU100">
                            </div>
                            <div class="field">
                                <label>Mode Nomor Bukti</label>
                                <select id="numberMode">
                                    <option value="renumber">Renomor berurutan dari nomor awal</option>
                                    <option value="preserve">Pertahankan nomor dari BKU</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Gabung item</label>
                                <select id="mergeMode">
                                    <option value="by-proof">Nomor bukti + kode kegiatan + rekening</option>
                                    <option value="by-proof-only">Nomor bukti saja</option>
                                    <option value="none">Satu baris satu kuitansi</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Materai jika nominal minimal</label>
                                <input id="stampLimit" type="number" value="5000000">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid">
                    <div class="stats">
                        <div class="stat"><b id="statRows">0</b><span>Baris transaksi</span></div>
                        <div class="stat"><b id="statReceipts">0</b><span>Kuitansi</span></div>
                        <div class="stat"><b id="statTotal">Rp. 0</b><span>Total pengeluaran</span></div>
                        <div class="stat"><b id="statWarnings">0</b><span>Perlu cek</span></div>
                    </div>
                    <div class="panel">
                        <div class="panel-head">
                            <h3>Hasil Penggabungan</h3>
                            <button class="btn" data-nav-jump="preview"><i data-lucide="eye"></i>Preview</button>
                        </div>
                        <div class="panel-body">
                            <div id="receiptList" class="list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="view" id="dataView">
            <div class="panel">
                <div class="panel-head">
                    <h3>Tabel Data BKU</h3>
                    <div class="actions">
                        <button class="btn" id="addRowBtn"><i data-lucide="plus"></i>Tambah</button>
                        <button class="btn" id="regroupBtn"><i data-lucide="refresh-cw"></i>Generate Ulang</button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No Bukti</th>
                                    <th>Kegiatan</th>
                                    <th>Rekening</th>
                                    <th>Rincian</th>
                                    <th>Uraian</th>
                                    <th>Pengeluaran</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="rowsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="view" id="previewView">
            <div class="panel">
                <div class="panel-head">
                    <h3>Preview Cetak</h3>
                    <div class="actions">
                        <button class="btn" id="refreshPreviewBtn"><i data-lucide="refresh-cw"></i>Refresh</button>
                        <button class="btn primary" id="printBtn2"><i data-lucide="printer"></i>Cetak</button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="preview-shell" id="previewShell"></div>
                </div>
            </div>
        </section>

        <section class="view" id="settingsView">
            <div class="tabs" role="tablist">
                <button class="active" data-tab="school">Sekolah</button>
                <button data-tab="sign">Pejabat</button>
                <button data-tab="codes">Master Kode</button>
                <button data-tab="template">Template</button>
            </div>

            <div class="panel tab-panel" id="schoolTab">
                <div class="panel-head"><h3>Identitas Sekolah</h3></div>
                <div class="panel-body form-grid" id="schoolForm"></div>
            </div>

            <div class="panel tab-panel" id="signTab" hidden>
                <div class="panel-head"><h3>Penandatangan</h3></div>
                <div class="panel-body form-grid" id="signForm"></div>
            </div>

            <div class="panel tab-panel" id="codesTab" hidden>
                <div class="panel-head">
                    <h3>Master Kode Kegiatan dan Rekening</h3>
                    <button class="btn" id="resetCodesBtn"><i data-lucide="rotate-ccw"></i>Reset Master</button>
                </div>
                <div class="panel-body form-grid">
                    <div class="field full">
                        <label>Master kode kegiatan (satu baris: kode = nama)</label>
                        <textarea id="activityCodesInput"></textarea>
                    </div>
                    <div class="field full">
                        <label>Master kode rekening/rincian (satu baris: kode rincian = nama)</label>
                        <textarea id="accountCodesInput"></textarea>
                    </div>
                </div>
            </div>

            <div class="panel tab-panel" id="templateTab" hidden>
                <div class="panel-head"><h3>Template dan Lampiran</h3></div>
                <div class="panel-body form-grid">
                    <div class="field">
                        <label>Nama dana</label>
                        <input id="fundNameInput">
                    </div>
                    <div class="field">
                        <label>Checklist umum</label>
                        <textarea id="generalChecklistInput"></textarea>
                    </div>
                    <div class="field">
                        <label>Checklist honor</label>
                        <textarea id="honorChecklistInput"></textarea>
                    </div>
                    <div class="field">
                        <label>Checklist SIPLah/ATK</label>
                        <textarea id="siplahChecklistInput"></textarea>
                    </div>
                    <div class="field full">
                        <label>Catatan lampiran</label>
                        <input id="noteInput">
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<div id="printArea"></div>
<div class="toast" id="toast"></div>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const defaultActivityCodes = {
    '06.05.08.': 'Pembelian Bahan Habis Pakai untuk mendukung pembelajaran dan administrasi sekolah (termasuk ATK, Tinta Printer, Kabel Ekstension, dsb)',
    '06.07.01.': 'Pembayaran daya listrik',
    '06.07.04.': 'Pembayaran biaya telepon',
    '06.07.05.': 'Pembayaran jasa internet',
    '07.12.01.': 'Pembayaran honor Guru/Pendidik',
    '07.12.02.': 'Pembayaran honor Tenaga Kependidikan (selain pendidik)',
    '07.12.03.': 'Pembayaran Honor tenaga administrasi',
    '07.12.04.': 'Pembayaran honor Tenaga Penunjang atau pelaksana'
};
const defaultAccountCodes = {
    '5.1.02.01.01.00 24': 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Tulis Kantor',
    '5.1.02.02.01.00 08': 'Belanja Jasa Tenaga Administrasi',
    '5.1.02.02.01.00 13': 'Belanja Jasa Tenaga Pendidikan',
    '5.1.02.02.01.00 29': 'Belanja Jasa Tenaga Ahli',
    '5.1.02.02.01.00 30': 'Belanja Jasa Tenaga Kebersihan',
    '5.1.02.02.01.00 31': 'Belanja Jasa Tenaga Keamanan',
    '5.1.02.02.01.00 59': 'Belanja Tagihan Telepon',
    '5.1.02.02.01.00 61': 'Belanja Tagihan Listrik',
    '5.1.02.02.01.00 63': 'Belanja Kawat/Faksimili/Internet/TV Berlangganan'
};
const defaults = {
    school: {
        schoolName: 'SDN Uji Coba SIBUK BOS',
        npsn: '20542535',
        district: 'Kecamatan Kandangan',
        city: 'Kabupaten Pasuruan',
        province: 'Jawa Timur',
        source: 'BOSP Reguler'
    },
    sign: {
        principalName: 'Ridwan',
        principalNip: '10000000 200000 1 000',
        treasurerName: 'Nama Bendahara',
        treasurerNip: '10000000 200000 1 000',
        receiverName: '-',
        place: 'Kecamatan Kandangan'
    },
    template: {
        fundName: 'BANTUAN OPERASIONAL SATUAN PENDIDIKAN ( BOSP )',
        generalChecklist: 'Bukti Daftar Penerimaan\nBukti Transfer Pembayaran\nDokumen Pendukung Lain',
        honorChecklist: 'Bukti Daftar Penerimaan\nBukti Transfer Pembayaran\nSK Pengangkatan\nDaftar Hadir Bulan Berjalan',
        siplahChecklist: 'Bukti Transaksi SIPLAH\nBukti Transfer Pembayaran\nDokumen Pendukung Lain',
        note: 'Pastikan dokumen lengkap dan sah.'
    },
    activityCodes: defaultActivityCodes,
    accountCodes: defaultAccountCodes
};

let state = loadState();
let rows = [];
let receipts = [];

const $ = (id) => document.getElementById(id);

function loadState() {
    const saved = localStorage.getItem('bosp_receipt_settings');
    if (!saved) return structuredClone(defaults);
    try { return deepMerge(structuredClone(defaults), JSON.parse(saved)); }
    catch { return structuredClone(defaults); }
}

function deepMerge(base, extra) {
    for (const key in extra) {
        if (extra[key] && typeof extra[key] === 'object' && !Array.isArray(extra[key])) {
            base[key] = deepMerge(base[key] || {}, extra[key]);
        } else {
            base[key] = extra[key];
        }
    }
    return base;
}

function saveState() {
    collectSettings();
    localStorage.setItem('bosp_receipt_settings', JSON.stringify(state));
}

function init() {
    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }
    months.forEach((m, i) => $('monthSelect').insertAdjacentHTML('beforeend', `<option value="${i + 1}">${m}</option>`));
    $('monthSelect').value = 3;
    buildSettingsForms();
    bindEvents();
    loadDemoRows(false);
    regroup();
    lucide.createIcons();
}

function bindEvents() {
    document.querySelectorAll('[data-nav]').forEach(btn => btn.addEventListener('click', () => showView(btn.dataset.nav)));
    document.querySelectorAll('[data-nav-jump]').forEach(btn => btn.addEventListener('click', () => showView(btn.dataset.navJump)));
    document.querySelectorAll('[data-tab]').forEach(btn => btn.addEventListener('click', () => showTab(btn.dataset.tab)));
    ['monthSelect','yearInput','startNoInput','numberMode','mergeMode','stampLimit'].forEach(id => $(id).addEventListener('input', regroup));
    $('pdfInput').addEventListener('change', e => handlePdf(e.target.files[0]));
    $('dropZone').addEventListener('dragover', e => { e.preventDefault(); $('dropZone').classList.add('drag'); });
    $('dropZone').addEventListener('dragleave', () => $('dropZone').classList.remove('drag'));
    $('dropZone').addEventListener('drop', e => {
        e.preventDefault(); $('dropZone').classList.remove('drag');
        const file = e.dataTransfer.files[0];
        if (file) handlePdf(file);
    });
    $('demoBtn').addEventListener('click', () => { loadDemoRows(true); regroup(); });
    $('addRowBtn').addEventListener('click', () => { rows.push(blankRow()); renderRows(); regroup(); });
    $('regroupBtn').addEventListener('click', regroup);
    $('refreshPreviewBtn').addEventListener('click', renderPreview);
    $('printBtn').addEventListener('click', printReceipts);
    $('printBtn2').addEventListener('click', printReceipts);
    $('pdfBtn').addEventListener('click', downloadPdf);
    $('saveJsonBtn').addEventListener('click', saveProject);
    $('loadJsonBtn').addEventListener('click', () => $('jsonInput').click());
    $('jsonInput').addEventListener('change', loadProject);
    $('resetCodesBtn').addEventListener('click', () => {
        state.activityCodes = structuredClone(defaultActivityCodes);
        state.accountCodes = structuredClone(defaultAccountCodes);
        buildSettingsForms();
        regroup();
        toast('Master kode dikembalikan ke bawaan.');
    });
}

function showView(name) {
    document.querySelectorAll('[data-nav]').forEach(b => b.classList.toggle('active', b.dataset.nav === name));
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    $(`${name}View`).classList.add('active');
    const titles = {
        workspace: ['Generate Kuitansi', 'Upload PDF BKU, gabungkan transaksi, lalu cetak kuitansi dan lampiran SPJ.'],
        data: ['Data BKU', 'Cek dan koreksi hasil ekstraksi sebelum kuitansi dicetak.'],
        preview: ['Preview Cetak', 'Lihat bentuk akhir kuitansi dan lembar kelengkapan lampiran SPJ.'],
        settings: ['Settings', 'Atur identitas sekolah, pejabat, master kode, dan template dokumen.']
    };
    $('pageTitle').textContent = titles[name][0];
    $('pageHint').textContent = titles[name][1];
    if (name === 'preview') renderPreview();
}

function showTab(name) {
    document.querySelectorAll('[data-tab]').forEach(b => b.classList.toggle('active', b.dataset.tab === name));
    document.querySelectorAll('.tab-panel').forEach(p => p.hidden = true);
    $(`${name}Tab`).hidden = false;
}

function buildSettingsForms() {
    const schoolFields = [
        ['schoolName','Nama Sekolah'], ['npsn','NPSN'], ['district','Kecamatan'],
        ['city','Kabupaten/Kota'], ['province','Provinsi'], ['source','Sumber Dana']
    ];
    const signFields = [
        ['principalName','Nama Kepala Sekolah'], ['principalNip','NIP Kepala Sekolah'],
        ['treasurerName','Nama Bendahara'], ['treasurerNip','NIP Bendahara'],
        ['receiverName','Nama Penerima Default'], ['place','Tempat Tanggal']
    ];
    $('schoolForm').innerHTML = schoolFields.map(([key,label]) => fieldHtml(`school.${key}`, label, state.school[key])).join('');
    $('signForm').innerHTML = signFields.map(([key,label]) => fieldHtml(`sign.${key}`, label, state.sign[key])).join('');
    $('activityCodesInput').value = objectToLines(state.activityCodes);
    $('accountCodesInput').value = objectToLines(state.accountCodes);
    $('fundNameInput').value = state.template.fundName;
    $('generalChecklistInput').value = state.template.generalChecklist;
    $('honorChecklistInput').value = state.template.honorChecklist;
    $('siplahChecklistInput').value = state.template.siplahChecklist;
    $('noteInput').value = state.template.note;
    document.querySelectorAll('[data-setting]').forEach(input => input.addEventListener('input', () => { saveState(); regroup(); }));
    ['activityCodesInput','accountCodesInput','fundNameInput','generalChecklistInput','honorChecklistInput','siplahChecklistInput','noteInput'].forEach(id => {
        $(id).addEventListener('input', () => { saveState(); regroup(); });
    });
}

function fieldHtml(path, label, value) {
    return `<div class="field"><label>${label}</label><input data-setting="${path}" value="${escapeHtml(value || '')}"></div>`;
}

function collectSettings() {
    document.querySelectorAll('[data-setting]').forEach(input => {
        const [group, key] = input.dataset.setting.split('.');
        state[group][key] = input.value;
    });
    state.activityCodes = linesToObject($('activityCodesInput').value);
    state.accountCodes = linesToObject($('accountCodesInput').value);
    state.template.fundName = $('fundNameInput').value;
    state.template.generalChecklist = $('generalChecklistInput').value;
    state.template.honorChecklist = $('honorChecklistInput').value;
    state.template.siplahChecklist = $('siplahChecklistInput').value;
    state.template.note = $('noteInput').value;
}

function objectToLines(obj) {
    return Object.entries(obj).map(([k,v]) => `${k} = ${v}`).join('\n');
}

function linesToObject(text) {
    const obj = {};
    text.split(/\n+/).map(s => s.trim()).filter(Boolean).forEach(line => {
        const parts = line.split(/\s+=\s+|=/);
        if (parts.length >= 2) obj[parts.shift().trim()] = parts.join('=').trim();
    });
    return obj;
}

async function handlePdf(file) {
    if (!file || file.type !== 'application/pdf') {
        toast('Pilih file PDF BKU.');
        return;
    }
    $('fileBadge').textContent = file.name;
    $('fileBadge').className = 'badge good';
    setProgress(8);
    try {
        const buffer = await file.arrayBuffer();
        const doc = await pdfjsLib.getDocument({ data: buffer }).promise;
        const parsed = [];
        for (let pageNo = 1; pageNo <= doc.numPages; pageNo++) {
            setProgress(Math.round((pageNo / doc.numPages) * 78));
            const page = await doc.getPage(pageNo);
            const content = await page.getTextContent();
            parsed.push(...parsePageItems(content.items, pageNo));
        }
        rows = normalizeParsedRows(parsed);
        if (!rows.length) {
            toast('Belum menemukan transaksi pengeluaran. Coba cek PDF atau isi manual.');
        } else {
            toast(`${rows.length} baris transaksi berhasil diekstrak.`);
        }
        setProgress(100);
        renderRows();
        regroup();
        setTimeout(() => setProgress(0), 900);
    } catch (error) {
        console.error(error);
        toast('Gagal membaca PDF. Pastikan file tidak diproteksi.');
        setProgress(0);
    }
}

function parsePageItems(items, pageNo) {
    const grouped = [];
    items.forEach(item => {
        const text = String(item.str || '').trim();
        if (!text) return;
        const x = item.transform[4];
        const y = Math.round(item.transform[5] / 3) * 3;
        let row = grouped.find(r => Math.abs(r.y - y) <= 2);
        if (!row) { row = { y, pageNo, parts: [] }; grouped.push(row); }
        row.parts.push({ x, text });
    });
    grouped.sort((a,b) => b.y - a.y);
    return grouped.map(row => {
        row.parts.sort((a,b) => a.x - b.x);
        return parseVisualRow(row);
    }).filter(Boolean);
}

function parseVisualRow(row) {
    const joined = row.parts.map(p => p.text).join(' ').replace(/\s+/g, ' ').trim();
    if (!joined || /BUKU KAS UMUM|TANGGAL|Halaman|Jumlah|Saldo Buku|Menyetujui|Kepala Sekolah/i.test(joined)) return null;
    const date = matchFirst(joined, /\b\d{2}-\d{2}-\d{4}\b/);
    const proof = matchFirst(joined, /\bBPU\s*\d+\b/i)?.replace(/\s+/g, '').toUpperCase() || '';
    const activity = matchFirst(joined, /\b\d{2}\.\d{2}\.\d{2}\./);
    const account = matchFirst(joined, /\b5\.\d\.\d{2}\.\d{2}\.\d{2}\.\d{2}\b/);
    const detail = extractDetailCode(row.parts, joined);
    const amount = extractExpense(row.parts);
    const description = extractDescription(row.parts, joined);
    if (!date && !proof && !activity && !account && !description) return null;
    return { date: date || '', proof, activity: activity || '', account: account || '', detail, description, amount, raw: joined };
}

function extractDetailCode(parts, text) {
    const smallCodes = parts.filter(p => p.x > 180 && p.x < 310 && /^\d{2}$/.test(p.text)).map(p => p.text);
    return smallCodes[0] || matchFirst(text, /\b(08|13|24|29|30|31|59|61|63)\b/) || '';
}

function extractExpense(parts) {
    const nums = parts
        .filter(p => p.x > 500 || /^(\d{1,3}\.)+\d{3}$/.test(p.text))
        .map(p => parseMoney(p.text))
        .filter(n => n > 0);
    if (!nums.length) return 0;
    const likely = nums.filter(n => n < 100000000);
    return likely.length ? likely[likely.length - 1] : 0;
}

function extractDescription(parts, text) {
    const fromMiddle = parts.filter(p => p.x > 300 && p.x < 620 && !/^BPU\s*\d+/i.test(p.text) && !/^(\d{1,3}\.)+\d{3}$/.test(p.text)).map(p => p.text).join(' ');
    let desc = fromMiddle || text;
    desc = desc
        .replace(/\b\d{2}-\d{2}-\d{4}\b/g, '')
        .replace(/\b\d{2}\.\d{2}\.\d{2}\./g, '')
        .replace(/\b5\.\d\.\d{2}\.\d{2}\.\d{2}\.\d{2}\b/g, '')
        .replace(/\bBPU\s*\d+\b/ig, '')
        .replace(/\b(0|(\d{1,3}\.)+\d{3})\b/g, '')
        .replace(/\s+/g, ' ')
        .trim();
    if (/^(saldo|tarik tunai|pergeseran uang|bunga bank|pajak bunga)$/i.test(desc)) return '';
    return desc;
}

function normalizeParsedRows(parsed) {
    let last = { date: '', proof: '', activity: '', account: '', detail: '' };
    const out = [];
    parsed.forEach(item => {
        if (item.date) last.date = item.date;
        if (item.proof) last.proof = item.proof;
        if (item.activity) last.activity = item.activity;
        if (item.account) last.account = item.account;
        if (item.detail) last.detail = item.detail;
        const row = {
            date: item.date || last.date,
            proof: item.proof || last.proof,
            activity: item.activity || last.activity,
            account: item.account || last.account,
            detail: item.detail || last.detail,
            description: item.description,
            amount: item.amount || 0,
            warning: ''
        };
        if (!row.description || row.amount <= 0) return;
        if (!row.proof) row.proof = `AUTO${String(out.length + 1).padStart(3, '0')}`;
        if (!row.activity || !row.account) row.warning = 'Kode belum lengkap';
        out.push(row);
    });
    return out;
}

function blankRow() {
    return { date: isoDateFromInputs(), proof: '', activity: '', account: '', detail: '', description: '', amount: 0, warning: 'Manual' };
}

function renderRows() {
    $('rowsBody').innerHTML = rows.map((row, index) => `
        <tr>
            <td><input value="${escapeHtml(row.date)}" data-row="${index}" data-key="date"></td>
            <td><input value="${escapeHtml(row.proof)}" data-row="${index}" data-key="proof"></td>
            <td><input value="${escapeHtml(row.activity)}" data-row="${index}" data-key="activity"></td>
            <td><input value="${escapeHtml(row.account)}" data-row="${index}" data-key="account"></td>
            <td><input value="${escapeHtml(row.detail)}" data-row="${index}" data-key="detail"></td>
            <td><textarea data-row="${index}" data-key="description">${escapeHtml(row.description)}</textarea></td>
            <td><input type="number" value="${Number(row.amount || 0)}" data-row="${index}" data-key="amount"></td>
            <td><div class="row-tools"><button class="btn icon danger" title="Hapus" data-delete-row="${index}"><i data-lucide="trash-2"></i></button></div></td>
        </tr>
    `).join('');
    document.querySelectorAll('[data-row]').forEach(input => input.addEventListener('input', updateRowFromInput));
    document.querySelectorAll('[data-delete-row]').forEach(btn => btn.addEventListener('click', () => {
        rows.splice(Number(btn.dataset.deleteRow), 1);
        renderRows();
        regroup();
    }));
    lucide.createIcons();
}

function updateRowFromInput(e) {
    const index = Number(e.target.dataset.row);
    const key = e.target.dataset.key;
    rows[index][key] = key === 'amount' ? Number(e.target.value || 0) : e.target.value;
    regroup();
}

function regroup() {
    saveState();
    const mode = $('mergeMode').value;
    const map = new Map();
    rows.filter(r => r.description && Number(r.amount) > 0).forEach((row, index) => {
        const key = mode === 'none'
            ? `row-${index}`
            : mode === 'by-proof-only'
                ? row.proof
                : [row.proof, row.activity, row.account, row.detail].join('|');
        if (!map.has(key)) {
            map.set(key, {
                sourceProof: row.proof,
                proof: row.proof,
                date: row.date,
                activity: row.activity,
                account: row.account,
                detail: row.detail,
                descriptions: [],
                amount: 0,
                warnings: []
            });
        }
        const group = map.get(key);
        group.date = row.date || group.date;
        group.activity = row.activity || group.activity;
        group.account = row.account || group.account;
        group.detail = row.detail || group.detail;
        if (!group.descriptions.includes(row.description)) group.descriptions.push(row.description);
        group.amount += Number(row.amount || 0);
        if (row.warning) group.warnings.push(row.warning);
    });
    receipts = Array.from(map.values()).map((receipt, idx) => decorateReceipt(receipt, idx));
    renderReceiptList();
    renderStats();
    if ($('previewView').classList.contains('active')) renderPreview();
}

function decorateReceipt(receipt, idx) {
    const proof = $('numberMode').value === 'renumber' ? nextProof($('startNoInput').value, idx) : receipt.proof;
    const accountKey = `${receipt.account} ${receipt.detail}`.trim();
    const activityName = state.activityCodes[receipt.activity] || '';
    const accountName = state.accountCodes[accountKey] || state.accountCodes[receipt.account] || '';
    const joinedDescription = receipt.descriptions.join(' | ');
    return {
        ...receipt,
        proof,
        activityName,
        accountKey,
        accountName,
        description: joinedDescription,
        terbilang: terbilang(receipt.amount) + ' Rupiah',
        checklist: chooseChecklist(receipt, joinedDescription, activityName, accountName),
        useStamp: receipt.amount >= Number($('stampLimit').value || 0),
        dateText: formatIndoDate(receipt.date)
    };
}

function chooseChecklist(receipt, desc, activityName, accountName) {
    const text = `${receipt.detail} ${desc} ${activityName} ${accountName}`.toLowerCase();
    if (/(honor|tenaga|pendidik|13|29|30|31)/.test(text)) return splitLines(state.template.honorChecklist);
    if (/(siplah|atk|bahan habis|alat tulis|24)/.test(text)) return splitLines(state.template.siplahChecklist);
    return splitLines(state.template.generalChecklist);
}

function renderReceiptList() {
    if (!receipts.length) {
        $('receiptList').innerHTML = '<div class="empty">Belum ada kuitansi. Upload PDF BKU atau isi data demo.</div>';
        return;
    }
    $('receiptList').innerHTML = receipts.map(r => `
        <article class="receipt-card">
            <header>
                <h4>${escapeHtml(r.proof)} - ${formatMoney(r.amount)}</h4>
                <span class="badge ${r.warnings.length ? 'warn' : 'good'}">${r.descriptions.length} item</span>
            </header>
            <p><b>${escapeHtml(r.activity || '-')}</b> ${escapeHtml(r.activityName || '')}</p>
            <p>${escapeHtml(r.description)}</p>
        </article>
    `).join('');
}

function renderStats() {
    const total = receipts.reduce((sum, r) => sum + r.amount, 0);
    $('statRows').textContent = rows.length;
    $('statReceipts').textContent = receipts.length;
    $('statTotal').textContent = formatMoney(total);
    $('statWarnings').textContent = rows.filter(r => r.warning).length;
}

function renderPreview() {
    collectSettings();
    const html = receipts.length ? receipts.map(receiptHtml).join('') : '<div class="empty">Belum ada kuitansi untuk ditampilkan.</div>';
    $('previewShell').innerHTML = html;
    $('printArea').innerHTML = html;
}

function receiptHtml(r) {
    return `
    <section class="receipt-page">
        <div class="receipt-title">
            <h1>KUITANSI</h1>
            <b>No. BKU : ${escapeHtml(r.proof)}</b>
        </div>
        <div class="receipt-subtitle">${escapeHtml(state.template.fundName)} <span style="float:right">Tahun Anggaran : ${escapeHtml($('yearInput').value)}</span></div>
        <table class="kv">
            <tr><td>Kode Program</td><td>:</td><td>${escapeHtml(r.program || '-')}</td></tr>
            <tr><td>Kode Kegiatan</td><td>:</td><td>${escapeHtml(r.activity)}${r.activityName ? ' - ' + escapeHtml(r.activityName) : ''}</td></tr>
            <tr><td>Kode Rekening</td><td>:</td><td>${escapeHtml(r.accountKey)}${r.accountName ? ' - ' + escapeHtml(r.accountName) : ''}</td></tr>
            <tr><td>Sudah Terima Dari</td><td>:</td><td>Kepala ${escapeHtml(state.school.schoolName)} Kecamatan ${escapeHtml(state.school.district)}</td></tr>
            <tr><td>Jumlah Uang</td><td>:</td><td>${formatMoney(r.amount)} ,-</td></tr>
            <tr><td>Terbilang</td><td>:</td><td>${escapeHtml(r.terbilang)}</td></tr>
            <tr><td>Untuk Pembayaran</td><td>:</td><td>${escapeHtml(r.description)}</td></tr>
        </table>
        <div class="amount-box">
            <div class="amount-row"><span>Perincian Penerimaan</span><b>${formatMoney(r.amount)} ,-</b></div>
            <div class="amount-row"><span>Penerimaan</span><b>${formatMoney(r.amount)} ,-</b></div>
            <div class="amount-row"><span>Jumlah Diterimakan</span><b>${formatMoney(r.amount)} ,-</b></div>
        </div>
        <div class="signatures">
            <div>
                <div>Mengetahui,<br>Kepala Sekolah</div>
                <div class="sig-space"></div>
                <div class="sig-name">${escapeHtml(state.sign.principalName)}</div>
                <div>NIP. ${escapeHtml(state.sign.principalNip)}</div>
            </div>
            <div>
                <div>Lunas Dibayar Oleh,<br>Bendahara BOS</div>
                <div class="sig-space">${r.useStamp ? '<div class="materai">Materai<br>Rp 10.000</div>' : ''}</div>
                <div class="sig-name">${escapeHtml(state.sign.treasurerName)}</div>
                <div>NIP. ${escapeHtml(state.sign.treasurerNip)}</div>
            </div>
            <div>
                <div>${escapeHtml(state.sign.place)}, ${escapeHtml(r.dateText)}<br>Penerima</div>
                <div class="sig-space"></div>
                <div class="sig-name">${escapeHtml(state.sign.receiverName || '-')}</div>
            </div>
        </div>
        <div class="attachment">
            <h2>LEMBAR KELENGKAPAN LAMPIRAN SPJ</h2>
            <table class="kv">
                <tr><td>No Bukti</td><td>:</td><td>${escapeHtml(r.proof)}</td></tr>
                <tr><td>Uraian</td><td>:</td><td>${escapeHtml(r.description)}</td></tr>
            </table>
            <b>Checklist Dokumen Pendukung :</b>
            <ul class="checklist">${r.checklist.map(item => `<li><span class="box"></span>${escapeHtml(item)}</li>`).join('')}</ul>
            <p><b>Catatan :</b> ${escapeHtml(state.template.note)}</p>
        </div>
    </section>`;
}

function printReceipts() {
    renderPreview();
    if (!receipts.length) return toast('Belum ada kuitansi untuk dicetak.');
    window.print();
}

async function downloadPdf() {
    renderPreview();
    if (!receipts.length) return toast('Belum ada kuitansi untuk diunduh.');
    const element = $('printArea').cloneNode(true);
    element.style.display = 'block';
    element.style.background = '#fff';
    document.body.appendChild(element);
    const filename = `Kuitansi_BOSP_${months[Number($('monthSelect').value) - 1]}_${$('yearInput').value}.pdf`;
    await html2pdf().set({
        margin: 0,
        filename,
        image: { type: 'jpeg', quality: .98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['css', 'legacy'] }
    }).from(element).save();
    element.remove();
}

function saveProject() {
    saveState();
    const data = JSON.stringify({ settings: state, rows, options: getOptions() }, null, 2);
    const blob = new Blob([data], { type: 'application/json' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `project-kuitansi-bosp-${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(a.href);
}

function loadProject(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => {
        try {
            const data = JSON.parse(reader.result);
            state = deepMerge(structuredClone(defaults), data.settings || {});
            rows = data.rows || [];
            applyOptions(data.options || {});
            buildSettingsForms();
            renderRows();
            regroup();
            toast('Project berhasil dibuka.');
        } catch {
            toast('File JSON tidak valid.');
        }
    };
    reader.readAsText(file);
    e.target.value = '';
}

function getOptions() {
    return {
        month: $('monthSelect').value,
        year: $('yearInput').value,
        startNo: $('startNoInput').value,
        numberMode: $('numberMode').value,
        mergeMode: $('mergeMode').value,
        stampLimit: $('stampLimit').value
    };
}

function applyOptions(options) {
    if (options.month) $('monthSelect').value = options.month;
    if (options.year) $('yearInput').value = options.year;
    if (options.startNo) $('startNoInput').value = options.startNo;
    if (options.numberMode) $('numberMode').value = options.numberMode;
    if (options.mergeMode) $('mergeMode').value = options.mergeMode;
    if (options.stampLimit) $('stampLimit').value = options.stampLimit;
}

function loadDemoRows(showToast) {
    rows = [
        { date:'08-03-2025', proof:'BPU100', activity:'06.07.05.', account:'5.1.02.02.01.00', detail:'63', description:'Langganan Internet-Dedicated Fiber Optik 40 Mbps', amount:4000000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Staples Sedang', amount:300000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Lakban Tanggung', amount:300000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Ballpoint', amount:450000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Kertas Buffalo', amount:350000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Plastik Mika', amount:250000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Isolasi Bening-Uk. 2 Inch', amount:250000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Kertas Warna Blank F4, 70 gram', amount:450000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Kertas stiker', amount:200000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Penghapus White Board', amount:600000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Snelhekter Plastik', amount:800000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Staples Kecil', amount:300000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Stapler Isi Stapler No, 10', amount:930000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Amplop Panjang Putih Polos Amplop polos Ukuran 90', amount:1300000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Amplop Polos Pendek No, 104', amount:900000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Tip Ex Mek : KENKO', amount:350000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Kertas HVS Folio', amount:1950000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Spidol Board Marker', amount:2400000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Stabilo', amount:700000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Ballpoint Standard AE7', amount:100000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Amplop Coklat / casing D', amount:2000000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Spidol Permanen', amount:850000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Pensil', amount:150000, warning:'' },
        { date:'19-03-2025', proof:'BPU101', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Kertas HVS 80 gram Merk : Sidu A4', amount:550000, warning:'' },
        { date:'19-03-2025', proof:'BPU102', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Tinta Refill Printer', amount:1500000, warning:'' },
        { date:'19-03-2025', proof:'BPU102', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Tinta Warna', amount:3200000, warning:'' },
        { date:'19-03-2025', proof:'BPU102', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Stapler Isi Stapler No,3 MAX per Pcs', amount:420000, warning:'' },
        { date:'19-03-2025', proof:'BPU102', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Isi Pisau Cutter Besar', amount:270000, warning:'' },
        { date:'19-03-2025', proof:'BPU102', activity:'06.05.08.', account:'5.1.02.01.01.00', detail:'24', description:'Gunting Besar', amount:150000, warning:'' },
        { date:'20-03-2025', proof:'BPU103', activity:'06.07.01.', account:'5.1.02.02.01.00', detail:'61', description:'Tagihan Listrik-Spesifikasi : 5.501- 200 kVA', amount:5083000, warning:'' },
        { date:'20-03-2025', proof:'BPU104', activity:'06.07.04.', account:'5.1.02.02.01.00', detail:'59', description:'Abonemen Telepon-Sosial', amount:380000, warning:'' },
        { date:'27-03-2025', proof:'BPU105', activity:'07.12.01.', account:'5.1.02.02.01.00', detail:'13', description:'Akhmad Barizi (2835769671110002)', amount:1200000, warning:'' },
        { date:'27-03-2025', proof:'BPU106', activity:'07.12.01.', account:'5.1.02.02.01.00', detail:'13', description:'AKH. MAKHBUBILLAH (7850775676130042)', amount:1200000, warning:'' },
        { date:'27-03-2025', proof:'BPU143', activity:'07.12.04.', account:'5.1.02.02.01.00', detail:'29', description:'Driver', amount:1200000, warning:'' },
        { date:'27-03-2025', proof:'BPU144', activity:'07.12.04.', account:'5.1.02.02.01.00', detail:'30', description:'Petugas Kebersihan', amount:4000000, warning:'' },
        { date:'27-03-2025', proof:'BPU145', activity:'07.12.04.', account:'5.1.02.02.01.00', detail:'31', description:'Petugas Keamanan', amount:4000000, warning:'' }
    ];
    renderRows();
    if (showToast) toast('Data demo dari pola PDF contoh dimuat.');
}

function nextProof(start, offset) {
    const m = String(start || 'BPU1').match(/^([A-Za-z]*)(\d+)$/);
    if (!m) return `${start}${offset + 1}`;
    return `${m[1].toUpperCase()}${String(Number(m[2]) + offset).padStart(m[2].length, '0')}`;
}

function parseMoney(text) {
    const clean = String(text || '').replace(/[^\d]/g, '');
    return clean ? Number(clean) : 0;
}

function formatMoney(value) {
    return 'Rp. ' + Number(value || 0).toLocaleString('id-ID');
}

function matchFirst(text, regex) {
    return (String(text || '').match(regex) || [])[0] || '';
}

function splitLines(text) {
    return String(text || '').split(/\n+/).map(s => s.trim()).filter(Boolean);
}

function setProgress(value) {
    $('progressBar').style.width = `${value}%`;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
}

function isoDateFromInputs() {
    return `01-${String($('monthSelect').value).padStart(2, '0')}-${$('yearInput').value}`;
}

function formatIndoDate(date) {
    const m = String(date || '').match(/(\d{2})-(\d{2})-(\d{4})/);
    if (!m) return date || '';
    return `${Number(m[1])} ${months[Number(m[2]) - 1]} ${m[3]}`;
}

function terbilang(number) {
    number = Math.floor(Number(number || 0));
    const units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    if (number < 12) return units[number] || 'Nol';
    if (number < 20) return `${terbilang(number - 10)} Belas`;
    if (number < 100) return `${terbilang(Math.floor(number / 10))} Puluh ${terbilang(number % 10)}`.replace(/\sNol/g, '');
    if (number < 200) return `Seratus ${terbilang(number - 100)}`.replace(/\sNol/g, '');
    if (number < 1000) return `${terbilang(Math.floor(number / 100))} Ratus ${terbilang(number % 100)}`.replace(/\sNol/g, '');
    if (number < 2000) return `Seribu ${terbilang(number - 1000)}`.replace(/\sNol/g, '');
    if (number < 1000000) return `${terbilang(Math.floor(number / 1000))} Ribu ${terbilang(number % 1000)}`.replace(/\sNol/g, '');
    if (number < 1000000000) return `${terbilang(Math.floor(number / 1000000))} Juta ${terbilang(number % 1000000)}`.replace(/\sNol/g, '');
    return `${terbilang(Math.floor(number / 1000000000))} Miliar ${terbilang(number % 1000000000)}`.replace(/\sNol/g, '');
}

function toast(message) {
    $('toast').textContent = message;
    $('toast').classList.add('show');
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(() => $('toast').classList.remove('show'), 2600);
}

init();
</script>
</body>
</html>
