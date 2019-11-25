@extends('layouts.app')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Home</li>
    <li><a href="">Bots</a></li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->

<div class="block full">
    <div class="block-title">
        <h2><strong>List bots</strong></h2>
    </div>

    <div class="table-responsive">
        <table id="bot-datatable" class="table table-vcenter table-condensed table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>CW ID</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bots as $bot)
                    <tr>
                        <td class="pl-20">{{ $bot->name }}</td>
                        <td class="pl-20">{{ $bot->cw_id }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                                <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- END Datatables Content -->
@endsection
