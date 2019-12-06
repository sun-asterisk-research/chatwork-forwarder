@extends('layouts.app')
@section('content')
@include('common.flash-message')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
<!-- Simple Editor Block -->
<div class="block">
	<!-- Simple Editor Title -->
	<div class="block-title">
		<h2><strong>Detail webhook</strong></h2>
	</div>
	<!-- END Simple Editor Title -->
	<!-- Simple Editor Content -->
	{{ Form::open(['url' => route('webhooks.update', ['webhook' => $webhook]), 'method' => 'PUT']) }}
	<div class="form-group row">
		<div class="col-xs-12">
			<button type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
			<a class="btn btn-sm btn-default float-right" href="{{ route('webhooks.index') }}"><i class="fa fa-times"></i> Cancel</a>
		</div>
		<div class="col-xs-8">
			<label class="field-compulsory required" for="webhook_name">Webhook name</label>
			<input type="text" id="webhook_name" name="name" class="form-control" value="{{  $webhook->name }}">
			@error('name')
			<div class="has-error">
				<span class="help-block">{{ $message }}</span>
			</div>
			@enderror
		</div>
		<div class="col-xs-4">
			<label class="field-compulsory required" for="webhook_status">Status</label>
			<select id="webhook_status" name="status" class="form-control">
				@foreach($webhookStatuses as $key => $value)
				<option value="{{ $value }}" {{ $webhook->status == $value ? "selected" : "" }}>{{ $key }}</option>
				@endforeach
			</select>
		</div>
		<div class="col-xs-12">
			<label class="field-compulsory">Token</label>
			<input type="text" class="form-control" value="{{ $webhook->token }}" readonly>
		</div>
		<div class="col-xs-12">
			<label class="field-compulsory required" for="webhook_description">Description</label>
			<textarea class="form-control" rows="5" name="description" id="webhook_description">{{ $webhook->description }}</textarea>
			@error('description')
			<div class="has-error">
				<span class="help-block">{{ $message }}</span>
			</div>
			@enderror
		</div>
	</div>
	<div class="block">
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="hidden" name="id" value="{{ $webhook->id }}">
				<label class="field-compulsory required">Chatwork bot</label>
				<select id="bot_id" name="bot_id" class="form-control">
					@foreach($bots as $key => $value)
					<option value="{{ $value }}" {{ $webhook->bot()->first()->name == $key ? "selected" : "" }}>{{ $key }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-4">
				<label class="field-compulsory required" for="cw_rooms">Chatwork room</label>
				<select id="room_name" name="room_name" class="form-control">
					<option selected value="{{ $webhook->room_name }}">{{ $webhook->room_name }}</option>
				</select>
				@error('room_name')
				<div class="has-error">
					<span class="help-block">{{ $message }}</span>
				</div>
				@enderror
			</div>
			<div class="col-xs-4">
				<label class="field-compulsory required" for="cw_room_id">Chatwork room id</label>
				<input type="text" readonly id="cw_room_id" name="room_id" class="form-control" value="{{ $webhook->room_id }}">
				@error('room_id')
				<div class="has-error">
					<span class="help-block">{{ $message }}</span>
				</div>
				@enderror
			</div>
		</div>
	</div>
	{{ Form::close() }}
	<div class="block payload-content">
		<div class="form-group row">
			<div class="col-xs-12">
				<a class="btn btn-sm btn-primary float-right" href="{{ route('webhooks.payloads.create', $webhook->id) }}"><i class="fa fa-plus-circle"></i> Create</a>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-5">
					<label class="field-compulsory" for="cw_room_id">Payload content</label>
				</div>
				<div class="col-xs-5">
					<label class="field-compulsory" for="cw_room_id">Payload condition</label>
				</div>
				<div class="col-xs-2">
					<label class="field-compulsory" for="cw_room_id">&nbsp &nbsp Action</label>
				</div>
			</div>
			@include('webhooks.delete_confirm_modal')
			@foreach($payloads as $key => $payload)
			<div class="row">
				<div class="col-xs-5">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">{{ $payload->content }}</h4>
						</div>
					</div>
				</div>
				<div class="col-xs-5">
					<div class="panel panel-default">
							@if($payload->conditions->isEmpty())
								<div class="panel-heading">
									<h4 class="panel-title">No conditions</h4>
								</div>
							@else
								@foreach($payload->conditions as $key => $condition)
									<div class="panel-heading">
										<h4 class="panel-title">{{$condition->field}} {{$condition->operator}} {{$condition->value}}</h4>
									</div>
								@endforeach
							@endif
					</div>
				</div>
				<div class="col-xs-2">
					<a class="btn btn-sm btn-default" href="{{ route('webhooks.payloads.edit', ['webhook' => $webhook, 'payload' => $payload]) }} ">
						<i class="fa fa-pencil"></i> Edit
					</a>&nbsp

					{{ Form::open([
							'method' => 'DELETE',
							'route' => ['webhooks.payloads.destroy', 'webhook' => $webhook, 'payload' => $payload],
							'style' => 'display:inline',
							'class' => 'form-delete'
					]) }}
							{{ Form::button('<i class="fa fa-trash-o"> Delete</i>' , [
											'type' => 'DELETE',
											'class' => 'btn btn-sm btn-danger delete-btn',
											'title' => 'Delete'
							]) }}
					{{ Form::close() }}
				</div>
			</div>
			@endforeach
		</div>
	</div>
	<!-- END Simple Editor Content -->
</div>

@endsection
@section('js')
<script src="{{ asset('/js/webhook.js') }}"></script>
@endsection
