<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pejabat;
use App\Models\KategoriSurat;
use App\Models\PenomoranSurat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ================= ADMIN =================
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin Desa',
                'nik'      => '3201234567890002',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
            ]
        );

        // ================= PEJABAT =================
        Pejabat::updateOrCreate(
            ['nip' => '198812122010011001'],
            ['nama' => 'Budi Santoso', 'jabatan' => 'Kepala Desa']
        );

        Pejabat::updateOrCreate(
            ['nip' => '198705102011012002'],
            ['nama' => 'Siti Aminah', 'jabatan' => 'Sekretaris Desa']
        );

        // ================= KATEGORI =================
        $kategoris = [
            'SKU'  => 'Surat Keterangan Usaha',
            'SKD'  => 'Surat Keterangan Domisili',
            'SKTM' => 'Surat Keterangan Tidak Mampu',
        ];

        $kategoriModels = [];
        foreach ($kategoris as $kode => $nama) {
            $kategoriModels[$kode] = KategoriSurat::updateOrCreate(
                ['kode_surat' => $kode],
                ['nama_kategori' => $nama]
            );
        }

        // ================= PENOMORAN =================
        // Pake map biar gak repetitif
        $penomoran = [
            'SKU'  => ['nomor' => 12, 'format' => '470/{nomor}/SKU/{bulan}/{tahun}'],
            'SKD'  => ['nomor' => 7,  'format' => '470/{nomor}/SKD/{bulan}/{tahun}'],
            'SKTM' => ['nomor' => 3,  'format' => '470/{nomor}/SKTM/{bulan}/{tahun}'],
        ];

        foreach ($penomoran as $kode => $data) {
            PenomoranSurat::updateOrCreate(
                ['id_kategori' => $kategoriModels[$kode]->id],
                [
                    'nomor_terakhir' => $data['nomor'],
                    'format_nomor'   => $data['format'],
                ]
            );
        }
    }
}