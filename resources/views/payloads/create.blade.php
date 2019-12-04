@extends('layouts.app')
@section('content')
@include('common.flash-message')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>New payload</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    <input type="hidden" name="webhook_id" value="{{ $webhook->id }}">
    <div class="form-horizontal form-bordered">
        <div class="col-xs-12">
            <button id="submit" type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
            <a class="btn btn-sm btn-warning float-right" href="{{ route('webhooks.edit', $webhook) }}"><i class="fa fa-times"></i> Cancel</a>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory">Conditions</label>
                <a href="" data-toggle="modal" data-target="#payloadExample"><i class="fa fa-info-circle"></i> example</a>
            </div>
            <div class="col-xs-12 mult-condition">
            </div>
            <div class="col-xs-2 has-error">
                <span class="help-block error-field"></span>
            </div>
            <div class="col-xs-1">
            </div>
            <div class="col-xs-2 has-error">
                <span class="help-block error-value"></span>
            </div>
            <div class="col-xs-12">
                <button type="button" class="btn btn--link-primary font-weight-normal" onclick="addFields();"><i class="fa fa-plus-circle"></i>
                    <strong>Add condition</strong>
                </button>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required" for="webhook_description">Content</label>
                <textarea class="form-control" rows="5" name="content"></textarea>
                <div class="has-error">
                    <span class="help-block content"></span>
                </div>
            </div>
        </div>
        <!-- END Simple Editor Content -->
    </div>
</div>
@include('payloads.condition-example')
<!-- END Simple Editor Block -->
@endsection
@section('js')
<script src="{{ asset('/js/payload.js') }}"></script>
@endsection
