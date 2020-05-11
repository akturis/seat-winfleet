<?php

namespace Seat\Akturis\WinFleet\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Warlof\Seat\Connector\Models\User as UserDiscord;
use Seat\Web\Models\User;

class AwardPosted extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($award)
    {
        $fields = array();
        $url = url('/calendar/operation', [$award->operation->id]);
        $content = "**Награда за участие во флоте** выслана ".$award->username."!\n\n";
        $user = User::find($award->character->character_id);
        $user_discord = UserDiscord::where('group_id',$user->group->id)->first();
        $connector_id = $user_discord?$user_discord->connector_id:null;
        $content .= "<@!".$connector_id."> / **".$award->character->name."** может ее забрать\n"; 
        
        $fields[trans('calendar::seat.starts_at')] = $award->operation->start_at->format('F j @ H:i EVE');
        $fields[trans('calendar::seat.duration')] = $award->operation->getDurationAttribute() ?
            $award->operation->getDurationAttribute() : trans('calendar::seat.unknown');
        $fields[trans('calendar::seat.fleet_commander')] = $award->operation->fc ? $award->operation->fc : trans('calendar::seat.unknown');
        
        return (new SlackMessage)
            ->success()
            ->from('AwardFleet', ':calendar:')
            ->content($content)
            ->attachment(function ($attachment) use($award,$url,$fields) {
                $attachment->title($award->operation->title, $url)
                           ->fields($fields)
                           ->footer('Спасибо за участие во флотах!');
            });
    }
}
