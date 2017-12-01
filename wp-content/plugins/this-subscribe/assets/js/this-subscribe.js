jQuery(function ($) {

    var subscriberMethods = {
        addMail: function (mail) {
            if (mail !== '') {

                // Ajax
                var data = {
                    'action': 'add_subscriber_mail',
                    'mail': mail
                };

                subscriberMethods.ajax(data);
            }
        },
        changeMail: function () {
            subscriberMethods.ajax({'action': 'change_subscriber_mail'});
        },
        abort: function () {
            subscriberMethods.ajax({'action': 'abort_subscriber'});
        },
        ajax: function (data) {
            $.post(ThisSubscribeAjax.ajaxurl, data, function (response) {
                response = JSON.parse(response);

                if (typeof response.html !== "undefined" && response.html !== '') {
                    $('.this-subscribe').html(response.html);
                }
            });
        }
    };

    // Ready
    if ($('.this-subscribe').length > 0 && typeof ThisSubscribeAjax !== "undefined") {

        // Send form
        $('.this-subscribe-form').on('submit', function (e) {
            e.preventDefault();

            var mail = $(this).find('[name="email"]').val();

            subscriberMethods.addMail(mail);

        });

        // Change mail
        $(document.body).on('click', '.this-subscribe-change', function (e) {
            e.preventDefault();

            subscriberMethods.changeMail();
        })

        // Abort subscriber
        $(document.body).on('click', '.this-subscribe-change', function (e) {
            e.preventDefault();

            subscriberMethods.abort();
        })
    }

});