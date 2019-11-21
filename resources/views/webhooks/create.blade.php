@extends('layouts.app')
@section('content')
@include('common.flash-message')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>New webhook</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    {{ Form::open(['url' => route('webhooks.store'), 'method' => 'post', 'class' => 'form-horizontal form-bordered']) }}
        <div class="form-group row">
            <div class="col-xs-8">
                <label class="field-compulsory required" for="webhook_name">Webhook name</label>
                <input type="text" id="webhook_name" name="name" class="form-control" value="{{ old('name') }}">
                @error('name')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
            <div class="col-xs-4">
                <label class="field-compulsory required" for="webhook_status">Status</label>
                <select id="webhook_status" name="status" class="form-control">
                    @foreach($webhookStatuses as $key => $value)
                        <option value="{{ $value }}" {{ old('status') === $value ? "selected" : "" }}>{{ $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-12">
                <label class="field-compulsory required" for="webhook_description">Description</label>
                <textarea class="form-control" rows="5" name="description" id="webhook_description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="webhook_description">Chatwork bot</label>
                <select id="cw_bots" name="bot_id" class="select-select2" style="width: 100%;" data-placeholder="Choose one..">
                    <option></option>
                    @foreach($bots as $key => $value)
                        <option value="{{ $value }}">{{ $key }}</option>
                    @endforeach
                </select>
                @error('bot_id')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
            <div class="col-xs-4">
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
            <div class="col-xs-4">
                <label class="field-compulsory required" for="cw_room_id">Chatwork room id</label>
                <input type="text" readonly id="cw_room_id" name="room_id" class="form-control">
                @error('room_id')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group form-actions">
            <div class="col-xs-12">
                <a class="btn btn-sm btn-warning" href="{{ route('webhooks.index') }}"><i class="fa fa-times"></i> Cancel</a>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    {{ Form::close() }}
    <!-- END Simple Editor Content -->
</div>
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/webhook.js') }}"></script>
@endsection
