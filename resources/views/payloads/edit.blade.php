@extends('layouts.app')
@section('content')
@include('common.flash-message')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
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
            <a class="btn btn-sm btn-warning float-right" href="{{ route('webhooks.edit', $webhook->id) }}">
                <i class="fa fa-times"></i> Cancel
            </a>
        </div>
        <div class="form-group row">
            <div class="col-xs-12">
                <label class="field-compulsory">Conditions</label>
                <a href="" data-toggle="modal" data-target="#payloadExample"><i class="fa fa-info-circle"></i> example</a>
            </div>
            <input type="hidden" name="webhook_id" value="{{ $webhook->id }}">
            <input type="hidden" name="payload_id" value="{{ $payload->id }}">
            <input type="hidden" name="url" value="{{ route('webhooks.payloads.update', ['webhook' => $webhook, 'payload' => $payload]) }}">
            <div class="col-xs-12 mult-condition">
                @for($i = 0; $i < count($conditions); $i++) <div class="row">
                    <div class="col-md-2">
                        <input class="form-control field" id="field{{ $i }}" value="{{ $conditions[$i]->field }}"
                            data-id="{{ $conditions[$i]->id }}" name="field[]">
                    </div>
                    <div class="col-md-1">
                        {!! Form::select(
                        'operator[]',
                        ['==' => '==', '!=' => '!=', '>' => '>', '>=' => '>=', '<'=> '<', '<='=> '<=' ], $conditions[$i]->operator,
                                    ['class'=>'form-control operator', 'id' => 'operator'.$i]
                                    ) !!}
                    </div>
                    <div class="col-md-2">
                        <input class="form-control value" id="value{{ $i }}" value="{{ $conditions[$i]->value }}" name="value[]">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn--link-danger font-weight-normal" id="action{{ $i }}" onclick="removeCondition({{ $i }});">
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
            <label class="field-compulsory required" for="webhook_description">Content</label>
            <textarea class="form-control" rows="5" name="content">{{ $payload->content }}</textarea>
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
