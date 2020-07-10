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
                            <td>{{ $template->name }}</td>
                            <td class="wd-30">{{ $template->content }}</td>
                            <td class="pl-20">
                                @if($template->status === TemplateStatus::STATUS_PUBLIC)
                                    <div class="template-status label label-success">Public</div>
                                @else
                                    <div class="template-status label label-warning">UnPublic</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-default" href="{{ route('templates.edit', $template->id) }}"><i class="fa fa-pencil"></i> Edit</a>

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
@endsection

@section('js')
    <script src="{{ asset('/js/templates.js') }}"></script>
    @include('common.flash-message')
@endsection
