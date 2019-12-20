@extends('layouts.app')
@section('content')
<ul class="breadcrumb breadcrumb-top">
    <li>Bots</li>
</ul>
<!-- END Datatables Header -->

<!-- Datatables Content -->

<div class="block full">
    <div class="block-title">
        <h2><strong>List bots</strong></h2>
        <a href="{{ route('bots.create') }}" class="btn-pull-right btn btn-md btn-primary"><i class="fa fa-plus-circle"></i> Create</a>
    </div>

    <div class="table-responsive">
        @include('bots.modal')
        <table class="table table-vcenter table-striped" data-form="deleteForm">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (count($bots) <= 0)
                    <p class="tbl-no-data"><i class="fa fa-info-circle"></i> No data</p>
                @else
                    @foreach($bots as $bot)
                        <tr>
                            <td>{{ Helper::indexNumber(app('request')->input('page'), config('paginate.perPage'), $loop->iteration) }}</td>
                            <td class="pl-20">{{ $bot->name }}</td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-default" href="{{ route('bots.edit', $bot->id) }}"><i class="fa fa-pencil"></i> Edit</a>

                                {{ Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['bots.destroy', 'bot' => $bot],
                                    'style' => 'display:inline',
                                    'class' => 'form-delete'
                                ]) }}
                                {{ Form::button('<i class="fa fa-trash-o"></i> Delete' , [
                                    'type' => 'DELETE',
                                    'class' => 'btn btn-sm btn-danger delete-btn',
                                    'title' => 'Delete'
                                ]) }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="text-center pagination-wrapper"> {{ $bots->appends(['search' => Request::get('search')])->render() }} </div>
    </div>
</div>
<!-- END Datatables Content -->
@endsection
@section('js')
    <script src="{{ asset('/js/bot.js') }}"></script>
    @include('common.flash-message')
@endsection
