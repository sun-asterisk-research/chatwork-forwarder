@extends('layouts.app')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Tables</li>
    <li><a href="">List user</a></li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->
<div class="block full">
    <div class="block-title">
        <h2><strong>List user</strong></h2>
        <a href="{{ route('users.create') }}" class="btn-pull-right btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
    </div>

    <div class="table-responsive">
        <table id="user-table" class="table table-vcenter table-condensed table-bordered">
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th class="text-center">Avatar</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- END Datatables Content -->

@endsection
@section('script')
<script src="{{ asset('js/user.js') }}"></script>
<script>
    var current_user_id = {{Auth::user()->id}};
</script>
@endsection
