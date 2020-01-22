@extends('layouts.app')
@section('content')
@include('common.flash-message')

<ul class="breadcrumb breadcrumb-top">
  <li><a href="{{ route('webhooks.index') }}">Webhooks</a></li>
  <li><a href="{{ route('webhooks.edit', $webhook) }}">Mappings</a></li>
  <li>Create</li>
</ul>

<div class="block">
    <div class="block-title">
        <h2><strong>New Mapping</strong></h2>
    </div>
    @include('modals.cancel_modal')
    <input type="hidden" name="webhook_id" value="{{ $webhook->id }}">
    <div class="form-horizontal form-bordered">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-sm btn-primary float-right" id="submit"><i class="fa fa-check"></i> Save</button>
            <button class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</button>
        </div>
        <div class="form-group row">
            <div class="col-xs-12 mult-condition">
                <div class="row">
                    <div class="col-md-5">
                        <label class="field-compulsory required" for="key"> Key</label>
                        <input name="key[]" id="key0" placeholder="Enter key" class="form-control col-md-5 key" onchange="setChangeStatus(true)">
                    </div>
                    <div class="col-md-5">
                        <label class="field-compulsory required" for="value"> Value</label>
                        <input name="value[]" id="value0" placeholder="Enter value" class="form-control col-md-5 value" onchange="setChangeStatus(true)">
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <button type="button" class="btn btn--link-primary font-weight-normal" onclick="addFields();"><i class="fa fa-plus-circle"></i>
                    <strong>Add fields</strong>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script src="{{ asset('/js/mapping.js') }}"></script>
@endsection
