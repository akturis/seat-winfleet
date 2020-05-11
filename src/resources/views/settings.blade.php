@extends('web::layouts.grids.12')

@section('title', trans_choice('winfleet::winfleet.settings', 0))
@section('page_header', trans_choice('winfleet::winfleet.settings', 0))

@inject('Integration', 'Seat\Notifications\Models\Integration')

@section('full')

  <div class="row">

    <div class="col-md-3">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Common</h3>
        </div>
        <div class="panel-body">
          <form method="post" id="winfleet-setup" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group">
              <label class="col-md-4 control-label" for="max_winners">{{ trans('winfleet::winfleet.max_winners') }}</label>
              <div class="col-md-8">
              @if(setting('winfleet.max_winners', true) == '')
                <input type="number" id="max_winners" name="max_winners" class="form-control input-md" value="3" />
              @else
                <input type="number" id="max_winners" name="max_winners" class="form-control input-md" value="{{ setting('winfleet.max_winners', true) }}" />
              @endif
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label" for="period">{{ trans('winfleet::winfleet.period') }}</label>
              <div class="col-md-8">
              @if(setting('winfleet.period', true) == '')
                <input type="number" id="period" name="period" class="form-control input-md" value="14" />
              @else
                <input type="number" id="period" name="period" class="form-control input-md" value="{{ setting('winfleet.period', true) }}" />
              @endif
              </div>
            </div>
            <div class="form-group">
                <label for="integration" class="col-md-4 control-label">
                    {{ trans('winfleet::winfleet.slack') }}
                </label>
                <div class="col-md-8">
                    <select name="integration" id="integration" class="form-control" style="width: 100%;">
                        <option value=""></option>
                        @php $notification_channels = $Integration::where('type', 'slack')->get();
                        @foreach($Integration::where('type', 'slack')->get() as $channel)
                        <option value="{{ $channel->id }}" 
                                {{ setting('winfleet.integration', true)==$channel->id?'selected':''}}
                        >{{ $channel->name }}</option>                            
                        @endforeach
                    </select>
                </div>
            </div>
          </form>
        </div>
        <div class="panel-footer clearfix">
          <button type="submit" class="btn btn-success pull-right" form="winfleet-setup">{{ trans('winfleet::winfleet.save') }}</button>
        </div>
      </div>

    </div>

  </div>

@stop

@push('javascript')

@endpush