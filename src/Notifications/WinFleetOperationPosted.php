<?php

namespace Seat\Akturis\WinFleet\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Warlof\Seat\Connector\Models\User as UserDiscord;
use Seat\Web\Models\User;
use Seat\Kassie\Calendar\Helpers\Helper;

class WinFleetOperationPosted extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($operation)
    {
        $fields = array();
        $url = url('/calendar/operation', [$operation->id]);
        $content = "**Поздравим победителей розыгрыша наград!**\n\n";
        $content .= "Призы будут выданы участникам флота:\n";        
        $content .= $operation->places->map(function($item){
            $user = User::find($item->character->character_id);
            $user_discord = UserDiscord::where('group_id',$user->group->id)->first();
            $connector_id = $user_discord?$user_discord->connector_id:null;
            $winner = $connector_id?'<@!'.$connector_id.'> / **'.$item->character->name.'**':'**'.$item->character->name.'**';
            return $item->place.'. '.$winner;
        })->implode("\n");

        $fields[trans('calendar::seat.starts_at')] = $operation->start_at->format('F j @ H:i EVE');
        $fields[trans('calendar::seat.duration')] = $operation->getDurationAttribute() ?
            $operation->getDurationAttribute() : trans('calendar::seat.unknown');
        $fields[trans('calendar::seat.fleet_commander')] = $operation->fc ? $operation->fc : trans('calendar::seat.unknown');
        
        return (new SlackMessage)
            ->success()
            ->from('AwardFleet', ':calendar:')
            ->content($content)
            ->attachment(function ($attachment) use($operation,$url,$fields) {
                $attachment->title($operation->title, $url)
                           ->fields($fields)
                           ->footer('Спасибо за участие во флотах! *'.$operation->username.'*');
            });
    }
}
