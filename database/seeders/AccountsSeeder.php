<?php

namespace Database\Seeders;

use App\Infrastructure\Laravel\Models\Account;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $distributor = Account::factory()->create([
            'type' => 'distributor',
            'name' => fake()->company(),
            'email' => 'distributor@sparring.catchall.infra.im'
        ]);

        foreach (range(1, 5) as $i) {
            Account::factory(5)->create([
                'parent_id' => $distributor->id
            ]);

            $reseller = Account::factory()->create([
                'type' => 'reseller',
                'parent_id' => $distributor->id,
                'email' => "reseller_{$i}@sparring.catchall.infra.im"
            ]);

            Account::factory(10)->create([
                'parent_id' => $reseller->id
            ]);
        }
    }
}
