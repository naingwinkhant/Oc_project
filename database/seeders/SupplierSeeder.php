<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Metro Fresh Distribution', 'code' => 'SUP-0001', 'contact_name' => 'Ricardo Lim', 'phone' => '+63 917 111 0001', 'email' => 'orders@metrofresh.ph', 'address' => '12 Market Ave, Quezon City'],
            ['name' => 'Davao Catch & Carry', 'code' => 'SUP-0002', 'contact_name' => 'Liza Melinda', 'phone' => '+63 917 111 0002', 'email' => 'sales@davaocatch.ph', 'address' => '88 Sasa Wharf, Davao City'],
            ['name' => 'Golden Harvest Mills', 'code' => 'SUP-0003', 'contact_name' => 'Teodoro Uy', 'phone' => '+63 917 111 0003', 'email' => 'sales@goldenharvest.ph', 'address' => '4 Bulacan Milling Road, Bulacan'],
            ['name' => 'Dairy Queen Philippines', 'code' => 'SUP-0004', 'contact_name' => 'Mae Villanueva', 'phone' => '+63 917 111 0004', 'email' => 'corporate@dairyqueen.ph', 'address' => '20 Aurora Blvd, Mandaluyong'],
            ['name' => 'Bakers Row Bakery', 'code' => 'SUP-0005', 'contact_name' => 'Carlo Reyes', 'phone' => '+63 917 111 0005', 'email' => 'hello@bakersrow.ph', 'address' => '31 Katipunan Ave, Quezon City'],
            ['name' => 'San Miguel Agri Foods', 'code' => 'SUP-0006', 'contact_name' => 'Ana Singson', 'phone' => '+63 917 111 0006', 'email' => 'agri@sanmiguelfoods.ph', 'address' => '7 Ortigas Center, Pasig'],
            ['name' => 'Selecta Frozen Products', 'code' => 'SUP-0007', 'contact_name' => 'Paolo Dizon', 'phone' => '+63 917 111 0007', 'email' => 'orders@selectafrozen.ph', 'address' => '55 Gilmore Ave, Quezon City'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['code' => $supplier['code']],
                [...$supplier, 'is_active' => true],
            );
        }
    }
}
