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
    <li>Detail</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>Edit payload</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    <div class="form-horizontal form-bordered">
        <div class="col-xs-12">
            <button id="update" type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
            <a class="btn btn-sm btn-default float-right cancel-btn">
                <i class="fa fa-times"></i> Cancel
            </a>
        </div>
        @include('modals.cancel_modal')
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory required">Payload params</label>
                <textarea class="form-control" rows="7" name="params" placeholder="Enter payload params">{{ $payload->params }}</textarea>
                <div class="has-error">
                    <span class="help-block params"></span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory">Conditions</label>
                <a href="" data-toggle="modal" data-target="#payloadExample"><i class="fa fa-info-circle"></i> Example</a>
            </div>
            <input type="hidden" name="webhook_id" value="{{ $webhook->id }}">
            <input type="hidden" name="payload_id" value="{{ $payload->id }}">
            <input type="hidden" name="url" value="{{ route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]) }}">
            <div class="col-xs-12 mult-condition">
                @for($i = 0; $i < count($conditions); $i++) <div class="row">
                    <div class="col-md-2">
                        <input class="form-control field" id="field{{ $i }}" value="{{ $conditions[$i]->field }}"
                            data-id="{{ $conditions[$i]->id }}" name="field[]" onchange="setChangeStatus(true)">
                    </div>
                    <div class="col-md-1">
                        {!! Form::select(
                        'operator[]',
                        ['==' => '==', '!=' => '!=', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=', 'Match' => 'Match'], $conditions[$i]->operator,
                                    ['class'=>'form-control operator', 'id' => 'operator'.$i, 'onchange' => "setChangeStatus(true)"]
                                    ) !!}
                    </div>
                    <div class="col-md-2">
                        <input class="form-control value" id="value{{ $i }}" value="{{ $conditions[$i]->value }}" name="value[]" onchange="setChangeStatus(true)">
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
            <span class="help-block error-field"></span>
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
    <div class="form-group row">
        <div class="col-xs-12">
            <label class="field-compulsory required">Content type</label>
            <a href="" data-toggle="modal" data-target="#contentType"><i class="fa fa-question-circle"></i> What is this?</a>
            <div class="radio">
                <label>
                    <input type="radio" name="content_type" id="content_text" value="text" {{ $payload->content_type == 'text' ? 'checked' : ''}}>
                    Text content
                </label>
                </div>
                <div class="radio">
                <label>
                    <input type="radio" name="content_type" id="content_block" value="blocks" {{ $payload->content_type == 'blocks' ? 'checked' : ''}}>
                    Block content
                </label>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <label class="field-compulsory required" for="webhook_description">Content</label>
            <a href="" data-toggle="modal" data-target="#contentExample"><i class="fa fa-info-circle"></i> Example</a>
            <textarea class="form-control" rows="7" name="content" placeholder="Enter Content message">{{ $payload->content }}</textarea>
            <div class="has-error">
                <span class="help-block content"></span>
            </div>
        </div>
    </div>

    <input type="hidden" id="webhookUrl" class="form-control" value="{{ config('app.url').'/api/v1/webhooks/'.$webhook->token }}">

    <div class="form-group row">
        <div class="col-xs-12">
            <label class="field-compulsory" for="webhook_description">Test with cURL</label>
            <a href="JavaScript:Void(0)" id="copyAsCurl"><i class="fa fa-clipboard"></i> copy as cURL</a>
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
    <script src="{{ asset('/js/payload.js') }}"></script>
    @include('common.flash-message')
@endsection
