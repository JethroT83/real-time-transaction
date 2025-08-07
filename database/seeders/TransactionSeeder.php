<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    private int $numberOfRecords = 1000;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::factory()
            ->count($this->numberOfRecords)
            ->create();
    }
}
