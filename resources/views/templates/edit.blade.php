<?php
use App\Enums\TemplateStatus;

?>

@extends('layouts.app')
@section('content')

<ul class="breadcrumb breadcrumb-top">
    <li><a href="{{ route('templates.index') }}">Templates</a></li>
    <li>Detail</li>
</ul>
<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Content -->
    <div class="form-horizontal form-bordered">
        <!-- Simple Editor Title -->
        <div class="block-title">
            <h2><strong>Update Template</strong></h2>
        </div>
        <!-- END Simple Editor Title -->
        <!-- Simple Editor Content -->
        @include('modals.cancel_modal')
        <div class="col-xs-12">
            <a class="btn btn-sm btn-default float-right cancel-btn" href="{{ route('templates.index') }}"><i class="fa fa-times"></i> Cancel</a>
            <button type="button" id="submitUpdate" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
        </div>
        <div class="form-group row">
            <input type="hidden" name="id" value="{{ $template->id }}">
            <input type="hidden" name="url" value="{{ route('templates.update', ['template' => $template]) }}">
            <div class="col-xs-12">
                <label class="field-compulsory required" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control field" value="{{ $template->name }}" placeholder="Enter name">
                <div class="has-error">
                    <span class="help-block name error-field"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required" for="name">Params</label>
                <textarea id="params" name="params" class="form-control field" rows="4" value="{{ $template->params }}" placeholder="Enter params">{{ $template->params }}</textarea>
                <div class="has-error">
                    <span class="help-block params error-field"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required" for="name">Content</label>
                <a href="" data-toggle="modal" data-target="#contentExample"><i class="fa fa-info-circle"></i> example</a>
                <textarea id="content" name="content" class="form-control field" rows="4" value="{{ $template->content }}" placeholder="Enter content">{{ $template->content }}</textarea>
                <div class="has-error">
                    <span class="help-block content error-field"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required" for="template_status">Status</label>
                <select id="template_status" name="status" class="form-control">
                    <option value="{{ TemplateStatus::STATUS_PUBLIC }}" {{ $template->status === strval(TemplateStatus::STATUS_PUBLIC) ? "selected" : ""}}>PUBLIC</option>
                    <option value="{{ TemplateStatus::STATUS_UNPUBLIC }}" {{ $template->status === strval(TemplateStatus::STATUS_UNPUBLIC) ? "selected" : "" }}>UNPUBLIC</option>
                </select>
            </div>
        </div>
    </div>
    <!-- END Simple Editor Content -->
</div>
@include('payloads.content-example')
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/templates.js') }}"></script>
    @include('common.flash-message')
@endsection
