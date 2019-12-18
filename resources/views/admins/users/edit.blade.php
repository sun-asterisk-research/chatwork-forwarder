<?php use App\Enums\UserType; ?>

@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">

<ul class="breadcrumb breadcrumb-top">
	<li><a href="{{ route('users.index') }}">Users</a></li>
    <li>Detail</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
	<!-- Simple Editor Title -->
	<div id="noti"></div>
	<div class="block-title">
		<h2><strong>Edit user</strong></h2>
	</div>
	<!-- END Simple Editor Title -->

	<!-- Simple Editor Content -->
	{{ Form::open(['method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'user_update', 'class' => 'user-form form-horizontal form-bordered padding-l-r-10']) }}
	<div class="form-group row">
		<div class="row">
			<div class="col-xs-12">
				<button class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</button>
				<button type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
			</div>
			<input type="hidden" name="" id="id_user" value="{{$user->id}}">
			<div class="col-md-4">
				<div class="form-group padding-bottom-30">
					@if($user->avatar)
					<img class="crusor width350 avatar_show_edit" src="/storage/{{ $user->avatar }}">
					@else
					<img class="crusor width350 avatar_show_edit" src="/img/avatar_default.png">
					@endif
					<div>
						<input class="display-none" type="file" id="avatar_edit" value="{{ old('avatar') }}" name="avatar"/>
					</div>
					<div class="has-error reset-error" id="error-avatar"></div>
				</div>
			</div>
			<div class="col-md-8">
				<div id="form-step-0" role="form" data-toggle="validator">
					<input type="hidden" name="id" value="{{ $user->id }}">
					<div class="mt-15">
						<label for="" class="field-compulsory required">Name</label>
						<input type="text" class="form-control" name="name" id="name" value="{{ $user->name }}" placeholder="Enter name">
						<div class="has-error reset-error" id="error-name"></div>
					</div>
					<div class="mt-15">
						<label for="" class="field-compulsory required">Email</label>
						<input type="email" class="form-control" name="email" id="email" value="{{ $user->email }}" placeholder="Enter email">
						<div class="has-error reset-error" id="error-email"></div>
					</div>
					<div class="mt-15">
						<label for="" class="field-compulsory">Password</label>
						<input type="password" class="form-control" name="password" id="password" placeholder="Enter new password">
						<div class="has-error reset-error" id="error-password"></div>
					</div>
					<div class="mt-15">
						<label>Role</label>
						<select id="role" name="role" class="form-control">
							<option value="{{ UserType::USER }}" {{ $user->role == UserType::USER ? "selected" : "" }}>User</option>
							<option value="{{ UserType::ADMIN }}" {{ $user->role == UserType::ADMIN ? "selected" : "" }}>Admin</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	{{ Form::close() }}
	@include('modals.cancel_modal')
</div>
<!-- END Simple Editor Block -->
@endsection
@section('js')
	<script src="{{ asset('js/user.js') }}"></script>
	@include('common.flash-message')
@endsection
