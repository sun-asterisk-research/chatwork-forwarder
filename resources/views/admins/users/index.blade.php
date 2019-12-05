@php use App\Enums\UserType; @endphp
@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
<ul class="breadcrumb breadcrumb-top">
    <li>Users</li>
    <li><a href="{{ route('users.index') }}">Lists</a></li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->
<div class="block">
    <div class="form-group row">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="col-xs-4">
                <input type="text" name="search[name]" class="form-control" placeholder="Enter user's name" value="{{ request('search')['name'] ?? '' }}">
            </div>
            <div class="col-xs-4">
                <input type="text" name="search[email]" class="form-control" placeholder="Enter user's email" value="{{ request('search')['email'] ?? '' }}">
            </div>
            <button class="btn btn-md btn-search"><i class="fa fa-search"></i> search</button>
        </form>
    </div>
</div>

<div class="block full">
    <div class="block-title">
        <h2><strong>List user</strong></h2>
        <a href="{{ route('users.create') }}" class="btn-pull-right btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th class="text-center">Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(Request::get('search') && count($users) == 0)
                <p class="tbl-no-data "><i class="fa fa-info-circle"></i> No matching records found</p>
                @elseif(count($users) == 0)
                <p class="tbl-no-data "><i class="fa fa-info-circle"></i> No data</p>
                @else
                @foreach($users as $user)
                <tr>
                    <td>{{ Helper::indexNumber(app('request')->input('page'), config('paginate.perPage'), $loop->iteration) }}</td>
                    <td class="text-center">
                        @if($user->avatar)
                            <img src="/storage/{{ $user->avatar }}" alt="avatar" width="60px;">
                        @else
                            <img src="/img/avatar_default.png" alt="avatar" width="60px;">
                        @endif
                    </td>
                    <td><a href="page_ready_user_profile.html">{{ $user->name }}</a></td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        @if($user->role === UserType::ADMIN)
                        <div class="label label-warning">Admin</div>
                        @else
                        <div class="label label-primary">User</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-default" href=""><i class="fa fa-pencil"></i> Edit</a>
                        <a class="btn btn-sm btn-danger" href=""><i class="fa fa-trash-o"></i> Delete</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <div class="pagination-wrapper"> {{ $users->appends(['search' => Request::get('search')])->render() }} </div>
    </div>
</div>
<!-- END Datatables Content -->

@endsection
@section('script')
<script src="{{ asset('js/user.js') }}"></script>
@endsection
