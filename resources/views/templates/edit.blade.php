@extends('layouts.app')
@section('content')

<ul class="breadcrumb breadcrumb-top">
    <li><a href="{{ route('templates.index') }}">Templates</a></li>
    <li><a href="{{ route('templates.edit', $template) }}">Templates</a></li>
    <li>Update</li>
</ul>
<!-- Simple Editor Block -->
<div class="block">
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
            <div class="col-xs-4">
                <label class="field-compulsory required" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control field" value="{{ $template->name }}" placeholder="Enter name">
                <div class="has-error">
                    <span class="help-block name error-field"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="name">Params</label>
                <textarea id="params" name="params" class="form-control field" rows="4" value="{{ $template->params }}" placeholder="Enter params">{{ $template->params }}</textarea>
                <div class="has-error">
                    <span class="help-block params error-field"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="name">Content</label>
            <textarea id="content" name="content" class="form-control field" rows="4" value="{{ $template->content }}" placeholder="Enter content">{{ $template->content }}</textarea>
            <div class="has-error">
                <span class="help-block content error-field"></span>
            </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="name">Status</label>
                <select name="status" id="status" class="form-comtrol field">
                <option value="0" {{ $template->status == 0 ? "selected" : "" }} > Public</option>
                    <option value="1" {{ $template->status == 1 ? "selected" : "" }}> Unpublic</option>
                </select>
                <div class="has-error">
                    <span class="help-block status error-field"></span>
                </div>
            </div>
        </div>
        <!-- END Simple Editor Content -->
</div>
    <!-- END Simple Editor Block -->
@include('payloads.content-example')
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/templates.js') }}"></script>
    @include('common.flash-message')
@endsection
