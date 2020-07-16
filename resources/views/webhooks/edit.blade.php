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
                <input type="text" id="webhookUrl" class="form-control" value="{{ config('app.url').'/api/v1/webhooks/'.$webhook->token }}" readonly>
                <a class="input-group-addon" id="copyUrl" ><i class="fa fa-clipboard"></i></a>
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
                <input type="hidden" name="id" value="{{ $webhook->id }}">
                <label class="field-compulsory required">Chatwork bot</label>
                <select id="cw_bots" name="bot_id" class="select-select2" style="width: 100%;" data-placeholder="Choose one..">
                    <option></option>
                    @foreach($bots as $bot)
                        <option value="{{ $bot->id }}" {{ $webhook->bot_id == $bot->id ? "selected" : "" }}>{{ $bot->name }}</option>
                    @endforeach
                </select>
                @error('bot_id')
                <div class="has-error">
                    <span class="help-block">{{ $message }}</span>
                </div>
                @enderror
            </div>
            <div class="col-xs-1">
                <label class="field-compulsory" for="type_rooms">Type room</label>
                <select id="type_room" name="room_type" class="form-control" style="width: 100%;">
                    <option value="all">All</option>
                    <option value="group">Group</option>
                    <option value="direct">Private</option>
                </select>
            </div>
            <div class="col-xs-4">
                <input type="hidden" id="room_name" value="{{ $webhook->room_name }}">
                <label class="field-compulsory required" for="cw_rooms">Chatwork room</label>
                <select id="cw_rooms" name="room_name" class="select-select2" style="width: 100%;" data-placeholder="Choose one..">
                    <option></option>
                </select>
                @error('room_name')
                <div class="has-error">
                    <span class="help-block">{{ $message }}</span>
                </div>
                @enderror
            </div>
            <div class="col-xs-3">
                <label class="field-compulsory required" for="cw_room_id">Chatwork room id</label>
                <input type="hidden" id="room_id" value="{{ $webhook->room_id }}">
                <input type="text" readonly id="cw_room_id" name="room_id" class="form-control" placeholder="Room ID">
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
		                </a>&nbsp

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
            <span class="btn-pull-right">
            <a href="{{ route('webhooks.mappings.create', $webhook) }}" class="btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
            <a href="{{ route('webhooks.edit.mappings', $webhook) }}" class="btn btn-md btn-default"><i class="fa fa-pencil"></i> Edit</a>
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
</div>

@endsection
@section('js')
    <script src="{{ asset('/js/webhook.js') }}"></script>
    @include('common.flash-message')
@endsection
