<?php

use App\Enums\TemplateStatus;

?>

@extends('layouts.app')

@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Templates</li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->
<div class="block full">
    <div class="block-title">
        <h2><strong>List templates</strong></h2>
        <a href="{{ route('templates.create') }}" class="btn-pull-right btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
    </div>

    <div class="table-responsive">
        @include('templates.modal')
        <table class="table table-vcenter table-striped" data-form="deleteForm">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Content</th>
                    <th>Status</th>
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
                            <td class="pl-20">
                                @if($template->status === TemplateStatus::STATUS_PUBLIC)
                                    <div class="template-status label label-success">Public</div>
                                @else
                                    <div class="template-status label label-warning">UnPublic</div>
                                @endif
                            </td>
                            <td class="text-center wd-20">
                                <a class="btn btn-sm btn-default" href="{{ route('templates.edit', $template->id) }}"><i class="fa fa-pencil"></i> Edit</a>
                                @if($template->status == TemplateStatus::STATUS_PUBLIC)
                                    <button class="btn btn-sm btn-warning btn-unpublic-wh btn-public-unpublic" data-toggle="modal" data-id="{{ $template->id }}" data-name="{{ $template->name }}" data-target="#exampleModal">Unpublic</button>
                                @else
                                    <button class="btn btn-sm btn-success btn-public-wh btn-public-unpublic" data-id="{{ $template->id }}" data-name="{{ $template->name }}">Public</button>
                                @endif

                                {{ Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['templates.destroy', 'template' => $template, 'page' => request('page')],
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
                                @include('templates.detail_template_modal')
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
