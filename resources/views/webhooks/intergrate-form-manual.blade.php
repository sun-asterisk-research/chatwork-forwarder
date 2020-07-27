<div class="modal fade detail-screen-modal" id="intergarteFormManual" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Intergarte Form Manual</h4>
            </div>
            <div class="modal-body">
                <div>
                    <h4>Step 1: Set up a new form or quiz</h4>
                    <ol>
                        <li data-outlined="false" class="">Go to <a href="https://forms.google.com" target="_blank" rel="noopener">forms.google.com <i class="fa fa-external-link" aria-hidden="true"></i></a>.</li>
                        <li>Click Blank <i class="fa fa-plus"></i>.</li>
                        <li>A new form will open and you can add, edit, or format text, images,... in a form.</li>
                    </ol>
                </div>
                <div>
                    <h4>Step 2: Choose where to save form responses</h4>
                    <ol>
                        <li>Open a form in <a href="https://forms.google.com" target="_blank" rel="noopener">Google Forms <i class="fa fa-external-link" aria-hidden="true"></i></a>.</li>
                        <li data-outlined="false" class="">In the top left under “Responses,” click <strong>Summary</strong>.</li>
                        <li data-outlined="false" class="">In the top right, click More <i class="fa fa-ellipsis-v" aria-hidden="true"></i> and click <strong>&nbsp;Select response destination</strong>.</li>
                        <li>Choose an option:&nbsp;
                            <ul>
                                <li><strong>Create a new spreadsheet: </strong>Creates a spreadsheet for responses in Google Sheets</li>
                                <li data-outlined="false" class=""><strong>Select existing spreadsheet: </strong>Choose from your existing spreadsheets in Google Sheets to store responses</li>
                            </ul>
                        </li>
                        <li>Click <strong>Create</strong> or <strong>Select</strong>.</li>
                    </ol>
                </div>
                <div>
                    <h4>Step 3: Set up file Code.gs and add Triggers</h4>
                    <ol>
                        <li>From within your response spreadsheet, select the menu item <strong>Tools</strong> > <strong>Script editor</strong>. <strong>Google Apps Script Dashboard</strong> will open.</li>
                        <li>Copy the script function and paste it into file Code.gs</li>
                        <li>Click Run <i class="fa fa-play" aria-hidden="true"></i> and Authorization this function.</li>
                        <li>Select the menu item <strong>Edit</strong> > <strong>Current project's Triggers</strong>. <strong>Google App Script</strong> will open.</li>
                        <li>Click <strong>create a new trigger</strong> and choose <strong>Sellect event type</strong> is "On form submit".</li>
                        <li>Click <strong>Save</strong>.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
