<?php

namespace Seat\Akturis\WinFleet\Observers;

use Illuminate\Support\Facades\Notification;
use Seat\Akturis\WinFleet\Models\WinFleetAward;
use Seat\Akturis\WinFleet\Notifications\AwardPosted;

class WinFleetAwardObserver
{
    public function updating(WinFleetAward $award)
    {
        if (setting('winfleet.integration', true))
            Notification::send($award, new AwardPosted());
    }

}
