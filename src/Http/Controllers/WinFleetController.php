<?php

namespace Seat\Akturis\WinFleet\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Seat\Kassie\Calendar\Models\Operation;
use Seat\Web\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WinFleetController extends Controller
{
   
    public $period = 14;

    public function __construct() {
        $this->period = setting('winfleet.period', true)?:14;
    }
    
    public function index(Request $request) {
       
        $operations = Operation::where('start_at','>=',Carbon::now()->subDays($this->period))->orderBy('start_at', 'desc')->get();
        return view('winfleet::winfleet',['operations' => $operations]);
    }
    
    public function update(Request $request) {
        $op = Operation::find($request->get('operation_id'));
        return view('winfleet::winfleet',['op' => $op ]);
    }
          
}

