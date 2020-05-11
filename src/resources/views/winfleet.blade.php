@extends('web::layouts.grids.12')

@section('title', trans('winfleet::winfleet.summary'))
@section('page_header', trans('winfleet::winfleet.summary'))

@push('head')
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script> 
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.10.20/b-1.6.1/sl-1.3.1/datatables.min.css"/>

    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.10.20/b-1.6.1/sl-1.3.1/datatables.min.js"></script>
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet"> 
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
@endpush

@inject('Operation', 'Seat\Kassie\Calendar\Models\Operation')
@inject('Integration', 'Seat\Notifications\Models\Integration')
@inject('Carbon', 'Carbon\Carbon')

@section('full')

<div id="response"></div>

<div class="row">
    <div class="col-md-6">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Operations</h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" id="formkill" method="POST" action="{{ route('winfleet.view') }}">
              {{ csrf_field() }}
                <div class="form-group">
                    <label for="winfleet-winners" class="col-sm-3 control-label">
                        {{ trans('winfleet::winfleet.winners') }}
                    </label>
                    <div class="col-sm-3">
                        <select name="winners_count" id="winfleet-winners" class="form-control" style="width: 100%;">
                            @for ($i=0;$i<setting('winfleet.max_winners', true);$i++)
                            <option value={{ $i }} {{ $i==2?'selected':'' }}>{{ $i+1 }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                @if(setting('winfleet.settings.slack_integration', true) == true)
                <div class="form-group">
                    <label for="create-winfleet-channel" class="col-sm-3 control-label">
                        {{ trans('winfleet::winfleet.operation') }}
                    </label>
                    <div class="col-sm-3">
                        <select name="integration_id" id="create-winfleet-channel" style="width: 100%;">
                            <option value=""></option>
                            @php $notification_channels = $Integration::where('type', 'slack')->get();
                            @foreach($Integration::where('type', 'slack')->get() as $channel)
                            <option value="{{ $channel->id }}">{{ $channel->name }}</option>                            
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="form-group">
                    <label for="create-winfleet-operation" class="col-sm-3 control-label">
                        {{ trans('winfleet::winfleet.operation') }}
                    </label>
                    <div class="col-sm-6">
                        <select name="operation_id" id="create-winfleet-operation" class="form-control" style="width: 100%;">
                            <option disabled selected value>{{ trans('winfleet::winfleet.select_fleet') }}</option>
                            @foreach($operations as $operation)
                                <option value="{{ $operation->id }}" 
                                    @if (isset($op))
                                        @if ($op->id == $operation->id)
                                            selected
                                        @endif
                                    @endif
                                >{{ $Carbon::parse($operation->start_at)->format('d.m.Y')}} {{ $operation->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
@if (1==2)
                <button type="submit" class="btn btn-default">{{trans('winkill::winkill.win')}}</button>
@endif
            </form>
            @if(auth()->user()->has('winfleet.update',false))
              <button onclick="award()" id="award_btn" class="btn btn-default">{{trans('winkill::winkill.win')}}</button>
            @endif
              <button onclick="sortTable()" id="sort_btn" class="btn btn-default">Sort</button>
              <button onclick="reload()" id="reload_btn" class="btn btn-default">Reload</button>
            @if(auth()->user()->has('winfleet.update',false))
              <button id="reset_btn" class="btn btn-default" disabled>Reset</button>
            @endif
        </div>
      </div>

    </div>
    <div class="col-md-6" id="winners_box">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Winners</h3>
        </div>
          <div class="panel-body">
            <table class="table table-condensed" id="winners">
                <thead>
                    <tr>
                        <th>Place</th>
                        <th>{{ trans_choice('web::seat.character', 1) }}</th>
                        <th>{{ trans_choice('web::seat.corporation', 1) }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>             
          </div>
      </div>
    </div>
</div>

@include('winfleet::fleet.member')

@endsection

@push('javascript')
  @include('web::includes.javascript.id-to-name')
  <script type="text/javascript">
//    $('#winners_box').hide();
    $('#award_btn').attr("disabled", true);
    $('#sort_btn').attr("disabled", true);
    $('#reload_btn').attr("disabled", true);
    $('#create-winfleet-operation').change(function () {
        $('#confirmed').DataTable().clear().destroy();
        $('#winners').DataTable().clear().destroy();
        table = $('#confirmed').DataTable({
//            "ajax": "/calendar/lookup/confirmed?id=" + $('#create-winfleet-operation').find(":selected").val(),
            "ajax": "/winfleet/operation?id=" + $('#create-winfleet-operation').find(":selected").val(),
            "ordering": true,
            "info": false,
            "processing": true,
            "paging": false,
            "order": [[ 1, "asc" ]],
            "aoColumnsDefs": [
                { orderable: false, targets: "no-sort" }
            ],
            'fnDrawCallback': function () {
                $(document).ready(function () {
                    ids_to_names();                   
                    $('#sort_btn').attr("disabled", false);
                    $('#reload_btn').attr("disabled", false);
                });
            },
            "columns": [
                { data: 'character.character_id'},
                { data: 'character.corporation_id'},
                { data: 'type.typeID'},
                { data: 'type.group.groupName'},
                { data: 'character_id', visible: false},
                { "data": null,
                  "defaultContent": '<button class="deleted btn btn-danger">Delete</button>'
                },
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).attr('data-id', data.character_id);
//                $(row).find('td:eq(0)').attr('data-search', data.character.character_id);
            }
        });
        
        winners = $('#winners').DataTable({
//            "ajax": "/calendar/lookup/confirmed?id=" + $('#create-winfleet-operation').find(":selected").val(),
            "ajax": "/winfleet/awards?id=" + $('#create-winfleet-operation').find(":selected").val(),
            "info": false,
            "processing": true,
            "searching": false,
            "paging": false,
            "ordering": false,
            "order": [[ 0, "asc" ]],
            "aoColumnsDefs": [
                { orderable: false, targets: "no-sort" }
            ],
            'fnDrawCallback': function () {
                if ( this.api().data().any() ) {
                    $('#award_btn').attr("disabled", true);
                    $('#reset_btn').attr("disabled", false);
                } else {
                    $('#award_btn').attr("disabled", false);
                    $('#reset_btn').attr("disabled", true);
                }
                $(document).ready(function () {
                    ids_to_names();                   
                });
            },
            "columns": [
                { data: 'place'},
                { data: 'character.character_id'},
                { data: 'character.corporation_id'},
                { data: 'character_id', visible: false},
                { data: 'status', visible: false},
                { "data": null,
                  "render": function ( data, type, row, meta ) {
                    btn = '';
                @if(auth()->user()->has('winfleet.status',false))
                    d = ' class="win btn btn-success"';
                    if(row.status === 'paid') d = ' class="paid btn btn-info" disabled="disabled"';
                    btn = '<button '+d+'>'+row.status+'</button>';
                @endif
                    return btn;
                  }  
//                  "defaultContent": "<button class='win'>New</button>"
                },
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).attr('data-id', data.character_id);
                $(row).attr('data-place', data.place);
                $(row).find('.win').html(data.status);
//                $(row).find('td:eq(0)').attr('data-search', data.character.character_id);
            }
        });
        
    });

    $('#confirmed tbody').on( 'click', '.deleted',function () {
        $(this).closest('tr').remove();
//        table.row( $(this).parents('tr') ).remove().draw();
    } );

    $('#winners tbody').on( 'click', '.win',function () {
        var btn = this;
        $.ajax({
          url: "{{route('winfleet.status')}}",
          type: "POST",
          data: { 
              pl:$(this).closest('tr').attr('data-place'),
              id:$(this).closest('tr').attr('data-id'),
              op:$('#create-winfleet-operation').find(":selected").val()
          },
          success: function(data) { 
            if(data.error) {
              $('#response').html(data.error).css({'color': 'red', 'text-align': 'center'});
              $('#response').show().delay(5000).fadeOut();          
            } else {
              $(btn).removeClass("win btn btn-success").addClass("paid btn btn-info");
              $(btn).attr("disabled", true);
              $(btn).text(data.ret);
            }
          }
        });
    } );
    $('#reset_btn').click(function(){
//            var btn = this;
        $.ajax({
          url: "{{route('winfleet.delete')}}",
          type: "POST",
          data: { 
              op:$('#create-winfleet-operation').find(":selected").val()
          },
          success: function(data) { 
            if(data.error) {
              $('#response').html(data.error).css({'color': 'red', 'text-align': 'center'});
              $('#response').show().delay(5000).fadeOut();          
            } else {
//                  $(btn).removeClass("win btn btn-success").addClass("paid btn btn-info");
              $('#award_btn').attr("disabled", false);
//                  $(btn).text(data);
            }
            reload();
          }
        });                
    });


function reload() {
    if ( $.fn.DataTable.isDataTable( '#confirmed' ) ) {
        table.ajax.reload();
    }
    if ( $.fn.DataTable.isDataTable( '#winners' ) ) {
        winners.ajax.reload();
    }
}

function award() {
    $('#winners_box').show();
    sortTable();
    table1 = document.getElementById("confirmed").getElementsByTagName("tbody")[0];
    table2 = document.getElementById("winners").getElementsByTagName("tbody")[0];
    rowsCollection = table1.querySelectorAll("tr");
    rows = Array.from(rowsCollection);
    $("#winners tbody tr").remove(); 
    win_count = $('#winfleet-winners').find(":selected").val();
    rows.some(function (arrayItem,i) {
        if (i > win_count) {
            return true;
        } else {
            clone = arrayItem.cloneNode(true);
            clone.deleteCell(4);
            clone.deleteCell(3);
            clone.deleteCell(2);
            newcell = clone.insertCell(0);
            newcell.innerHTML = i + 1;
            clone.setAttribute('data-place', i + 1);
            btn = clone.insertCell(3);
            btn.innerHTML = "<button class='win btn btn-success'>win</button>";
            table2.appendChild(clone);
            return false;
        }
    });
    rowsCollection = table2.querySelectorAll("tr[data-id]");
    rows = Array.from(rowsCollection);
    var ids = [];
    for (const row of rows) {
        ids.push(row.getAttribute('data-id'));
    }
    $.ajax({
      url: "{{route('winfleet.save')}}",
      type: "POST",
      data: {ids:JSON.stringify(ids),op:$('#create-winfleet-operation').find(":selected").val()},
      success: function(data){
          if(data.error) {
            $('#response').html(data.error).css({'color': 'red', 'text-align': 'center'});
            $('#response').show().delay(5000).fadeOut();          
          }
      },
      error: function(data){
      }
    });    
}

function sortTable() {
  //get the parent table for convenience
  let table1 = document.getElementById("confirmed").getElementsByTagName("tbody")[0];

  //1. get all rows
  let rowsCollection = table1.querySelectorAll("tr");

  //2. convert to array
  let rows = Array.from(rowsCollection);
//    .slice(1); //skip the header row

  //3. shuffle
  shuffleArray(rows);

  //4. add back to the DOM
  $("#confirmed tbody tr").remove(); 
  for (const row of rows) {
    table1.appendChild(row);
  }
}


/**
 * Randomize array element order in-place.
 * Using Durstenfeld shuffle algorithm.
 * from: https://stackoverflow.com/questions/2450954/how-to-randomize-shuffle-a-javascript-array/12646864#12646864
 */
function shuffleArray(array) {
  for (var i = array.length - 1; i > 0; i--) {
    var j = Math.floor(Math.random() * (i + 1));
    var temp = array[i];
    array[i] = array[j];
    array[j] = temp;
  }
}

  </script>
@endpush
