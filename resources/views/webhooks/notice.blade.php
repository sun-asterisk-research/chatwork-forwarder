<div class="modal fade detail-screen-modal" id="permission" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Slack App requirement</h4>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 0">
                    1. Your Slack App must have <code>chat:write</code> permission. You can add permission with the folowing url:
                </p>
                <p style="padding-left: 15px;">
                    <span style="font-weight: bold; text-decoration: underline">https://api.slack.com/apps/[YOUR_APP_ID]/oauth</span>
                    under
                    <span style="font-weight: bold;">Bot Token Scopes</span>
                </p>
                <p style="margin-bottom: 0">2. The Slack App must be added to your selected channel in order to send message.</p>
                <p style="padding-left: 15px;">
                    Click in the name of channel then select
                    <span style="font-weight: bold;">Integrations</span>
                    > <span style="font-weight: bold;">Add an app</span>
                </p>
            </div>
        </div>
    </div>
</div>
