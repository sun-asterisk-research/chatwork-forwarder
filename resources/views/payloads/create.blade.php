@extends('layouts.app')
@section('content')
<?php use App\Enums\UserType; ?>

<ul class="breadcrumb breadcrumb-top">
    @if (Auth::user()->role == UserType::ADMIN)
        <li><a href="{{ route('admin.webhooks.index') }}">Webhooks</a></li>
    @else
        <li><a href="{{ route('webhooks.index') }}">Webhooks</a></li>
    @endif
    <li><a href="{{ route('webhooks.edit', ['webhook' => $webhook]) }}">Payloads</a></li>
    <li>Create</li>
</ul>

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
            <div style="padding-top: 10px">
            <span class="fill" id="github" data-toggle="tooltip" data-placement="top" title="Sample template for new Pull Request on Github">Github</span>
            <span class="fill" id="gitlab" data-toggle="tooltip" data-placement="top" title="Sample template for new Pull Request on Gitlab"> Gitlab</span>
            <span class="fill" id="viblo" data-toggle="tooltip" data-placement="top" title="Sample template for new post on Viblo"> Viblo</span>
            <select class="fill" id="selectTemplate" onchange="selectTemplate({{$templates}})">
                <option value="" disabled selected>Choose template</option>
                @foreach ($templates as $index => $template)
                    <option value="{{ $index }}">{{$template->name}}</option>
                @endforeach
            </select>
            <button id="submit" type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
            <a class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
        @include('modals.cancel_modal')
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required">Payload params</label>
                <textarea class="form-control" id="payload_params" rows="5" name="params" placeholder="Enter payload params"></textarea>
                <div class="has-error">
                    <span class="help-block params"></span>
                </div>
            </div>
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
                <a href="" data-toggle="modal" data-target="#contentExample"><i class="fa fa-info-circle"></i> example</a>
                <textarea class="form-control" id="payload_content" rows="5" name="content" placeholder="Enter Content message"></textarea>
                <div class="has-error">
                    <span class="help-block content"></span>
                </div>
            </div>
        </div>
        <!-- END Simple Editor Content -->
    </div>
</div>
@include('payloads.condition-example')
@include('payloads.content-example')
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/payload.js') }}"></script>
    @include('common.flash-message')
@endsection
