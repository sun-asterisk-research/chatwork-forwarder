@extends('layouts.app')
@section('content')
@include('common.flash-message')

<ul class="breadcrumb breadcrumb-top">
  <li><a href="{{ route('webhooks.index') }}">Webhooks</a></li>
  <li><a href="{{ route('webhooks.edit', $webhook) }}">Mappings</a></li>
  <li>Update</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>Update Mapping</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    @include('modals.cancel_modal')
    <input type="hidden" name="webhook_id" value="{{ $webhook->id }}">
    <div class="form-horizontal form-bordered">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-sm btn-primary float-right" id="submitUpdate"><i class="fa fa-check"></i> Save</button>
            <button class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</button>
        </div>
        <div class="form-group row">
            <div class="col-xs-12 mult-condition">
                <div class="row">
                    <div class="col-md-5">
                        <label class="field-compulsory required" for="key"> Key</label>
                    </div>
                    <div class="col-md-5">
                        <label class="field-compulsory required" for="value"> Value</label>
                    </div>
                </div>
                @foreach($mappings as $index => $mapping)
                <div class="row">
                    <div class="col-md-5">
                        <input name="key[]" id="{{ 'key'. $index }}" value="{{ $mapping->key }}" class="form-control col-md-5 key" onchange="setChangeStatus(true)">
                    </div>
                    <div class="col-md-5">
                        <input name="value[]" id="{{ 'value'. $index }}" value="{{ $mapping->value }}" class="form-control col-md-5 value" onchange="setChangeStatus(true)">
                    </div>
                    <div class="col-md-2">
                        <button name="action[]" id="{{ 'action'. $index }}" class="btn btn--link-danger font-weight-normal action fa fa-minus-circle" onclick="removeCondition({{ $index }})"></button>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-xs-12">
                <button type="button" class="btn btn--link-primary font-weight-normal" onclick="addFields();"><i class="fa fa-plus-circle"></i>
                    <strong>Add fields</strong>
                </button>
            </div>
        </div>
    </div>
    <!-- END Simple Editor Content -->
</div>
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/mapping.js') }}"></script>
    @include('common.flash-message')
@endsection
