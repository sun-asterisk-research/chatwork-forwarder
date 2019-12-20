@php use App\Enums\UserType; @endphp
@extends('layouts.app')
@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Users</li>
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
            <button class="btn btn-md btn-search"><i class="fa fa-search"></i> Search</button>
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
                <tr class="item-{{ $user->id }} user-item">
                    <td>{{ Helper::indexNumber(app('request')->input('page'), config('paginate.perPage'), $loop->iteration) }}</td>
                    <td class="text-center">
                        @if($user->avatar)
                            @if (substr($user->avatar, 0, 8) == 'https://')
                            <img src="{{ $user->avatar }}" alt="avatar" width="60px;">
                            @else
                            <img src="/storage/{{ $user->avatar }}" alt="avatar" width="60px;">
                            @endif
                        @else
                        <img src="/img/avatar_default.png" alt="avatar" width="60px;">
                        @endif
                    </td>
                    <td><a href="{{ route('users.edit', ['user' => $user]) }}">{{ $user->name }}</a></td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        @if($user->role === UserType::ADMIN)
                        <div class="label label-warning">Admin</div>
                        @else
                        <div class="label label-primary">User</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-default" href="{{ route('users.edit', ['user' => $user]) }}"><i class="fa fa-pencil"></i> Edit</a>
                        {{ Form::open([
                            'method' => 'DELETE',
                            'route' => ['users.destroy', 'user' => $user],
                            'style' => 'display:inline',
                            'class' => 'form-delete'
                            ]) }}
                            {{ Form::button('<i class="fa fa-trash-o"></i> Delete' , [
                            'type' => 'DELETE',
                            'class' => 'btn btn-sm btn-danger delete-btn',
                            'title' => 'Delete'
                            ]) }}
                        {{ Form::close() }}
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <div class="pagination-wrapper text-center"> {{ $users->appends(['search' => Request::get('search')])->render() }} </div>
    </div>
</div>
@include('modals.delete_user_confirm_modal')
<!-- END Datatables Content -->

@endsection
@section('script')
    <script src="{{ asset('js/user.js') }}"></script>
    @include('common.flash-message')
@endsection
