<?php use App\Enums\MessageHistoryStatus;
      use App\Enums\PayloadHistoryStatus;
?>

@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
<ul class="breadcrumb breadcrumb-top">
    <li>Payload History</li>
    <li><a href="/history">List</a></li>
    <li><a href="">Detail</a></li>
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
        <div class="col-md-11">
            {{ $payloadHistory->status == PayloadHistoryStatus::SUCCESS ? 'Success' : 'Failed' }}
        </div>
    </div>
    <div class="row">
        <label class="col-md-1 text-right">
            Params:
        </label>
        <div class="col-md-11">
            <pre class="payload-example">
{{ $payloadHistory->params }}
            </pre>
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
</div>
<div class="block full">
    <div class="block-title">
        <h2><strong>Message History list</strong></h2>
    </div>
    @include('common.flash-message')
    <div class="table-responsive">

        <div class="form-group row">
            <form method="GET" action="{{ route('history.show', ['history' => $payloadHistory->id]) }}">
                <div class="col-xs-4">
                    <input type="text" type="submit" name="search" class="form-control" placeholder="message content" value="{{ request('search') }}">
                </div>
                <button class="btn btn-md btn-search"><i class="fa fa-search"></i> search</button>
            </form>
        </div>
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
            </tbody>
        </table>
        <div class="pagination-wrapper"> {{ $messageHistories->appends(['search' => Request::get('search')])->render() }} </div>
    </div>
</div>
<!-- END Datatables Content -->
@endsection
<script src="{{ mix('/js/custom.js') }}"></script>

