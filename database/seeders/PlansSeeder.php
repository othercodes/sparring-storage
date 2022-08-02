<?php

namespace Database\Seeders;

use App\Domain\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (($handle = fopen("resources/plans/plans.csv", "r")) !== FALSE) {
            $headers = [];
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (empty($headers)) {
                    $headers = array_map('trim', $data);
                    continue;
                }

                Plan::create(array_combine($headers, array_map('trim', $data)));
            }
            fclose($handle);
        }
    }
}
