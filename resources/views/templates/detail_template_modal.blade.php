<?php use App\Enums\TemplateStatus; ?>
<style>
    label {
        margin-bottom: 0;
    }
    .mrb {
        margin-bottom: 8px;
    }
</style>
<div class="modal fade detail-screen-modal" id="detail-{{ $template->id }}" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group detail-template-modal">
                    <div class="mrb">
                        <label class="field-compulsory p-6 fl-left">Name</label>
                        <input type="text" class="form-control" value="{{ $template->name }}" readonly>
                    </div>
                    <div class="mrb">
                        <label class="field-compulsory p-6 fl-left">Param</label>
                        <textarea class="form-control" rows="7" readonly>{{ $template->params }}</textarea>
                    </div>
                    @if(count($template->conditions) > 0)
                        <div class="row pdl-15">
                            <label class="field-compulsory p-6 fl-left">Condition</label>
                        </div>
                        @for($i = 0; $i < count($template->conditions); $i++)
                        <div class="row mrb">
                            <div class="col-md-5">
                                <input class="form-control field-condition" id="field{{ $i }}" value="{{ $template->conditions[$i]->field }}"
                                    data-id="{{ $template->conditions[$i]->id }}" name="field[]" onchange="setChangeStatus(true)">
                            </div>
                            <div class="col-md-2">
                                {!! Form::select(
                                'operator[]',
                                ['==' => '==', '!=' => '!=', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=', 'Match' => 'Match'], $template->conditions[$i]->operator,
                                            ['class'=>'form-control operator', 'id' => 'operator'.$i, 'onchange' => "setChangeStatus(true)"]
                                            ) !!}
                            </div>
                            <div class="col-md-5">
                                <input class="form-control value" id="value{{ $i }}" value="{{ $template->conditions[$i]->value }}" name="value[]" onchange="setChangeStatus(true)">
                            </div>
                        </div>
                        @endfor
                    @endif
                    <div class="mrb">
                        <label class="field-compulsory p-6 fl-left">Content type</label>
                        <input type="text" class="form-control" value="{{ $template->content_type }}" readonly>
                    </div>
                    <div class="mrb">
                        <label class="field-compulsory p-6 fl-left">Content</label>
                        <textarea class="form-control" rows="3" readonly>{{ $template->content }}</textarea>
                    </div>
                    <div class="">
                        <label class="field-compulsory p-6 fl-left">Status</label>
                        <input type="text" class="form-control" {{ $template->status == TemplateStatus::STATUS_PUBLIC ? 'value=Public' : 'value=Unpublic' }} readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
