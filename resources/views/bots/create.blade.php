@extends('layouts.app')
@section('content')
@include('common.flash-message')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>New Bot</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    @include('bots.cancel_modal')
    {{ Form::open(['url' => route('bots.store'), 'method' => 'post', 'class' => 'bot-form form-horizontal form-bordered']) }}
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="bot_name">Bot name</label>
                <input type="text" id="bot_name" name="name" class="form-control" value="{{ old('name') }}">
                @error('name')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="cw_id">Chatwork Bot ID</label>
                <input type="number" id="cw_id" name="cw_id" class="form-control" value="{{ old('cw_id') }}">
                @error('cw_id')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="bot_key">Bot Key</label>
                <input type="text" id="bot_key" name="bot_key" class="form-control" value="{{ old('bot_key') }}">
                @error('bot_key')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group form-actions">
            <div class="col-xs-12">
                <a class="btn btn-sm btn-default cancel-btn" href="{{ route('bots.index') }}"><i class="fa fa-times"></i> Cancel</a>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    {{ Form::close() }}
    <!-- END Simple Editor Content -->
</div>
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/bot.js') }}"></script>
@endsection
