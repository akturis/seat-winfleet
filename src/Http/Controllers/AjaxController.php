<?php

namespace Seat\Akturis\WinFleet\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Seat\Kassie\Calendar\Models\Operation;
use Illuminate\Support\Facades\DB;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Kassie\Calendar\Models\Pap;
use Seat\Akturis\WinFleet\Models\WinFleetAward;
use Seat\Akturis\WinFleet\Models\WinFleetOperation;
use Seat\Akturis\WinFleet\Notifications\WinFleetOperationPosted;
use Warlof\Seat\Connector\Models\User as UserDiscord;
use Seat\Web\Models\User;

class AjaxController extends Controller
{


    public function __construct() {

    }
    
    public function getOperation(Request $request)
    {
        if(!$request->ajax()) {
            return '';
        }
        $confirmed = Pap::with([
                'character:character_id,name,corporation_id',
                'user:id,group_id',
                'user.group:id',
                'type:typeID,typeName,groupID',
                'type.group:groupID,groupName'
            ])
            ->where('operation_id', $request->input('id'))
            ->has('character')
            ->select('character_id', 'ship_type_id')
            ->get();

        return app('DataTables')::collection($confirmed)
            ->removeColumn('ship_type_id')                
            ->editColumn('character.character_id', function ($row) {
                return view('calendar::operation.includes.cols.confirmed.character', compact('row'))->render();
            })
            ->editColumn('character.corporation_id', function ($row) {
                return view('calendar::operation.includes.cols.confirmed.corporation', compact('row'))->render();
            })
            ->editColumn('type.typeID', function ($row) {
                return view('calendar::operation.includes.cols.confirmed.ship', compact('row'))->render();
            })
            ->rawColumns(['character.character_id', 'character.corporation_id', 'type.typeID'])
            ->make(true);
    }
    
    public function getAwards(Request $request)
    {
        if(!$request->ajax()) {
            return '';
        }
        $awards = WinFleetAward::with([
                'character:character_id,name,corporation_id',
                'user:id,group_id',
                'user.group:id',
            ])
            ->where('operation_id', $request->input('id'))
            ->select('place','character_id','status')
            ->get();

        return app('DataTables')::collection($awards)
            ->editColumn('character.character_id', function ($row) {
                return view('calendar::operation.includes.cols.confirmed.character', compact('row'))->render();
            })
            ->editColumn('character.corporation_id', function ($row) {
                return view('calendar::operation.includes.cols.confirmed.corporation', compact('row'))->render();
            })
            ->rawColumns(['character.character_id', 'character.corporation_id'])
            ->make(true);
    }

    public function getAwards2(Request $request)
    {
        $op = WinFleetOperation::find($request->input('id'));

        $awards = WinFleetAward::with([
                'character:character_id,name,corporation_id',
                'user:id,group_id',
                'user.group:id',
            ])
            ->where('operation_id', $request->input('id'))
            ->has('character')
            ->select('place','character_id','status')
            ->get();
        $awards->username = auth()->user()->name;
        $op->username = auth()->user()->name;
        dd($awards,$awards->username,$op,$op->places,$op->username);

        $awards = WinFleetOperation::find($request->input('id'));
        $content = "**Поздравим победителей розыгрыша наград!**\n";
        $content .= "Флот ".$awards->title."\n";
        
        $content .= $awards->places->map(function($item){
            $user = User::find($item->character->character_id);
            $user_discord = UserDiscord::where('group_id',$user->group->id)->first();
            $connector_id = $user_discord?$user_discord->connector_id:null;
            $winner = $connector_id?'<@!'.$connector_id.'> / '.$item->character->name:$item->character->name;
            return $item->place.'. '.$winner;
        })->implode("\n");
        dd($content);
    }
    
    public function updateWinners(Request $request){
        $ids = json_decode($request->get('ids'));
        $op = $request->get('op');
        $operation  = Operation::find($op);
        $place = 1;
        $ret_ids = [];
        if (auth()->user()->has('winfleet.update',false)) {
            $win_op = WinFleetOperation::find($op);
            $awards = [];
            foreach($ids as $key => $id) {
                $character = CharacterInfo::find($id);
                if($character) {
                    $award = new WinFleetAward();
                    $award->place = $place++;
                    $award->status = 'win';
                    $award->character_id = $character?$character->character_id:$id;
                    $award->user_id = $character?$character->character_id:$id;
                    $award->operation_id = $operation->id;
                    $awards[] = $award;
                    $ret_ids[] = $id;                    
                }
            }
            $win_op->places()->saveMany($awards);
            
            if (auth()->user()->has('winfleet.update',false)) {
                $awards = WinFleetOperation::find($operation->id);
                $awards->username = auth()->user()->name;
                Notification::send($awards, new WinFleetOperationPosted());
            }
            
            return response()->json(['code' => 200, 'success' => 'Ok', 'ret' => $ret_ids]);
        } else {
            return response()->json(['code' => 401, 'error' => 'Need permission for save']);            
        }
    }

    public function deleteWinners(Request $request){
        $op = $request->get('op');
        if (auth()->user()->has('winfleet.update',false)) {
            $win_op = WinFleetOperation::find($op);
            $win_op->places()->delete();           
            return response()->json(['code' => 200, 'success' => 'Ok']);
        } else {
            return response()->json(['code' => 401, 'error' => 'Need permission for save']);            
        }
    }
    
    public function updateStatus(Request $request){
        $id = $request->get('id');
        $op = $request->get('op');
        $pl = $request->get('pl');
        if (auth()->user()->has('winfleet.status',false)) {
            $award = WinFleetAward::where('operation_id',$op)
                                    ->where('place',$pl)
                                    ->where('character_id',$id)->first();
            $award->status = 'paid';
            $award->username = auth()->user()->name;
            $update = $award->save();
//            return $update?'paid':'win';
            return response()->json(['code' => 200, 'success' => 'Ok', 'ret' => $update?'paid':'win']);
        } else {
            return response()->json(['code' => 401, 'error' => 'Need permission for update status']);
        }
    }
    
       
}

