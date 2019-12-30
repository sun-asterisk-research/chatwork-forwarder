<?php use App\Enums\WebhookStatus; ?>

@extends('layouts.app')

@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Webhooks</li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->

<div class="block">
    <div class="form-group row">
        <form method="GET" action="{{ route('admin.webhooks.index') }}">
            <div class="col-xs-3">
                <input type="text" name="search[name]" class="form-control" placeholder="Enter webhook's name" value="{{ request('search')['name'] ?? '' }}">
            </div>
            <div class="col-xs-3">
            <select name="search[user]" class="select-select2" style="width: 100%;" data-placeholder="Choose a user">
                    <option></option>
                    @foreach($users as $key => $value)
                        <option value="{{ $value }}" {{ (request('search')['user'] ?? '') == $value ? 'selected' : '' }}>
                            {{ $key }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-2">
                <select name="search[status]" class="form-control">
                    <option value="">All</option>
                    <option value="{{ WebhookStatus::ENABLED }}" {{ request('search')['status'] === strval(WebhookStatus::ENABLED) ? "selected" : "" }}>Enable</option>
                    <option value="{{ WebhookStatus::DISABLED }}"  {{ request('search')['status'] === strval(WebhookStatus::DISABLED) ? "selected" : "" }}>Disable</option>
                </select>
            </div>
            <button class="btn btn-md btn-search"><i class="fa fa-search"></i> Search</button>
        </form>
    </div>
</div>

<div class="block full">
    <div class="block-title">
        <h2><strong>Webhooks list</strong></h2>
    </div>
    <div class="table-responsive">
        <table  class="table table-vcenter table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th class="webhook-description">Description</th>
                    <th>User</th>
                    <th>Chatwork Room</th>
                    <th class="text-center">Chatwork Room ID</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(count($webhooks) == 0)
                    <p class="tbl-no-data "><i class="fa fa-info-circle"></i> No data</p>
                @else
                    @foreach ($webhooks as $webhook)
                        <tr class="item-{{ $webhook->id }}">
                            <td>{{ Helper::indexNumber(app('request')->input('page'), config('paginate.perPage'), $loop->iteration) }}</td>
                            <td>{{ $webhook->name }}</td>
                            <td class="webhook-description">{{ $webhook->description }}</td>
                            <td>{{ $webhook->user->name }}</td>
                            <td>{{ $webhook->room_name }}</td>
                            <td class="text-center">{{ $webhook->room_id }}</td>
                            <td class="pl-20">
                                @if($webhook->status === WebhookStatus::ENABLED)
                                    <div class="webhook-status label label-success">Enabled</div>
                                @else
                                    <div class="webhook-status label label-warning">Disabled</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($webhook->status == WebhookStatus::ENABLED)
                                    <button class="btn btn-sm btn-warning btn-disable-wh btn-enable-disable" data-toggle="modal" data-id="{{ $webhook->id }}" data-name="{{ $webhook->name }}" data-target="#exampleModal">Disable</button>
                                @else
                                    <button class="btn btn-sm btn-success btn-enable-wh btn-enable-disable" data-id="{{ $webhook->id }}" data-name="{{ $webhook->name }}">Enable</button>
                                @endif

                                <a class="btn btn-sm btn-default" href="{{ route('admin.webhooks.show', ['webhook' => $webhook]) }}"><i class="fa fa-eye"></i> Detail</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="text-center pagination-wrapper"> {{ $webhooks->appends(['search' => Request::get('search')])->render() }} </div>
    </div>
</div>
<!-- END Datatables Content -->
@endsection
<script src="{{ mix('/js/custom.js') }}"></script>

<!-- Modal -->
<div class="modal fade" id="enableModal" tabindex="-1" role="dialog" aria-labelledby="enableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <input type="hidden" name="id">
                <h4 class="modal-title" id="enableModalLabel">Enable webhook: <span class="webhook-name"></span></h4>
            </div>
            <div class="modal-body">
                <p class="model-content">Are you sure you want to enable this webhook?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-confirm-enable">Enable</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="disableModal" tabindex="-1" role="dialog" aria-labelledby="disableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <input type="hidden" name="id">
                <h4 class="modal-title" id="disableModalLabel">Disable webhook: <span class="webhook-name"></span></h4>
            </div>
            <div class="modal-body">
                <p class="model-content">Are you sure you want to disable this webhook?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning btn-confirm-disable">Disable</button>
            </div>
        </div>
    </div>
</div>
@section('js')
    <script src="{{ asset('/js/webhook.js') }}"></script>
@endsection
