<?php

use App\Enums\TemplateStatus;

?>

@extends('layouts.app')

@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Templates</li>
</ul>
<!-- END Datatables Header -->
<div class="block">
    <div class="form-group row">
        <form method="GET" action="{{ route('admin.template.index') }}">
            <div class="col-xs-3">
                <input type="text" name="search[name]" class="form-control" placeholder="Enter template's name" value="{{ request('search')['name'] ?? '' }}">
            </div>
            <div class="col-xs-2">
                <select name="search[status]" class="form-control">
                    <option value="">All</option>
                    <option value="{{ TemplateStatus::STATUS_PRIVATE }}" {{ (request('search')['status'] ?? '') === strval(TemplateStatus::STATUS_PRIVATE) ? "selected" : "" }}>Private</option>
                    <option value="{{ TemplateStatus::STATUS_REVIEWING }}" {{ (request('search')['status'] ?? '') === strval(TemplateStatus::STATUS_REVIEWING) ? "selected" : "" }}>Reviewing</option>
                    <option value="{{ TemplateStatus::STATUS_PUBLIC }}" {{ (request('search')['status'] ?? '') === strval(TemplateStatus::STATUS_PUBLIC) ? "selected" : "" }}>Public</option>
                    <option value="{{ TemplateStatus::STATUS_UNPUBLIC }}" {{ (request('search')['status'] ?? '') === strval(TemplateStatus::STATUS_UNPUBLIC) ? "selected" : "" }}>UnPublic</option>
                </select>
            </div>
            <button class="btn btn-md btn-search"><i class="fa fa-search"></i> Search</button>
        </form>
    </div>
</div>
<!-- Datatables Content -->
<div class="block full">
    <div class="block-title">
        <h2><strong>List templates</strong></h2>
    </div>

    <div class="table-responsive">
        @include('templates.modal')
        <table class="table table-vcenter table-striped" data-form="deleteForm">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Content</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (count($templates) <= 0)
                    <p class="tbl-no-data"><i class="fa fa-info-circle"></i> No data</p>
                @else
                    @foreach ($templates as $template)
                        <tr class="item-{{ $template->id }} webtemplatehook-item">
                            <td>{{ Helper::indexNumber(app('request')->input('page'), config('paginate.perPage'), $loop->iteration) }}</td>
                            <td class="wd-10">{{ $template->name }}</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $template->content }}" readonly>
                            </td>
                            <td class="pl-20 text-center">
                            @if($template->status == TemplateStatus::STATUS_PRIVATE)
                                <div class="label status-private">Private</div>
                            @else
                                <select onchange="adminChangeStatus(value, {{ $template->id}})" class="mdb-select md-form change-status">
                                    <option value="{{TemplateStatus::STATUS_REVIEWING}}" @if ($template->status == TemplateStatus::STATUS_REVIEWING) {{ 'selected' }} @endif>Reviewing</option>
                                    <option value="{{TemplateStatus::STATUS_PUBLIC}}" @if ($template->status == TemplateStatus::STATUS_PUBLIC) {{ 'selected' }} @endif>Public</option>
                                    <option value="{{TemplateStatus::STATUS_UNPUBLIC}}" @if ($template->status == TemplateStatus::STATUS_UNPUBLIC) {{ 'selected' }} @endif>Unpublic</option>
                                </select>
                            @endif
                            </td>
                            <td class="text-center wd-20">
                                {{ Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['admin.template.destroy', 'template' => $template, 'page' => request('page')],
                                    'style' => 'display:inline',
                                    'class' => 'form-delete'
                                ]) }}
                                {{ Form::button('<i class="fa fa-trash-o"></i> Delete' , [
                                    'type' => 'DELETE',
                                    'class' => 'btn btn-sm btn-danger delete-btn',
                                    'title' => 'Delete'
                                ]) }}
                                {{ Form::close() }}

                                <a href="" class="btn btn-sm btn-default template-detail btn-detail" data-toggle="modal" data-target="#detail-{{ $template->id }}"><i class="fa fa-eye"></i> Detail</a>
                                @include('admins.templates.detail_template_modal')
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="text-center pagination-wrapper"> {{ $templates->appends(['search' => Request::get('search')])->render() }} </div>
    </div>
</div>
<!-- END Datatables Content -->
<!-- Modal -->
<div class="modal fade" id="publicModal" tabindex="-1" role="dialog" aria-labelledby="enableModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <input type="hidden" name="id">
                    <h4 class="modal-title" id="enableModalLabel">Public template: <span class="template-name"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="model-content">Are you sure you want to public this template?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-confirm-public">Public</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="unpublicModal" tabindex="-1" role="dialog" aria-labelledby="disableModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <input type="hidden" name="id">
                    <h4 class="modal-title" id="disableModalLabel">Unpublic template: <span class="template-name"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="model-content">Are you sure you want to unpublic this template?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-confirm-unpublic">Unpublic</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('/js/templates.js') }}"></script>
    @include('common.flash-message')
@endsection

@section('js')
    <script src="{{ asset('/js/templates.js') }}"></script>
    @include('common.flash-message')
@endsection
