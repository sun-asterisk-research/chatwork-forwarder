@extends('layouts.app')
@section('content')
<?php use App\Enums\UserType; ?>

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
        <h2><strong>Migrate data from Chatwork Forwarder</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    {{ Form::open(['url' => route('webhook.start-migrate', ['webhook' => $webhook->id]), 'method' => 'post', 'class' => 'form-horizontal form-bordered webhook-form']) }}
        <div class="form-group row">
            <div class="col-xs-12">
                <p class="notice">* This feature will migrate your webhook config from Chatwork Forwarder to Slack Forwarder</p>
            </div>
            <div class="col-xs-12 mt-15">
                <label class="field-compulsory required" for="url">Chatwork Forwarder Webhook</label>
                <a href="" data-toggle="modal" data-target="#note" style="margin-left: 1rem;"><i class="fa fa-info-circle"></i> Notice</a>
                <input type="text" id="url" name="url" class="form-control" value="{{ old('url') }}" placeholder="https://cw-forwarder.sun-asterisk.vn/api/v1/webhooks/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                @error('url')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
            <div class="col-xs-12">
                <button type="submit" class="btn btn-sm btn-primary float-right" style="margin-right: 0">
                    <i class="fa fa-refresh" aria-hidden="true"></i> Start migrate
                </button>
            </div>
        </div>
    {{ Form::close() }}
    <!-- END Simple Editor Content -->
</div>
@include('webhooks.migrate_note')

<!-- END Simple Editor Block -->
@endsection

@section('js')
    @include('common.flash-message')
@endsection
