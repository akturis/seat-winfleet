<?php

namespace Seat\Akturis\WinFleet\database\seeds;

use Illuminate\Database\Seeder;


class WinFleetSettingsTableSeeder extends Seeder
{
    public function run()
    {
        setting([
            'winfleet.period',
            14,
        ], true);

        setting([
            'winfleet.max_winners',
            3,
        ], true);
    }
}
