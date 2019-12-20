<?php use App\Enums\UserType; ?>

@extends('layouts.app')
@section('content')

<ul class="breadcrumb breadcrumb-top">
    <li><a href="{{ route('users.index') }}">Users</a></li>
    <li>Create</li>
</ul>

<!-- Simple Editor Block -->
<div class="block">
	<!-- Simple Editor Title -->
	<div class="block-title">
		<h2><strong>New user</strong></h2>
	</div>
	<!-- END Simple Editor Title -->

	<!-- Simple Editor Content -->
	{{ Form::open(['url' => route('users.store'), 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'user-form form-horizontal form-bordered padding-l-r-10']) }}
	<div class="form-group row">
		<div class="row">
			<div class="col-xs-12">
				<button type="submit" class="btn btn-sm btn-primary float-right"><i class="fa fa-check"></i> Save</button>
				<button class="btn btn-sm btn-default float-right cancel-btn"><i class="fa fa-times"></i> Cancel</button>
			</div>
			<div class="col-md-4">
				<div class="form-group padding-bottom-30">
					<img class="crusor width350" id="avatar_show" src="/img/avatar_default.png"><br>
					<div>
						<input class="display-none" type="file" id="avatar" value="{{ old('avatar') }}" name="avatar"/>
					</div>
					@error('avatar')
						<div class="has-error">
							<span class="help-block">{{ $message }}</span>
						</div>
					@enderror
				</div>
			</div>
			<div class="col-md-8">
				<div id="form-step-0" role="form" data-toggle="validator">
					<div class="mt-15">
						<label for="" class="field-compulsory required">Name</label>
						<input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="Enter name">
						@error('name')
						<div class="has-error">
							<span class="help-block">{{ $message }}</span>
						</div>
						@enderror
					</div>
					<div class="mt-15">
						<label for="" class="field-compulsory required">Email</label>
						<input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" placeholder="Enter email">
						@error('email')
						<div class="has-error">
							<span class="help-block">{{ $message }}</span>
						</div>
						@enderror
					</div>
					<div class="mt-15">
						<label for="" class="field-compulsory required">Password</label>
						<input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
						@error('password')
						<div class="has-error">
							<span class="help-block">{{ $message }}</span>
						</div>
						@enderror
					</div>
					<div class="mt-15">
						<label>Role</label>
						<select id="role" name="role" class="form-control">
							 <option value="{{ UserType::USER }}" {{ old('role') == UserType::USER ? "selected" : "" }}>User</option>
							 <option value="{{ UserType::ADMIN }}" {{ old('role') == UserType::ADMIN ? "selected" : "" }}>Admin</option>
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
