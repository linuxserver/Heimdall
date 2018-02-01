$.when( $.ready ).then(function() {
    $('.color-picker').each( function( i, elem ) {
        var hueb = new Huebee( elem, {
          // options
        });
     });
    $('#app').on('click', '#config-button', function(e) {
        e.preventDefault();
        var app = $('#app');
        var active = (app.hasClass('header'));
        app.toggleClass('header');
        if(active) {
            $('.add-item').hide();
            $('#app').removeClass('sidebar');
        } else {
            setTimeout(
                function() 
                {
                  $('.add-item').fadeIn();
                }, 350);
    
        }
    }).on('click', '#add-item', function(e) {
        e.preventDefault();
        var app = $('#app');
        var active = (app.hasClass('sidebar'));
        app.toggleClass('sidebar');
        
    });
});