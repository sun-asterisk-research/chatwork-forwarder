@if(Session::has('messageSuccess'))
    <script>
        toastr.success('{{ Session::get("messageSuccess")["message"] }}', '{{ Session::get("messageSuccess")["status"] }}',{
            timeOut: 4000
        });
    </script>
@elseif(Session::has('messageFail'))
    <script>
        toastr.error('{{ Session::get("messageFail")["message"] }}', '{{ Session::get("messageFail")["status"] }}', {
            timeOut: 4000
        });
    </script>
@elseif(Session::has('errors'))
    <script>
        toastr.error('There are some invalid inputs. Please check the fields and fix them.', {
            timeOut: 4000
        });
    </script>
@endif
