jQuery(function($) {

    // Ready
    if($('.this-subscribe').length > 0) {
        $('.this-subscribe').on('submit', function(e) {
            e.preventDefault();

            var mail = $(this).find('[name="email"]').val();

            if(mail !== '') {
                // Ajax
                var data = {
                    'action' : 'add_mail',
                    'mail' : mail
                };

                jQuery.post(ThisSubscribeAjax.ajaxurl, data, function(response) {
                    console.log('Got this from the server: ' + response);
                });
            }
        });
    }

});