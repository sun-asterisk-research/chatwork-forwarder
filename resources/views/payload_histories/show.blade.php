<?php use App\Enums\MessageHistoryStatus;
use App\Enums\PayloadHistoryStatus;
?>

@extends('layouts.app')
@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li><a href="/history">Payload Histories</a></li>
    <li>Detail</li>
</ul>
<div class="block full">
    <div class="block-title">
        <h2><strong>Payload History detail</strong></h2>
    </div>
    <div class="row">
        <label class="col-md-1 text-right">
            Webhook:
        </label>
        <div class="col-md-11">
            {{ $payloadHistory->webhook->name }}
        </div>
    </div>
    <div class="row">
        <label class="col-md-1 text-right">
            Status:
        </label>
        <div class="col-md-11 mt-4">
            @if($payloadHistory->status === PayloadHistoryStatus::SUCCESS)
                <div class="label label-success">Success</div>
            @else
                <div class="label label-danger">Failed</div>
            @endif
        </div>
    </div>
    <div class="row">
        <label class="col-md-1 text-right">
            Params:
        </label>
        <div class="col-md-11">
            <a href="javascript:void(0)" data-toggle="collapse" data-target="#params">click to show</a>
            <div id="params" class="collapse">
                <pre class="payload-example">{{ json_encode(json_decode($payloadHistory->params), JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
    <div class="row">
        <label class="col-md-1 text-right">
            Log:
        </label>
        <div class="col-md-11">
            {{ $payloadHistory->log }}
        </div>
    </div>
    <div class="row">
        <label class="col-md-1 text-right">
            <input type="hidden" id="history_id" value="{{ $payloadHistory->id }}">
            <button class="btn btn-sm btn-warning" id="recheck" data-toggle="tooltip" data-placement="top" title="Recheck the payload again"><i class="fa fa-repeat" aria-hidden="true"></i> Recheck</button>
        </label>
    </div>
</div>
<div class="block full">
    <div class="block-title">
        <h2><strong>Message History list</strong></h2>
    </div>
    <div class="form-group row">
            <form method="GET" action="{{ route('history.show', ['history' => $payloadHistory->id]) }}">
                <div class="col-xs-4">
                    <input type="text" type="submit" name="search" class="form-control" placeholder="Search by message content" value="{{ request('search') }}">
                </div>
                <button class="btn btn-md btn-search"><i class="fa fa-search"></i> search</button>
            </form>
        </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-condensed table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Message Content</th>
                    <th class="text-center">Status</th>
                    <th>Log</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (count($messageHistories) <= 0)
                    <p class="tbl-no-data"><i class="fa fa-info-circle"></i> No data</p>
                @else
                    @foreach ($messageHistories as $messageHistory)
                        <tr class="item-{{ $messageHistory->id }}">
                            <td>{{ Helper::indexNumber(app('request')->input('page'), config('paginate.perPage'), $loop->iteration) }}</td>
                            <td>{{ $messageHistory->message_content }}</td>
                            <td class="text-center">
                                @if($messageHistory->status == MessageHistoryStatus::SUCCESS)
                                    <div class="label label-primary">Success</div>
                                @else
                                    <div class="label label-warning">Failed</div>
                                @endif
                            </td>
                            <td>{{ $messageHistory->log }}</td>
                            <td class="text-center">
                                {{ Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['message.destroy', 'message' => $messageHistory],
                                    'style' => 'display:inline',
                                    'class' => 'form-delete'
                                    ]) }}
                                    {{ Form::button('<i class="fa fa-trash-o"></i> Delete' , [
                                    'type' => 'DELETE',
                                    'class' => 'btn btn-sm btn-danger delete-btn',
                                    'title' => 'Delete'
                                    ]) }}
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="pagination-wrapper"> {{ $messageHistories->appends(['search' => Request::get('search')])->render() }} </div>
    </div>
</div>
<!-- END Datatables Content -->
@endsection
@section('js')
    @include('common.flash-message')
    <script src="{{ asset('js/payloadHistory.js') }}"></script>
@endsection
<script src="{{ mix('/js/custom.js') }}"></script>
