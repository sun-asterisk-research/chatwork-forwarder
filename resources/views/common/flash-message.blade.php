@if(Session::has('messageSuccess'))
<div class="alert alert-success alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>{{ Session::get('messageSuccess') }}</strong>
</div>
@endif
@if(Session::has('messageFail'))
<div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>{{ Session::get('messageFail') }}</strong>
</div>
@endif
