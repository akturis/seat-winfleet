<?php

namespace Seat\Akturis\WinFleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Kassie\Calendar\Models\Operation;
use Seat\Notifications\Models\Integration;
use Seat\Web\Models\User;
use Seat\Akturis\WinFleet\Models\WinFleetAward;

class WinFleetOperation extends Operation
{
    use Notifiable;
    
//    protected $table = 'winfleet_awards';
//    protected $username = null;

    public function setUsernameAttribute($value) {
        $this->attributes['username'] = $value;
    }

    public function getUsernameAttribute($value) {
        return ucfirst($value); 
    }
    
    public function getSlackIntegrationAttribute($value)
    {
        if(setting('winfleet.integration', true)!='') {
            $integration = Integration::find(setting('winfleet.integration', true));
            return $integration->id;
        }
        return null;
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function places()
    {
        return $this->hasMany(WinFleetAward::class,'operation_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id', 'id');
    }
    
    public function routeNotificationForSlack()
    {
        if (setting('winfleet.integration', true)) {
            $integration = Integration::find(setting('winfleet.integration', true));
            return $integration->settings['url'];
        }
    }
    
}
