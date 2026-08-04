@extends('layouts.app')

@section('title', 'Settings - Generator Kuitansi BOSP')
@section('page_title', 'Settings')
@section('page_hint', 'Atur identitas sekolah, pejabat, checklist lampiran, dan master kode referensi manual.')

@section('content')
    <form method="post" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="grid">
        @csrf
        @method('put')

        <div class="panel">
            <div class="panel-head">
                <h2>Identitas Sekolah</h2>
            </div>
            <div class="panel-body form-grid">
                <x-field name="school[school_name]" label="Nama Sekolah" :value="$settings['school']['school_name']" />
                <x-field name="school[npsn]" label="NPSN" :value="$settings['school']['npsn']" />
                <x-field name="school[district]" label="Kecamatan" :value="$settings['school']['district']" />
                <x-field name="school[city]" label="Kabupaten/Kota" :value="$settings['school']['city']" />
                <x-field name="school[province]" label="Provinsi" :value="$settings['school']['province']" />
                <x-field name="school[source]" label="Sumber Dana" :value="$settings['school']['source']" />
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Pejabat</h2>
            </div>
            <div class="panel-body form-grid">
                <x-field name="sign[principal_name]" label="Nama Kepala Sekolah" :value="$settings['sign']['principal_name']" />
                <x-field name="sign[principal_nip]" label="NIP Kepala Sekolah" :value="$settings['sign']['principal_nip']" />
                <x-field name="sign[treasurer_name]" label="Nama Bendahara" :value="$settings['sign']['treasurer_name']" />
                <x-field name="sign[treasurer_nip]" label="NIP Bendahara" :value="$settings['sign']['treasurer_nip']" />
                <x-field name="sign[place]" label="Tempat Tanggal" :value="$settings['sign']['place']" />
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Tanda Tangan &amp; Stempel</h2>
            </div>
            <div class="panel-body form-grid">
                <div class="field">
                    <label>Mode Tanda Tangan</label>
                    <select name="sign[mode]">
                        <option value="ttd_stempel" @selected(($settings['sign']['mode'] ?? 'ttd_stempel') === 'ttd_stempel')>Tanda Tangan + Stempel</option>
                        <option value="qr_code" @selected(($settings['sign']['mode'] ?? 'ttd_stempel') === 'qr_code')>QR Code</option>
                        <option value="none" @selected(($settings['sign']['mode'] ?? 'ttd_stempel') === 'none')>Tanpa Tanda Tangan</option>
                    </select>
                </div>
                <div class="field">
                    <label>Tanda Tangan Kepala Sekolah</label>
                    @if (!empty($settings['sign']['principal_sign']))
                        <div class="img-preview"><img src="{{ asset('storage/' . $settings['sign']['principal_sign']) }}"
                                alt="TTD Kepsek" style="max-height:80px"></div>
                    @endif
                    <input type="file" name="sign_principal_sign_file" accept="image/*">
                    <input type="hidden" name="sign[principal_sign]"
                        value="{{ $settings['sign']['principal_sign'] ?? '' }}">
                </div>
                <div class="field">
                    <label>Tanda Tangan Bendahara</label>
                    @if (!empty($settings['sign']['treasurer_sign']))
                        <div class="img-preview"><img src="{{ asset('storage/' . $settings['sign']['treasurer_sign']) }}"
                                alt="TTD Bendahara" style="max-height:80px"></div>
                    @endif
                    <input type="file" name="sign_treasurer_sign_file" accept="image/*">
                    <input type="hidden" name="sign[treasurer_sign]"
                        value="{{ $settings['sign']['treasurer_sign'] ?? '' }}">
                </div>
                <div class="field">
                    <label>Stempel Sekolah</label>
                    @if (!empty($settings['sign']['school_stamp']))
                        <div class="img-preview"><img src="{{ asset('storage/' . $settings['sign']['school_stamp']) }}"
                                alt="Stempel" style="max-height:80px"></div>
                    @endif
                    <input type="file" name="sign_school_stamp_file" accept="image/*">
                    <input type="hidden" name="sign[school_stamp]" value="{{ $settings['sign']['school_stamp'] ?? '' }}">
                </div>
                <div class="field">
                    <label>QR Code Kepala Sekolah</label>
                    @if (!empty($settings['sign']['principal_qr']))
                        <div class="img-preview"><img src="{{ asset('storage/' . $settings['sign']['principal_qr']) }}"
                                alt="QR Kepsek" style="max-height:80px"></div>
                    @endif
                    <input type="file" name="sign_principal_qr_file" accept="image/*">
                    <input type="hidden" name="sign[principal_qr]" value="{{ $settings['sign']['principal_qr'] ?? '' }}">
                </div>
                <div class="field">
                    <label>QR Code Bendahara</label>
                    @if (!empty($settings['sign']['treasurer_qr']))
                        <div class="img-preview"><img src="{{ asset('storage/' . $settings['sign']['treasurer_qr']) }}"
                                alt="QR Bendahara" style="max-height:80px"></div>
                    @endif
                    <input type="file" name="sign_treasurer_qr_file" accept="image/*">
                    <input type="hidden" name="sign[treasurer_qr]" value="{{ $settings['sign']['treasurer_qr'] ?? '' }}">
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Template dan Checklist</h2>
            </div>
            <div class="panel-body form-grid">
                <x-field name="template[fund_name]" label="Nama Dana" :value="$settings['template']['fund_name']" class="full" />
                <div class="field">
                    <label>Checklist Umum</label>
                    <textarea name="template[general_checklist]">{{ old('template.general_checklist', $settings['template']['general_checklist']) }}</textarea>
                </div>
                <div class="field">
                    <label>Checklist Honor</label>
                    <textarea name="template[honor_checklist]">{{ old('template.honor_checklist', $settings['template']['honor_checklist']) }}</textarea>
                </div>
                <div class="field">
                    <label>Checklist SIPLah/ATK</label>
                    <textarea name="template[siplah_checklist]">{{ old('template.siplah_checklist', $settings['template']['siplah_checklist']) }}</textarea>
                </div>
                <x-field name="template[note]" label="Catatan Lampiran" :value="$settings['template']['note']" />
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Export PDF</h2>
            </div>
            <div class="panel-body form-grid">
                <div class="field">
                    <label>Layout Kuitansi</label>
                    <select name="export[layout]">
                        <option value="v1" @selected(($settings['export']['layout'] ?? 'v1') === 'v1')>Layout V1 (Klasik)</option>
                        <option value="v2" @selected(($settings['export']['layout'] ?? 'v1') === 'v2')>Layout V2 (Modern)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Master Kode Referensi</h2>
            </div>
            <div class="panel-body form-grid">
                <div class="field full">
                    <label>Kode Program</label>
                    <textarea name="program_codes_text" placeholder="06 = Dukungan operasional dan layanan sekolah">{{ old('program_codes_text', collect($settings['program_codes'])->map(fn($name, $code) => $code . ' = ' . $name)->implode("\n")) }}</textarea>
                </div>
                <div class="field full">
                    <label>Kode Kegiatan</label>
                    <textarea name="activity_codes_text" placeholder="06.07.05. = Pembayaran jasa internet">{{ old('activity_codes_text', collect($settings['activity_codes'])->map(fn($name, $code) => $code . ' = ' . $name)->implode("\n")) }}</textarea>
                </div>
                <div class="field full">
                    <label>Kode Rekening dan Rincian</label>
                    <textarea name="account_codes_text"
                        placeholder="5.1.02.02.01.00 63 = Belanja Kawat/Faksimili/Internet/TV Berlangganan">{{ old('account_codes_text', collect($settings['account_codes'])->map(fn($name, $code) => $code . ' = ' . $name)->implode("\n")) }}</textarea>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Simpan Settings</h2>
            </div>
            <div class="panel-body actions">
                <button class="btn good" type="submit"><i class="fa-solid fa-floppy-disk"></i>Simpan Settings</button>
            </div>
        </div>
    </form>
@endsection
