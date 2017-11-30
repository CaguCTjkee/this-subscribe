jQuery(function($) {

    // Ready
    if($('.this-subscribe').length > 0) {
        $('.this-subscribe-form').on('submit', function(e) {
            e.preventDefault();

            var mail = $(this).find('[name="email"]').val();

            if(mail !== '') {
                // Ajax
                var data = {
                    'action' : 'add_mail',
                    'mail' : mail
                };

                jQuery.post(ThisSubscribeAjax.ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    console.log(response);

                    if(typeof response.html !== "undefined" && response.html !== '') {
                        // do some staff when we add new subs
                        $('.this-subscribe').html(response.html);
                    }
                });
            }
        });
    }

});