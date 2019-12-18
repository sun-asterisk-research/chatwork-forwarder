@extends('layouts.app')
@section('content')
@include('common.flash-message')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">

<ul class="breadcrumb breadcrumb-top">
  <li><a href="{{ route('webhooks.index') }}">Webhooks</a></li>
  <li><a href="{{ route('webhooks.edit', $webhook) }}">Mappings</a></li>
  <li>Create</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
    <!-- Simple Editor Title -->
    <div class="block-title">
        <h2><strong>New Mapping</strong></h2>
    </div>
    <!-- END Simple Editor Title -->

    <!-- Simple Editor Content -->
    @include('modals.cancel_modal')
    {{ Form::open(['url' => route('webhooks.mappings.store', $webhook), 'method' => 'post', 'class' => 'mapping-form form-horizontal form-bordered']) }}
        <div class="col-xs-12">
            <button class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter name">
                @error('name')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="key">Key</label>
                <input type="text" id="key" name="key" class="form-control" value="{{ old('key') }}" placeholder="Enter key">
                @error('key')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-4">
                <label class="field-compulsory required" for="value">Value</label>
                <input type="text" id="value" name="value" class="form-control" value="{{ old('value') }}" placeholder="Enter value">
                @error('value')
                    <div class="has-error">
                        <span class="help-block">{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>
    {{ Form::close() }}
    <!-- END Simple Editor Content -->
</div>
<!-- END Simple Editor Block -->
@endsection
@section('js')
    <script src="{{ asset('/js/mapping.js') }}"></script>
@endsection
