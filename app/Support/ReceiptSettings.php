<?php

namespace App\Support;

use App\Models\AppSetting;

class ReceiptSettings
{
    public const MONTHS = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public static function get(): array
    {
        $defaults = self::defaults();
        $saved = AppSetting::query()->where('key', 'receipt')->value('value');

        return array_replace_recursive($defaults, is_array($saved) ? $saved : []);
    }

    public static function save(array $value): void
    {
        AppSetting::query()->updateOrCreate(
            ['key' => 'receipt'],
            ['value' => array_replace_recursive(self::defaults(), $value)]
        );
    }

    public static function defaults(): array
    {
        return [
            'school' => [
                'school_name' => 'SDN Uji Coba SIBUK BOS',
                'npsn' => '20542535',
                'district' => 'Kecamatan Kandangan',
                'city' => 'Kabupaten Pasuruan',
                'province' => 'Jawa Timur',
                'source' => 'BOSP Reguler',
            ],
            'sign' => [
                'principal_name' => 'Ridwan',
                'principal_nip' => '10000000 200000 1 000',
                'treasurer_name' => 'Nama Bendahara',
                'treasurer_nip' => '10000000 200000 1 000',
                'place' => 'Kecamatan Kandangan',
            ],
            'template' => [
                'fund_name' => 'BANTUAN OPERASIONAL SATUAN PENDIDIKAN ( BOSP )',
                'general_checklist' => "Bukti Daftar Penerimaan\nBukti Transfer Pembayaran\nDokumen Pendukung Lain",
                'honor_checklist' => "Bukti Daftar Penerimaan\nBukti Transfer Pembayaran\nSK Pengangkatan\nDaftar Hadir Bulan Berjalan",
                'siplah_checklist' => "Bukti Transaksi SIPLAH\nBukti Transfer Pembayaran\nDokumen Pendukung Lain",
                'note' => 'Pastikan dokumen lengkap dan sah.',
            ],
            'program_codes' => [
                '06' => 'Dukungan operasional dan layanan sekolah',
                '07' => 'Pembayaran honorarium dan jasa tenaga sekolah',
            ],
            'activity_codes' => [
                '06.05.08.' => 'Pembelian Bahan Habis Pakai untuk mendukung pembelajaran dan administrasi sekolah (termasuk ATK, Tinta Printer, Kabel Ekstension, dsb)',
                '06.07.01.' => 'Pembayaran daya listrik',
                '06.07.04.' => 'Pembayaran biaya telepon',
                '06.07.05.' => 'Pembayaran jasa internet',
                '07.12.01.' => 'Pembayaran honor Guru/Pendidik',
                '07.12.02.' => 'Pembayaran honor Tenaga Kependidikan (selain pendidik)',
                '07.12.03.' => 'Pembayaran Honor tenaga administrasi',
                '07.12.04.' => 'Pembayaran honor Tenaga Penunjang atau pelaksana',
            ],
            'account_codes' => [
                '5.1.02.01.01.00 24' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Tulis Kantor',
                '5.1.02.02.01.00 08' => 'Belanja Jasa Tenaga Administrasi',
                '5.1.02.02.01.00 13' => 'Belanja Jasa Tenaga Pendidikan',
                '5.1.02.02.01.00 29' => 'Belanja Jasa Tenaga Ahli',
                '5.1.02.02.01.00 30' => 'Belanja Jasa Tenaga Kebersihan',
                '5.1.02.02.01.00 31' => 'Belanja Jasa Tenaga Keamanan',
                '5.1.02.02.01.00 59' => 'Belanja Tagihan Telepon',
                '5.1.02.02.01.00 61' => 'Belanja Tagihan Listrik',
                '5.1.02.02.01.00 63' => 'Belanja Kawat/Faksimili/Internet/TV Berlangganan',
            ],
        ];
    }
}
