<div class="row">
    <div class="col-md-12">
        <div class="box box-solid no-border no-shadow">
            <div class="box-header with-border">
                <h3 class="inline">{{ trans('calendar::seat.confirmed') }}</h3>
            </div>
            <div class="box-body">
                <table class="table table-condensed" id="confirmed">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('web::seat.character', 1) }}</th>
                            <th>{{ trans_choice('web::seat.corporation', 1) }}</th>
                            <th>{{ trans('web::seat.ship_type') }}</th>
                            <th>{{ trans_choice('web::seat.group', 1) }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>