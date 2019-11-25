<?php use App\Enums\WebhookStatus; ?>

@extends('layouts.app')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">

@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Webhooks</li>
    <li><a href="">List</a></li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->
<div class="block full">
    <div class="block-title">
        <h2><strong>Webhooks list</strong></h2>
        <a href="{{ route('webhooks.create') }}" class="btn-pull-right btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
    </div>

    <div class="table-responsive">
        <table id="webhook-datatable" class="table table-vcenter table-condensed table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="webhook-description">Description</th>
                    <th>Chatwork Room</th>
                    <th>Chatwork Room ID</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($webhooks as $webhook)
                    <tr>
                        <td>{{ $webhook->name }}</td>
                        <td class="webhook-description">{{ $webhook->description }}</td>
                        <td>{{ $webhook->room_name }}</td>
                        <td>{{ $webhook->room_id }}</td>
                        <td class="text-center">{{ $webhook->status == WebhookStatus::ENABLED ? 'Enabled' : 'Disabled' }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
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
<script src="{{ mix('/js/custom.js') }}"></script>
