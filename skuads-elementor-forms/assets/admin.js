/* global jQuery, skuadsForms */
(function ($, ids) {
    if (!ids) {
        return;
    }

    $(function () {
        var manualSync = $('#' + ids.slug + '-form-manualsync');
        var options = $('#' + ids.slug + '-form-options');
        var saved = $('#' + ids.saved);

        manualSync.on('submit', function (event) {
            event.preventDefault();
            var button = manualSync.find('input[type=submit]');
            var buttonText = button.val();
            var feedback = manualSync.find('.' + ids.slug + '-feedback');

            $.ajax({
                type: 'POST',
                url: manualSync.attr('action'),
                data: manualSync.serialize(),
                beforeSend: function () {
                    button.prop('disabled', true);
                    button.val('Syncing ...');
                    feedback.html('');
                },
                success: function () {
                    button.prop('disabled', false);
                    button.val(buttonText);
                    feedback.html(
                        '<div class="notice is-dismissible inline notice-success" style="max-width:25em">' +
                        '<p><b>Entries synced successfully</b></p>' +
                        '</div>'
                    );
                }
            });
        });

        options.on('submit', function () {
            var button = options.find('#' + ids.slug + '-setup');
            button.prop('disabled', true);
            button.val('Connecting ...');
        });

        $('#' + ids.siteKey + ',#' + ids.apiToken).on('change', function () {
            saved.val(Date.now());
        });

        $('#' + ids.enabled).on('change', function (event) {
            if (event.target.value) {
                saved.val(Date.now());
            }
            $('.depends-on-enabled').toggleClass('hidden');
        });
    });
})(jQuery, window.skuadsForms);
