<?php

namespace Seat\Akturis\WinFleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Kassie\Calendar\Models\Operation;
use Seat\Notifications\Models\Integration;
use Seat\Web\Models\User;

class WinFleetAward extends Model
{
    use Notifiable;

    public $username = null;
    
    protected $table = 'winfleet_awards';
      
    public $incrementing = true;
    
    protected $fillable = ['status','character_id','user_id'];
      
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function character()
    {
        return $this->belongsTo(CharacterInfo::class,'character_id','character_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
