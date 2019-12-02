<div class="modal fade detail-screen-modal" id="payloadExample" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Condition example</h4>
            </div>
            <div class="modal-body">
                <p>Params:
                    <pre class="payload-example">
        {
            "type": "new_post",
            "user": {
                "name": "rasmus",
                "user_display_name": "Rasmus Lerdorf",
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
                <p>Condition example:
                    <ul>
                        <li>User name equal annv</li>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <input type="text" class="form-control" value="$payload->user->name" disabled>
                            </div>
                            <div class="col-xs-2">
                                <select class="form-control" disabled>
                                    <option>==</option>
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <input type="text" class="form-control" value="rasmus" disabled>
                            </div>
                        </div>
                        <li>User number of post greater than 5</li>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <input type="text" class="form-control" value="$payload->user->number_of_posts" disabled>
                            </div>
                            <div class="col-xs-2">
                                <select class="form-control" disabled>
                                    <option>></option>
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <input type="text" class="form-control" value="5" disabled>
                            </div>
                        </div>
                    </ul>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
