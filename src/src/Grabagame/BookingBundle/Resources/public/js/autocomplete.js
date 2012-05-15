$('#search_member').on('keypress', function() {

    var query = $(this).val();

    if (query.length > 2) {
        var url = $(this).parents('form').attr('action') + '?_format=json&q=' + query;
        
        $.getJSON(url, function(data) {
            if (data.members.length > 0) {
                var member;
                for (member in data.members) {
                    var row = '<tr>';
                    row    += '<td>'+data.members[0].memberId+'</td>';
                    row    += '<td>'+data.members[0].name+'</td>';
                    row    += '<td>'+data.members[0].email+'</td>';
                    row    += '<td><a href="'+data.members[0].viewLink+'">View</a> / <a href="'+data.members[0].editLink+'">Edit</a></td>';
                    row    += '</tr>';
                }
            } else {
                row = '<tr><td colspan="4">No members matched your query</td></tr>';
            }

            $('#member-search tbody').html(row);
        });
    }

});
