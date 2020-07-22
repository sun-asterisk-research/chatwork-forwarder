<div class="modal fade detail-screen-modal" id="contentExample" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Message content example</h4>
            </div>
            <div class="modal-body">
                <p>Params:
                    <pre class="payload-example">
    {
        "type": "new_post",
        "user": {
            "name": "rasmus",
            "display_name": "Rasmus Lerdorf",
            "number_of_posts": 3
        },
        "post": {
            "post_title": "Javascript Tips & Trick",
            "post_url": "https://viblo.asia/p/javascript-tips",
            "published_at": "2019-11-06 13:01:05"
        }
    }
                    </pre>
                </p>
                <p>Content example:</p>
                <p class="well">
                    TO ALL >>> <br>
                    <br>
                    There is a original text <strong>@{!! $params.user.display_name !!}</strong><br>
                    There is a mapping syntax <strong>@{{$params.user.display_name}}</strong><br>
                    There is a mapping by regex syntax <strong>[[$params.user.display_name]]</strong><br>
                    <br>
                    To see how to improve your code in javascript, click the link below:<br>
                    <strong>@{{$params.post.post_title}}</strong> <strong>@{{$params.post.post_url}}</strong><br><br>
                    <i>Note: use</i> <strong style="color: red">$params.user.display_name</strong> or <strong style="color: red">user.display_name</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
