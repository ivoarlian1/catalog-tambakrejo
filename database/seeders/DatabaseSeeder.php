<?php

namespace Database\Seeders;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@example.test',
        ], [
            'name' => 'Superadmin Tambakrejo',
            'password' => 'password',
            'role' => 'superadmin',
            'catalog_id' => null,
            'is_active' => true,
        ]);

        $samples = [
            [
                'name' => 'Warung Bu Siti',
                'type' => 'umkm',
                'sub_type' => 'Kuliner',
                'owner_name' => 'Siti Aminah',
                'description' => 'Warung makan rumahan dengan menu nasi pecel, ayam goreng, dan minuman tradisional.',
                'address' => 'Jl. Tambakrejo No. 10, Kelurahan Tambakrejo',
                'whatsapp' => '08123456789',
                'email' => 'warungbusiti@example.test',
                'instagram' => 'warungbusiti',
                'status' => 'published',
                'admin_email' => 'siti@example.test',
                'products' => [
                    ['name' => 'Nasi Pecel', 'price' => 15000, 'description' => 'Nasi pecel lengkap dengan peyek.'],
                    ['name' => 'Es Teh', 'price' => 4000, 'description' => 'Es teh manis dingin.', 'is_available' => true],
                    ['name' => 'Ayam Goreng', 'price' => 18000, 'description' => 'Ayam goreng bumbu kuning.', 'is_available' => false],
                ],
            ],
            [
                'name' => 'Butik Pesisir',
                'type' => 'umkm',
                'sub_type' => 'Fashion',
                'owner_name' => 'Rina Wulandari',
                'description' => 'Menjual busana muslim dan kain motif pesisir.',
                'address' => 'Jl. Pantai Tambakrejo No. 5, Kelurahan Tambakrejo',
                'whatsapp' => '082198765432',
                'phone' => '0315551234',
                'tiktok' => 'butikpesisir',
                'status' => 'published',
                'admin_email' => 'rina@example.test',
                'products' => [
                    ['name' => 'Gamis Pesisir', 'price' => 175000, 'description' => 'Gamis katun motif ombak.'],
                ],
            ],
            [
                'name' => 'PAUD Melati',
                'type' => 'education',
                'sub_type' => 'PAUD',
                'responsible_person' => 'Ibu Lestari',
                'description' => 'Pendidikan anak usia dini dengan kegiatan bermain sambil belajar.',
                'address' => 'Jl. Pendidikan No. 2, Kelurahan Tambakrejo',
                'phone' => '0315552001',
                'email' => 'paudmelati@example.test',
                'status' => 'published',
                'admin_email' => 'lestari@example.test',
            ],
            [
                'name' => 'SDN Tambakrejo 1',
                'type' => 'education',
                'sub_type' => 'SD',
                'responsible_person' => 'Bapak Hartono',
                'description' => 'Sekolah dasar negeri di wilayah Kelurahan Tambakrejo.',
                'address' => 'Jl. Sekolah No. 1, Kelurahan Tambakrejo',
                'phone' => '0315552002',
                'status' => 'published',
                'admin_email' => 'hartono@example.test',
            ],
            [
                'name' => 'Posyandu Mawar',
                'type' => 'health',
                'sub_type' => 'Posyandu',
                'responsible_person' => 'Bidan Ani',
                'description' => 'Layanan kesehatan ibu dan anak, imunisasi, dan pemantauan gizi.',
                'address' => 'Jl. Kesehatan No. 8, Kelurahan Tambakrejo',
                'whatsapp' => '081122334455',
                'status' => 'published',
                'admin_email' => 'ani@example.test',
            ],
            [
                'name' => 'Apotek Sehat Pesisir',
                'type' => 'health',
                'sub_type' => 'Apotek',
                'responsible_person' => 'Apt. Budi Santoso',
                'description' => 'Apotek dengan layanan konsultasi obat sederhana.',
                'address' => 'Jl. Raya Tambakrejo No. 21, Kelurahan Tambakrejo',
                'phone' => '0315553003',
                'status' => 'draft',
                'admin_email' => 'budi@example.test',
            ],
            [
                'name' => 'Balai Kelurahan Tambakrejo',
                'type' => 'public_facility',
                'sub_type' => 'Balai Kelurahan',
                'responsible_person' => 'Lurah Tambakrejo',
                'description' => 'Pusat pelayanan administrasi dan kegiatan warga kelurahan.',
                'address' => 'Jl. Kelurahan No. 1, Kelurahan Tambakrejo',
                'phone' => '0315554001',
                'email' => 'kelurahan@example.test',
                'status' => 'published',
                'admin_email' => 'lurah@example.test',
            ],
            [
                'name' => 'Masjid Al-Hidayah',
                'type' => 'public_facility',
                'sub_type' => 'Tempat Ibadah',
                'responsible_person' => 'Takmir Masjid',
                'description' => 'Masjid untuk ibadah dan kegiatan keagamaan warga.',
                'address' => 'Jl. Ibadah No. 3, Kelurahan Tambakrejo',
                'status' => 'published',
                'admin_email' => 'takmir@example.test',
            ],
        ];

        foreach ($samples as $sample) {
            $products = $sample['products'] ?? [];
            $adminEmail = $sample['admin_email'];
            unset($sample['products'], $sample['admin_email']);
            $catalog = Catalog::query()
                ->where('name', $sample['name'])
                ->where('type', $sample['type'])
                ->first();

            if ($catalog === null) {
                $sample['slug'] = Catalog::uniqueSlugFromName($sample['name']);
                $catalog = Catalog::query()->create($sample);
            } else {
                $catalog->update($sample);
            }

            User::query()->updateOrCreate([
                'email' => $adminEmail,
            ], [
                'name' => 'Admin '.$catalog->name,
                'password' => 'password',
                'role' => 'catalog_admin',
                'catalog_id' => $catalog->id,
                'is_active' => true,
            ]);

            foreach ($products as $product) {
                $catalog->products()->updateOrCreate([
                    'name' => $product['name'],
                ], [
                    'name' => $product['name'],
                    'description' => $product['description'] ?? null,
                    'price' => $product['price'],
                    'is_available' => $product['is_available'] ?? true,
                ]);
            }
        }
    }
}
