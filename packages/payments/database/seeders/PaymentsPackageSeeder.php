<?php

use Illuminate\Database\Seeder;

class PaymentsPackageSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ensure seeders run in this particular order
        $this->call(PaymentGatewaySeeder::class);
        $this->call(PaymentGatewayMethodSeeder::class);
        $this->call(DepositMethodSeeder::class);
        $this->call(WithdrawalMethodSeeder::class);
    }
}
