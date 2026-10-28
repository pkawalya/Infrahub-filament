<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class MohestProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('infrahub:setup-mohest-projects', [
            '--password' => 'InfraHub@2026!',
        ]);

        $this->command->info(Artisan::output());
    }
}
