<?php
use App\Enums\TemplateStatus;

?>

@extends('layouts.app')
@section('content')

<ul class="breadcrumb breadcrumb-top">
    <li><a href="{{ route('templates.index') }}">Templates</a></li>
    <li>Edit</li>
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
                <textarea id="params" name="params" class="form-control field" rows="7" value="{{ $template->params }}" placeholder="Enter params">{{ $template->params }}</textarea>
                <div class="has-error">
                    <span class="help-block params error-field"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory">Conditions</label>
                <a href="" data-toggle="modal" data-target="#payloadExample"><i class="fa fa-info-circle"></i> Example</a>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-12 mult-condition">
                    @for($i = 0; $i < count($template->conditions); $i++)
                    <div class="row">
                        <div class="col-md-2">
                            <input class="form-control field-condition" id="field{{ $i }}" value="{{ $template->conditions[$i]->field }}"
                                data-id="{{ $template->conditions[$i]->id }}" name="field[]" onchange="setChangeStatus(true)">
                        </div>
                        <div class="col-md-1">
                            {!! Form::select(
                            'operator[]',
                            ['==' => '==', '!=' => '!=', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=', 'Match' => 'Match'], $template->conditions[$i]->operator,
                                        ['class'=>'form-control operator', 'id' => 'operator'.$i, 'onchange' => "setChangeStatus(true)"]
                                        ) !!}
                        </div>
                        <div class="col-md-2">
                            <input class="form-control value" id="value{{ $i }}" value="{{ $template->conditions[$i]->value }}" name="value[]" onchange="setChangeStatus(true)">
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn--link-danger font-weight-normal action" id="action{{ $i }}" onclick="removeCondition({{ $i }});">
                                <i class="fa fa-minus-circle"></i>
                            </button>
                        </div>
                    </div>
                    @endfor
                </div>
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
                        <input type="radio" name="content_type" id="content_text" value="text" {{ $template->content_type == 'text' ? 'checked' : ''}}>
                        Text content
                    </label>
                    </div>
                    <div class="radio">
                    <label>
                        <input type="radio" name="content_type" id="content_block" value="blocks" {{ $template->content_type == 'blocks' ? 'checked' : ''}}>
                        Block content
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required" for="name">Content</label>
                <a href="" data-toggle="modal" data-target="#contentExample"><i class="fa fa-info-circle"></i> Example</a>
                <p class="notice">* You can use syntax <strong><@memberId></strong> to mention people on Slack</p>
                <textarea id="content" name="content" class="form-control field mt-15" rows="7" value="{{ $template->content }}" placeholder="Enter content">{{ $template->content }}</textarea>
                <div class="has-error">
                    <span class="help-block content error-field"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- END Simple Editor Content -->
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
