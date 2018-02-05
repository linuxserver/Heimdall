$.when( $.ready ).then(function() {

    if($('.message-container').length) {
        setTimeout(
        function() 
        {
            $('.message-container').fadeOut();
        }, 3500);


    }

    $( "#sortable" ).sortable({
        stop: function (event, ui) {
            var idsInOrder = $("#sortable").sortable('toArray', {
                attribute: 'data-id'
            });
            $.post(
                '/order',
                { order:idsInOrder }
            );
        }
    
    });
    $("#sortable").sortable("disable");

    
    $('#app').on('click', '#config-button', function(e) {
        e.preventDefault();
        var app = $('#app');
        var active = (app.hasClass('header'));
        app.toggleClass('header');
        if(active) {
            $('.add-item').hide();
            $('.item-edit').hide();
            $('#app').removeClass('sidebar');
            $("#sortable").sortable("disable")
        } else {
            $("#sortable").sortable("enable")
            setTimeout(
                function() 
                {
                  $('.add-item').fadeIn();
                  $('.item-edit').fadeIn();
                }, 350);
    
        }
    }).on('click', '#add-item, #pin-item', function(e) {
        e.preventDefault();
        var app = $('#app');
        var active = (app.hasClass('sidebar'));
        app.toggleClass('sidebar');
        
    }).on('click', '.close-sidenav', function(e) {
        e.preventDefault();
        var app = $('#app');
        app.removeClass('sidebar');
        
    });
    $('#pinlist').on('click', 'a', function(e) {
        e.preventDefault();
        var current = $(this);
        var id = current.data('id');
        $.get('items/pintoggle/'+id+'/true', function(data) {
            var inner = $(data).filter('#sortable').html();
            $('#sortable').html(inner);
            current.toggleClass('active');
        });
    });
    
});