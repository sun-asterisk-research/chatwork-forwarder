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
    <li>Create</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>New webhook</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    {{ Form::open(['url' => route('webhooks.store'), 'method' => 'post', 'class' => 'form-horizontal form-bordered webhook-form']) }}
        <div class="form-group row">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
                <a class="btn btn-sm btn-default float-right cancel-btn" href="{{ route('webhooks.index') }}"><i class="fa fa-times"></i> Cancel</a>
            </div>
            <div class="col-xs-8">
                <label class="field-compulsory required" for="webhook_name">Webhook name</label>
                <input type="text" id="webhook_name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter name">
                @error('name')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
            <div class="col-xs-4">
                <label class="field-compulsory required" for="webhook_status">Status</label>
                <select id="webhook_status" name="status" class="form-control">
                    <option value="{{ WebhookStatus::ENABLED }}" {{ old('status') === strval(WebhookStatus::ENABLED) ? "selected" : ""}}>Enable</option>
                    <option value="{{ WebhookStatus::DISABLED }}" {{ old('status') === strval(WebhookStatus::DISABLED) ? "selected" : "" }}>Disable</option>
                </select>
            </div>
            <div class="mt-15 col-xs-12">
                <label class="field-compulsory" for="webhook_description">Description</label>
                <textarea class="form-control" rows="5" name="description" id="webhook_description" placeholder="Enter description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <div class="form-check">
                    @if (old('bot_id'))
                        <input class="form-check-input" type="checkbox" id="use_default" name="use_default">
                    @else
                        <input class="form-check-input" type="checkbox" id="use_default" checked name="use_default">
                    @endif
                    <label class="form-check-label" for="use_default">
                        Use Slack Forwarder's Bot
                    </label>
                    <p class="notice">* You will need to add Slack Fowarder App to your channel</p>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="webhook_bot_id">Select your Slack bot</label>
                <a href="" data-toggle="modal" data-target="#permission" style="margin-left: 1rem;"><i class="fa fa-info-circle"></i> Notice</a>
                @if (!old('bot_id'))
                    <select id="cw_bots" name="bot_id" class="select-select2" disabled style="width: 100%;" data-placeholder="Choose one..">
                        <option></option>
                        @foreach($bots as $bot)
                            <option value="{{ $bot->id }}" {{ old('bot_id') == $bot->id ? "selected" : "" }}>{{ $bot->name }}</option>
                        @endforeach
                    </select>
                @else
                    <select id="cw_bots" name="bot_id" class="select-select2" style="width: 100%;" data-placeholder="Choose one..">
                        <option></option>
                        @foreach($bots as $bot)
                            <option value="{{ $bot->id }}" {{ old('bot_id') == $bot->id ? "selected" : "" }}>{{ $bot->name }}</option>
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
                <input type="text" id="cw_rooms" name="room_name" class="form-control" value="{{ old('room_name') }}" placeholder="Enter name">
                @error('room_name')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
            <div class="col-xs-4">
                <label class="field-compulsory required" for="cw_room_id">Channel ID</label>
                <a href="" data-toggle="modal" data-target="#channelId" style="margin-left: 1rem;"><i class="fa fa-info-circle"></i> Notice</a>
                <input type="text" id="cw_room_id" name="room_id" class="form-control" value="{{ old('room_id') }}" placeholder="Channel/ DMs ID">
                @error('room_id')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
    {{ Form::close() }}
    @include('modals.cancel_modal')
    @include('webhooks.notice')
    @include('webhooks.channel_id')
    <!-- END Simple Editor Content -->
</div>
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/webhook.js') }}"></script>
    @include('common.flash-message')
@endsection
