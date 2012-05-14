$('.delete').on('click', function(e) {
    $(this).closest('td').find('.actionConfirm').toggle();

    e.preventDefault();
});
