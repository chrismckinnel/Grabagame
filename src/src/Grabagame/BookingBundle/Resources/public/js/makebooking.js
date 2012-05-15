$('#onBehalf').on('change', function(e) {
    $('#onBehalfName').slideToggle('fast', function() {
        $('#onBehalfName input').each(function() {
            if ($(this).attr('disabled') === 'disabled') {
                $(this).removeAttr('disabled');
            } else {
                $(this).attr('disabled', 'disabled');
            }
        });
    });
});
