<?php

namespace Seat\Akturis\WinFleet\Observers;

use Illuminate\Support\Facades\Notification;
use Seat\Akturis\WinFleet\Models\WinFleetOperation;
use Seat\Akturis\WinFleet\Notifications\WinFleetOperationPosted;

class WinFleetOperationObserver
{
    public function updating(WinFleetOperation $operation)
    {
        if (setting('winfleet.integration', true))
            Notification::send($operation, new WinFleetOperationPosted());
    }

}
