$(document).ready(function () {
  $('.mapping-form').on('click', '.cancel-btn', function (e) {
    e.preventDefault();
    $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
      .on('click', '#cancel-btn', function () {
        let webhook_id = window.location.pathname.match(/\d+/)[0];
        window.location.pathname = '/webhooks/' + webhook_id + '/edit';
      });
  });
});
