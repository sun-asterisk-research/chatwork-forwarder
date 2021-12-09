<div class="modal fade detail-screen-modal" id="contentType" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">What is content type</h4>
            </div>
            <div class="modal-body">
                <p>Choose between using normal text message content or with json message build with <a href="https://app.slack.com/block-kit-builder">Slack block kit builder</a>.</p>
                <p>If you choose json message, please note that it must be an array, incase your json payload from Slack kit builder like this:<p>
                <pre class="payload-example">
{
    "blocks": [
        {
            "type": "section",
            "text": {
                "type": "mrkdwn",
                "text": "Some message"
            }
        }
    ]
}
                </pre>
                <p>You will need to remove the "blocks" key, outer brackets and keep only the array content to fill in the content field:</p>
                <pre class="payload-example">
[
    {
        "type": "section",
        "text": {
            "type": "mrkdwn",
            "text": "Some message"
        }
    }
]
                </pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
