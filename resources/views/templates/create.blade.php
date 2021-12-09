<?php
use App\Enums\TemplateStatus;

?>

@extends('layouts.app')
@section('content')

<ul class="breadcrumb breadcrumb-top">
    <li><a href="{{ route('templates.index') }}">Templates</a></li>
    <li>Create</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>New Template</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    <div class="form-horizontal form-bordered">
        <div class="col-xs-12">
            <div style="padding-top: 10px">
                <button id="submit" type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
                <a class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
        @include('modals.cancel_modal')
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required">Name</label>
                <input id="template_name" class="form-control field" type="text" name="name" placeholder="Enter template name">
                <div class="has-error">
                    <span class="help-block template_name name error-field"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required">Params</label>
                <textarea class="form-control field" id="template_params" rows="7" name="params" placeholder="Enter payload params"></textarea>
                <div class="has-error">
                    <span class="help-block template_params params error-field"></span>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory">Conditions</label>
                <a href="" data-toggle="modal" data-target="#payloadExample"><i class="fa fa-info-circle"></i> Example</a>
            </div>
            <div class="col-xs-12 mult-condition">
            </div>
            <div class="col-xs-12">
                <div class="col-xs-2 has-error">
                    <span class="help-block error-field-condition"></span>
                </div>
                <div class="col-xs-1">
                </div>
                <div class="col-xs-2 has-error">
                    <span class="help-block error-value"></span>
                </div>
                <div class="col-xs-12">
                    <button type="button" class="btn btn--link-primary font-weight-normal" onclick="addFields();">
                        <i class="fa fa-plus-circle"></i>
                        <strong>Add condition</strong>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required">Content type</label>
                <a href="" data-toggle="modal" data-target="#contentType"><i class="fa fa-question-circle"></i> What is this?</a>
                <div class="radio">
                    <label>
                        <input type="radio" name="content_type" id="content_text" value="text" {{ (old('content_type') == 'text' || old('content_type') == null) ? 'checked' : ''}}>
                        Text content
                    </label>
                    </div>
                    <div class="radio">
                    <label>
                        <input type="radio" name="content_type" id="content_block" value="blocks" {{ old('content_type') == 'blocks' ? 'checked' : ''}}>
                        Block content
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required" for="webhook_description">Content</label>
                <a href="" data-toggle="modal" data-target="#contentExample"><i class="fa fa-info-circle"></i> Example</a>
                <textarea class="form-control field" id="template_content" rows="7" name="content" placeholder="Enter Content message"></textarea>
                <div class="has-error">
                    <span class="help-block template_content content error-field"></span>
                </div>
            </div>
        </div>
        <!-- END Simple Editor Content -->
    </div>
</div>
@include('payloads.condition-example')
@include('payloads.content-example')
@include('payloads.content-type')
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/templates.js') }}"></script>
    @include('common.flash-message')
@endsection
