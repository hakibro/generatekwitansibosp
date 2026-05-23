@extends('layouts.app')

@section('title', 'Input BKU - Generator Kuitansi BOSP')
@section('page_title', 'Input File BKU')
@section('page_hint', 'Pilih tahun, pilih bulan, upload PDF, tampilkan data hasil ekstraksi, lalu simpan.')

@section('content')
    <form id="importForm" method="post" action="{{ route('imports.store') }}" class="grid">
        @csrf
        <input type="hidden" name="rows_json" id="rowsJson">
        <input type="hidden" name="source_file" id="sourceFile">
        <input type="hidden" name="merge_mode" value="by-proof-only">

        <div class="panel">
            <div class="panel-head">
                <h2>Periode dan File BKU</h2>
                <span class="badge" id="rowCount">Belum ada file</span>
            </div>
            <div class="panel-body">
                <div class="grid grid-cols-1 lg:grid-cols-[220px_220px_220px_1fr] gap-3 items-end">
                    <div class="field">
                        <label>Tahun</label>
                        <input type="number" name="year" id="yearInput" value="{{ now()->year }}" required>
                    </div>
                    <div class="field">
                        <label>Bulan</label>
                        <select name="month" id="monthInput" required>
                            @foreach (\App\Support\ReceiptSettings::MONTHS as $value => $label)
                                <option value="{{ $value }}" @selected(now()->month === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Batas Materai</label>
                        <input type="number" name="stamp_limit" value="5000000" required>
                    </div>
                    <div id="uploadStatus" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                        <i class="fa-solid fa-circle-check mr-2 animate-pulse"></i><span id="uploadStatusText">File siap dipreview</span>
                    </div>
                </div>

                <label class="drop mt-4" id="dropZone">
                    <input type="file" id="pdfInput" accept="application/pdf">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-white text-2xl text-brand shadow-sm"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                    <strong>Klik atau seret PDF BKU ke sini</strong>
                    <span>Setelah file terbaca, preview data akan muncul otomatis. Periksa data, lalu simpan untuk lanjut edit penerima dan transaksi.</span>
                </label>
                <div class="mt-4 progress"><span id="progressBar"></span></div>
            </div>
        </div>
    </form>

    <div id="previewModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/45 backdrop-blur-sm" data-close-preview></div>
        <div class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-8">
            <div class="w-full overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Preview Data Import</h2>
                        <p class="text-sm text-slate-500" id="previewSummary">Periksa data sebelum disimpan.</p>
                    </div>
                    <button class="btn" type="button" data-close-preview><i class="fa-solid fa-xmark"></i>Tutup</button>
                </div>
                <div class="max-h-[68vh] overflow-auto p-5">
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
                                </tr>
                            </thead>
                            <tbody id="rowsBody"></tbody>
                        </table>
                    </div>
                    <div id="emptyState" class="empty">Belum ada data yang bisa dipreview.</div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                    <span class="badge" id="modalRowCount">0 baris</span>
                    <button type="submit" form="importForm" class="btn good" id="saveBtn" disabled>
                        <i class="fa-solid fa-floppy-disk"></i>Simpan dan Edit Penerima/Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let rows = [];
const $ = (id) => document.getElementById(id);
const proofRegex = /\bB[PN]U\s*\d+\b/i;
const proofRegexGlobal = /\bB[PN]U\s*\d+\b/ig;

$('pdfInput').addEventListener('change', (event) => handlePdf(event.target.files[0]));
$('importForm').addEventListener('submit', () => $('rowsJson').value = JSON.stringify(rows));
document.querySelectorAll('[data-close-preview]').forEach((item) => item.addEventListener('click', closePreview));

$('dropZone').addEventListener('dragover', event => { event.preventDefault(); $('dropZone').classList.add('drag'); });
$('dropZone').addEventListener('dragleave', () => $('dropZone').classList.remove('drag'));
$('dropZone').addEventListener('drop', event => {
    event.preventDefault();
    $('dropZone').classList.remove('drag');
    if (event.dataTransfer.files[0]) handlePdf(event.dataTransfer.files[0]);
});

async function handlePdf(file) {
    if (!file || file.type !== 'application/pdf') return alert('Pilih file PDF BKU.');
    $('sourceFile').value = file.name;
    $('uploadStatus').classList.add('hidden');
    setProgress(8);
    const buffer = await file.arrayBuffer();
    const doc = await pdfjsLib.getDocument({ data: buffer }).promise;
    const parsed = [];

    for (let pageNo = 1; pageNo <= doc.numPages; pageNo++) {
        setProgress(Math.round(pageNo / doc.numPages * 85));
        const page = await doc.getPage(pageNo);
        const content = await page.getTextContent();
        parsed.push(...parsePageItems(content.items));
    }

    rows = normalizeRows(parsed);
    updateRowCount();
    setProgress(100);
    setTimeout(() => setProgress(0), 900);
    $('uploadStatusText').textContent = `${file.name} berhasil diupload dan diekstrak`;
    $('uploadStatus').classList.remove('hidden');
    renderRows();
    openPreview();
}

function parsePageItems(items) {
    const lines = [];
    items.forEach(item => {
        const text = String(item.str || '').trim();
        if (!text) return;
        const y = Math.round(item.transform[5] / 4) * 4;
        let line = lines.find(row => Math.abs(row.y - y) <= 4);
        if (!line) { line = { y, parts: [] }; lines.push(line); }
        line.parts.push({ x: item.transform[4], text });
    });
    return lines.sort((a,b) => b.y - a.y).map(parseLine).filter(Boolean);
}

function parseLine(line) {
    line.parts.sort((a,b) => a.x - b.x);
    const text = line.parts.map(part => part.text).join(' ').replace(/\s+/g, ' ').trim();
    if (!text || /BUKU KAS UMUM|TANGGAL|Halaman|Jumlah|Saldo Buku|Menyetujui|Kepala Sekolah/i.test(text)) return null;

    const row = {
        date: match(text, /\b\d{2}-\d{2}-\d{4}\b/),
        proof: (match(text, proofRegex) || '').replace(/\s+/g, '').toUpperCase(),
        activity: match(text, /\b\d{2}\.\d{2}\.\d{2}\./),
        account: match(text, /\b5\.\d\.\d{2}\.\d{2}\.\d{2}\.\d{2}\b/),
        detail: extractDetail(line.parts, text),
        amount: extractAmount(line.parts),
        description: extractDescription(line.parts, text),
        warning: ''
    };

    if (!row.date && !row.proof && !row.activity && !row.account && !row.description) return null;
    return row;
}

function normalizeRows(parsed) {
    const last = { date:'', activity:'', account:'', detail:'', proof:'' };
    const out = [];
    const pending = [];

    const pushReady = (row) => {
        row.description = cleanDescription(row.description);
        row.detail = inferDetail(row);
        if (row.proof && row.description && row.amount > 0) out.push(row);
    };

    parsed.forEach(item => {
        if (item.date) last.date = item.date;
        if (item.activity) last.activity = item.activity;
        if (item.account) last.account = item.account;
        if (item.detail) last.detail = item.detail;

        if (item.proof) {
            last.proof = item.proof;
            const row = {
                date: item.date || last.date,
                proof: item.proof,
                activity: item.activity || last.activity,
                account: item.account || last.account,
                detail: item.detail || last.detail,
                description: item.description,
                amount: item.amount,
                warning: (!last.activity || !last.account) ? 'Kode belum lengkap' : ''
            };
            if (row.amount > 0) {
                pushReady(row);
            } else if (row.description && !looksLikeSummary(row.description)) {
                pending.push(row);
            }
            return;
        }

        if (item.detail && pending.length) {
            const row = pending[pending.length - 1];
            if (!row.detail || shouldReplaceDetail(row.detail, row)) {
                row.detail = item.detail;
            }
        }

        if (item.amount > 0 && pending.length) {
            const row = pending.shift();
            row.amount = item.amount;
            if (item.description && !looksLikeSummary(item.description)) {
                row.description = `${row.description} ${item.description}`;
            }
            pushReady(row);
            return;
        }

        if (pending.length && item.description && !looksLikeSummary(item.description)) {
            const row = pending[pending.length - 1];
            row.description = `${row.description} ${item.description}`;
        }
    });

    return out.filter(row => row.proof && row.description && row.amount > 0).map(row => ({
        ...row,
        description: cleanDescription(row.description),
    }));
}

function cleanDescription(text) {
    return String(text || '')
        .replace(/\s+/g, ' ')
        .replace(/^(?:\d{1,2}\s+)+(?=[A-Za-z(])/g, '')
        .replace(/^(?:\d+(?:\.\d+)+\.?\s+)+/g, '')
        .replace(/^(?:\d{1,2}\s+)+(?=[A-Za-z(])/g, '')
        .replace(/\bTerima\b.*$/i, '')
        .replace(/\bSetor\b.*$/i, '')
        .trim();
}

function looksLikeSummary(text) {
    return /Saldo|Tarik Tunai|Pergeseran Uang|Terima |Setor /i.test(text || '');
}

function renderRows() {
    $('emptyState').classList.toggle('hidden', rows.length > 0);
    $('saveBtn').disabled = rows.length === 0;
    updateRowCount();
    $('rowsBody').innerHTML = rows.map((row, index) => `
        <tr>
            ${inputCell(index, 'date', row.date)}
            ${inputCell(index, 'proof', row.proof)}
            ${inputCell(index, 'activity', row.activity)}
            ${inputCell(index, 'account', row.account)}
            ${inputCell(index, 'detail', row.detail)}
            ${textareaCell(index, 'description', row.description)}
            ${inputCell(index, 'amount', row.amount, 'number')}
        </tr>
    `).join('');
    document.querySelectorAll('[data-row]').forEach(input => input.addEventListener('input', updateRow));
}

function openPreview() {
    $('previewModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePreview() {
    $('previewModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function inputCell(index, key, value, type = 'text') {
    return `<td><input type="${type}" data-row="${index}" data-key="${key}" value="${escapeHtml(value || '')}"></td>`;
}

function textareaCell(index, key, value) {
    return `<td><textarea data-row="${index}" data-key="${key}" style="min-width:260px;min-height:38px">${escapeHtml(value || '')}</textarea></td>`;
}

function updateRow(event) {
    const index = Number(event.target.dataset.row);
    const key = event.target.dataset.key;
    rows[index][key] = key === 'amount' ? Number(event.target.value || 0) : event.target.value;
}

function updateRowCount() {
    const text = `${rows.length} baris / ${uniqueProofCount(rows)} no bukti`;
    $('rowCount').textContent = rows.length ? text : 'Belum ada file';
    $('modalRowCount').textContent = text;
    $('previewSummary').textContent = rows.length ? `Ditemukan ${text}. Koreksi langsung di tabel ini sebelum simpan.` : 'Periksa data sebelum disimpan.';
}

function uniqueProofCount(items) {
    return new Set(items.map(row => row.proof).filter(Boolean)).size;
}

function extractDetail(parts, text) {
    const beforeProof = String(text || '').match(/\b(03|08|13|24|29|30|31|52|59|61|63)\s+B[PN]U\s*\d+\b/i);
    if (beforeProof) return beforeProof[1];

    return (parts.find(part => part.x > 180 && part.x < 315 && isDetailCode(part.text)) || {}).text || '';
}

function isDetailCode(value) {
    return /^(03|08|13|24|29|30|31|52|59|61|63)$/.test(String(value || '').trim());
}

function inferDetail(row) {
    const current = String(row.detail || '').trim();
    const account = String(row.account || '').trim();
    const activity = String(row.activity || '').trim();
    const text = `${row.description || ''} ${row.activity || ''}`.toLowerCase();
    let inferred = '';

    if (/^07\.12\.(01|02|03)\.?$/.test(activity)) {
        inferred = '13';
    } else if (account === '5.1.02.04.01.00') {
        inferred = '03';
    } else if (/internet|fiber|dedicated|langganan internet/.test(text)) {
        inferred = '63';
    } else if (/listrik|daya|tarif listrik/.test(text)) {
        inferred = '61';
    } else if (/telepon|abonemen/.test(text)) {
        inferred = '59';
    } else if (/tenaga ahli|profesional|skk|ska/.test(text)) {
        inferred = '29';
    } else if (/administrasi/.test(text)) {
        inferred = '08';
    } else if (/guru|pendidik/.test(text)) {
        inferred = '13';
    } else if (/kebersihan/.test(text)) {
        inferred = '30';
    } else if (/keamanan/.test(text)) {
        inferred = '31';
    } else if (/atk|alat tulis|bahan habis|printer|kertas|ballpoint|staples|lakban|amplop|spidol|pensil|tinta/.test(text)) {
        inferred = '24';
    }

    if (!current || shouldReplaceDetail(current, row)) {
        return inferred || current;
    }

    return current;
}

function shouldReplaceDetail(detail, row) {
    const account = String(row.account || '').trim();
    const activity = String(row.activity || '').trim();
    const text = String(row.description || '').toLowerCase();

    return (/^07\.12\.(01|02|03)\.?$/.test(activity) && detail !== '13')
        || (account === '5.1.02.04.01.00' && detail !== '03')
        || (/internet|fiber|dedicated/.test(text) && detail !== '63')
        || (/listrik|tarif listrik|daya/.test(text) && detail !== '61')
        || (/tenaga ahli|profesional|skk|ska/.test(text) && detail !== '29');
}

function extractAmount(parts) {
    const values = parts
        .filter(part => part.x > 500 || /^(\d{1,3}\.)+\d{3}$/.test(part.text))
        .map(part => Number(part.text.replace(/[^\d]/g, '')))
        .filter(value => value > 0 && value < 100000000);
    return values.length ? values[values.length - 1] : 0;
}

function extractDescription(parts, text) {
    const middle = parts.filter(part => {
        return part.x > 250
            && part.x < 620
            && !/^\d{2}$/.test(part.text)
            && !proofRegex.test(part.text)
            && !/^(\d{1,3}\.)+\d{3}$/.test(part.text);
    }).map(part => part.text).join(' ');
    return (middle || text)
        .replace(/\b\d{2}-\d{2}-\d{4}\b/g, '')
        .replace(/\b\d{2}\.\d{2}\.\d{2}\./g, '')
        .replace(/\b5\.\d\.\d{2}\.\d{2}\.\d{2}\.\d{2}\b/g, '')
        .replace(proofRegexGlobal, '')
        .replace(/\b(0|(\d{1,3}\.)+\d{3})\b/g, '')
        .replace(/\s+/g, ' ')
        .trim();
}

function match(text, regex) {
    return (String(text || '').match(regex) || [])[0] || '';
}

function setProgress(value) {
    $('progressBar').style.width = `${value}%`;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
}
</script>
@endpush
