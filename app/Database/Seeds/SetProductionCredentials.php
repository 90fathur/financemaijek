<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SetProductionCredentials extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $adminPassword   = password_hash('Maijek#2026!Admin', PASSWORD_BCRYPT);
        $financePassword = password_hash('Maijek#Finance2026!', PASSWORD_BCRYPT);

        // Update atau Buat Akun Super Admin
        $admin = $db->table('users')->where('username', 'admin')->get()->getRowArray();
        if ($admin) {
            $db->table('users')->where('username', 'admin')->update([
                'password'   => $adminPassword,
                'role'       => 'superadmin',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('users')->insert([
                'nama'        => 'Super Administrator Maijek',
                'username'    => 'admin',
                'email'       => 'admin@maijek.id',
                'password'    => $adminPassword,
                'role'        => 'superadmin',
                'investor_id' => null,
                'avatar'      => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        // Update atau Buat Akun Staff Finance
        $finance = $db->table('users')->where('username', 'finance')->get()->getRowArray();
        if ($finance) {
            $db->table('users')->where('username', 'finance')->update([
                'password'   => $financePassword,
                'role'       => 'admin',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('users')->insert([
                'nama'        => 'Staff Finance & Accounting',
                'username'    => 'finance',
                'email'       => 'finance@maijek.id',
                'password'    => $financePassword,
                'role'        => 'admin',
                'investor_id' => null,
                'avatar'      => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        // Bersihkan seluruh user ber-role investor dummy jika masih tersisa
        $db->query("DELETE FROM users WHERE role = 'investor' OR investor_id IS NOT NULL");
    }
}
