@extends('layouts.app')
@section('content')
<?php use App\Enums\UserType; ?>
<?php use App\Enums\WebhookStatus; ?>

<ul class="breadcrumb breadcrumb-top">
    @if (Auth::user()->role == UserType::ADMIN)
        <li><a href="{{ route('admin.webhooks.index') }}">Webhooks</a></li>
    @else
        <li><a href="{{ route('webhooks.index') }}">Webhooks</a></li>
    @endif
    <li>Detail</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>Edit webhook</strong></h2>
    </div>
    <!-- END Simple Editor Title -->
    <!-- Simple Editor Content -->
    {{ Form::open(['url' => route('webhooks.update', ['webhook' => $webhook]), 'method' => 'PUT', 'class' => 'webhook-form']) }}
    <div class="form-group row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
            <button class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</button>
        </div>
        <div class="col-xs-6">
            <label class="field-compulsory">Url</label>
            <div class="input-group">
                @php($url = config('app.url').'/api/v1/webhooks/'.$webhook->token)
                <input type="text" id="webhookUrl" class="form-control" value="{{ $url }}" readonly>
                <a class="input-group-addon" id="copyUrl" ><i class="fa fa-clipboard"></i></a>
            </div>
        </div>
        <div class="col-xs-6">
            <label class="field-compulsory">Change Owner Webhook</label>
            <div class="input-group">
                <input type="email" name="email" class="form-control" placeholder="Please type email" style="width: 300px">
                @error('email')
                <div class="has-error">
                    <span class="help-block">{{ $message }}</span>
                </div>
                @enderror
            </div>
        </div>
        <div class="mt-15 col-xs-12">
            <label class="field-compulsory">Google form script :
                <span class="fill" onclick="appendCode('{{$url}}', true)" data-toggle="tooltip" data-placement="top" title="Generate google script include form data">
                    Include form data
                </span>
                <span class="fill" onclick="appendCode('{{$url}}', false)" data-toggle="tooltip" data-placement="top" title="Generate google script do not include form data">
                    Do not include form data
                </span>
                <a href="" data-toggle="modal" data-target="#intergarteFormManual" style="margin-left: 1rem;"><i class="fa fa-info-circle"></i> Manual</a>
            </label>
            <div class="input-group">
                <textarea class="form-control" rows="12" readonly id="webhookScript"></textarea>
                <a class="input-group-addon" id="copyScript" ><i class="fa fa-clipboard"></i></a>
            </div>
        </div>
        <div class="mt-15 col-xs-8">
            <label class="field-compulsory required" for="webhook_name">Webhook name</label>
            <input type="text" id="webhook_name" name="name" class="form-control" value="{{  $webhook->name }}" placeholder="Enter name">
            @error('name')
            <div class="has-error">
                <span class="help-block">{{ $message }}</span>
            </div>
            @enderror
        </div>
        <div class="mt-15 col-xs-4">
            <label class="field-compulsory required" for="webhook_status">Status</label>
            <select id="webhook_status" name="status" class="form-control">
                <option value="{{ WebhookStatus::ENABLED }}" {{ $webhook->status == WebhookStatus::ENABLED ? "selected" : "" }}>Enable</option>
                <option value="{{ WebhookStatus::DISABLED }}" {{ $webhook->status == WebhookStatus::DISABLED ? "selected" : "" }}>Disable</option>
            </select>
        </div>
        <div class="mt-15 col-xs-12">
            <label class="field-compulsory" for="webhook_description">Description</label>
            <textarea class="form-control" rows="5" name="description" id="webhook_description" placeholder="Enter description">{{ $webhook->description }}</textarea>
            @error('description')
            <div class="has-error">
                <span class="help-block">{{ $message }}</span>
            </div>
            @enderror
        </div>
    </div>
    <div class="block">
        <div class="block-title">
            <h2><strong>Chatbot</strong></h2>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <div class="form-check">
                    <input class="form-check-input"
                        type="checkbox"
                        id="use_default"
                        {{ $webhook->bot_id != config('slack.slack_bot_id') ? '' : 'checked' }}
                        name="use_default">
                    <label class="form-check-label" for="use_default">
                        Use Slack Forwarder Bot
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <input type="hidden" name="id" value="{{ $webhook->id }}">
                <label class="field-compulsory required">Slack bot</label>
                @if ($webhook->bot_id != config('slack.slack_bot_id'))
                <select id="cw_bots" name="bot_id" class="select-select2" style="width: 100%;" data-placeholder="Choose one..">
                    <option></option>
                    @foreach($bots as $bot)
                        <option value="{{ $bot->id }}" {{ $webhook->bot_id == $bot->id ? "selected" : "" }}>{{ $bot->name }}</option>
                    @endforeach
                </select>
                @else
                <select id="cw_bots" name="bot_id" class="select-select2" disabled style="width: 100%;" data-placeholder="Choose one..">
                    <option></option>
                    @foreach($bots as $bot)
                        <option value="{{ $bot->id }}" {{ $webhook->bot_id == $bot->id ? "selected" : "" }}>{{ $bot->name }}</option>
                    @endforeach
                </select>
                @endif
                @error('bot_id')
                <div class="has-error">
                    <span class="help-block">{{ $message }}</span>
                </div>
                @enderror
            </div>
            <div class="col-xs-4">
                <label class="field-compulsory required" for="cw_rooms">Channel name</label>
                <input type="text" id="cw_rooms" name="room_name" class="form-control" value="{{ $webhook->room_name }}" placeholder="Enter name">
                @error('room_name')
                <div class="has-error">
                    <span class="help-block">{{ $message }}</span>
                </div>
                @enderror
            </div>
            <div class="col-xs-4">
                <label class="field-compulsory required" for="cw_room_id">Channel ID</label>
                <input type="hidden" id="room_id" value="{{ $webhook->room_id }}">
                <input type="text" id="cw_room_id" name="room_id" class="form-control" placeholder="Room ID">
                @error('room_id')
                <div class="has-error">
                    <span class="help-block">{{ $message }}</span>
                </div>
                @enderror
            </div>
        </div>
    </div>
    {{ Form::close() }}
    @include('modals.cancel_modal')
    <br/>
    <div class="block">
        <!-- Simple Editor Title -->
        <div class="block-title">
            <h2><strong>Payloads</strong></h2>
            <a href="{{ route('webhooks.payloads.create', $webhook->id) }}" class="btn-pull-right btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
        </div>

        @if (count($payloads) <= 0)
            <div class="form-group row">
                <div class="col-xs-12">
                    No records
                </div>
            </div>
        @else
            <div class="form-group row">
                <div class="col-xs-5">
                    <label class="field-compulsory" for="cw_room_id">Payload content</label>
                </div>
                <div class="col-xs-5">
                    <label class="field-compulsory" for="cw_room_id">Payload condition</label>
                </div>
                <div class="col-xs-2 text-center">
                    <label class="field-compulsory" for="cw_room_id">&nbsp &nbsp Action</label>
                </div>
            </div>
            @include('webhooks.delete_confirm_modal')
            @foreach($payloads as $key => $payload)
            <div class="form-group row">
                <div class="col-xs-5">
                    <input type="text" class="form-control" value="{{ $payload->content }}" readonly>
                </div>
                <div class="col-xs-5">
                    @if($payload->conditions->isEmpty())
                        <input type="text" class="form-control" value="No conditions" readonly>
                        <div class="panel-heading">
                            <h4 class="panel-title"></h4>
                        </div>
                    @else
                        @foreach($payload->conditions as $key => $condition)
                        <input type="text" class="form-control" value="{{$condition->field}} {{$condition->operator}} {{$condition->value}}" readonly>
                        @endforeach
                    @endif
                </div>
		            <div class="col-xs-2 text-center">
		                <a class="btn btn-sm btn-default" href="{{ route('webhooks.payloads.edit', ['webhook' => $webhook, 'payload' => $payload]) }} ">
		                    <i class="fa fa-pencil"></i> Edit
		                </a>

                        <a class="btn btn-sm btn-default" href="{{ route('webhooks.payloads.copy', ['webhook' => $webhook, 'payload' => $payload]) }} ">
		                    <i class="fa fa-clipboard"></i> Copy
		                </a>

		                {{ Form::open([
		                    'method' => 'DELETE',
		                    'route' => ['webhooks.payloads.destroy', 'webhook' => $webhook, 'payload' => $payload],
		                    'style' => 'display:inline',
		                    'class' => 'form-delete'
		                ]) }}
		                    {{ Form::button('<i class="fa fa-trash-o"> Delete</i>' , [
                            'type' => 'DELETE',
                            'class' => 'btn btn-sm btn-danger delete-btn',
                            'title' => 'Delete'
		                    ]) }}
		                {{ Form::close() }}
		            </div>
            </div>
            @endforeach
        @endif
    </div>
    <br>
    <div class="block">
        <div class="block-title">
            <h2><strong>Mappings</strong></h2>
            <span class="upload-file">
            <a href="{{ route('webhooks.mappings.create', $webhook) }}" class="btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
            <a href="{{ route('webhooks.edit.mappings', $webhook) }}" class="btn btn-md btn-default"><i class="fa fa-pencil"></i> Edit</a>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadFile">Import</button>
            <a href={{ route('export-file', $webhook) }}>
                <button type="button" class="btn btn-info">Export</button>
            </a>
        </span>

        </div>

        @if (count($mappings) <= 0)
            <div class="form-group row">
                <div class="col-xs-12">
                    No records
                </div>
            </div>
        @else
            <div class="form-group row">
                <div class="col-xs-5">
                    <label class="field-compulsory" for="cw_room_id">Key</label>
                </div>
                <div class="col-xs-5">
                    <label class="field-compulsory" for="cw_room_id">Value</label>
                </div>
                <div class="col-xs-2 text-center">
                    <label class="field-compulsory" for="cw_room_id">Action</label>
                </div>
            </div>
            @include('webhooks.delete_mapping_confirm_modal')
            @foreach($mappings as $key => $mapping)
                <div class="form-group row">
                    <div class="col-xs-5">
                        <input type="text" class="form-control" value="{{ $mapping->key }}" readonly>
                    </div>
                    <div class="col-xs-5">
                        <input type="text" class="form-control" value="{{ $mapping->value }}" readonly>
                    </div>
                    <div class="col-xs-2 text-center mapping-item">
                        {{ Form::open([
		                    'method' => 'DELETE',
		                    'url' => route('webhooks.mappings.destroy', ['webhook' => $webhook, 'mapping' => $mapping]),
		                    'style' => 'display:inline',
		                    'class' => 'form-delete-mapping'
		                ]) }}
		                    {{ Form::button('<i class="fa fa-trash-o"> Delete</i>' , [
                            'type' => 'DELETE',
                            'class' => 'btn btn-sm btn-danger delete-btn delete-mapping',
                            'title' => 'Delete',
		                    ]) }}
		                {{ Form::close() }}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <!-- END Simple Editor Content -->
    <!-- Modal Upload File -->
    <div class="modal fade" id="uploadFile" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <form id="myForm" name="myForm" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog" style="top: 40%;" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Upload File Json</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                            <div class="header">
                                <input type="hidden" name="webhook_id" value="{{ $webhook->id }}">
                                <input type="file" id="UploadFileJson" name="file_uploads" accept=".json">
                            </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="upload" class="btn btn-primary">Save</button>
                </div>
                </div>
            </div>
        </form>
    </div>
</div>

@include('webhooks.intergrate-form-manual')
@endsection


@section('js')
    <script src="{{ asset('/js/webhook.js') }}"></script>
    <script src="{{ asset('/js/mapping.js') }}"></script>
    @include('common.flash-message')
@endsection

<style type="text/css">
    .upload-file {
        margin-left: 74% !important;
        margin-bottom: 5px !important;
    }
</style>
