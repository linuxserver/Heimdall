$.when( $.ready ).then(function() {

    if($('.message-container').length) {
        setTimeout(
            function()
            {
                $('.message-container').fadeOut();
            }, 3500);
    }

    if($('.livestats-container').length) {
        $('.livestats-container').each(function(index){
            var id = $(this).data('id');
            var dataonly = $(this).data('dataonly');
            var increaseby = (dataonly == 1) ? 20000 : 1000;
            var container = $(this);
            var max_timer = 30000;
            var timer = 5000;
            (function worker() {
                $.ajax({
                    url: '/get_stats/'+id,
                    dataType: 'json',
                    success: function(data) {
                        container.html(data.html);
                        if(data.status == 'active') timer = increaseby;
                        else {
                            if(timer < max_timer) timer += 2000;
                        }
                    },
                    complete: function() {
                    // Schedule the next request when the current one's complete
                        setTimeout(worker, timer);
                    }
                });
            })();
        });

    }

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#appimage img').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#upload').change(function() {
        readURL(this);
    });
    /*$(".droppable").droppable({
        tolerance: "intersect",
        drop: function( event, ui ) {
            var tag = $( this ).data('id');
            var item = $( ui.draggable ).data('id');

            $.get('tag/add/'+tag+'/'+item, function(data) {
                if(data == 1) {
                    $( ui.draggable ).remove();
                } else {
                    alert('not added');
                }
            });

        }
      });*/

    $( '#sortable' ).sortable({
        stop: function (event, ui) {
            var idsInOrder = $('#sortable').sortable('toArray', {
                attribute: 'data-id'
            });
            $.post(
                '/order',
                { order:idsInOrder }
            );
        }

    });
    $('#sortable').sortable('disable');



    $('#app').on('click', '#config-button', function(e) {
        e.preventDefault();
        var app = $('#app');
        var active = (app.hasClass('header'));
        app.toggleClass('header');
        if(active) {
            $('.add-item').hide();
            $('.item-edit').hide();
            $('#app').removeClass('sidebar');
            $('#sortable').sortable('disable');
        } else {
            $('#sortable').sortable('enable');
            setTimeout(function() {
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

    }).on('click', '#test_config', function(e) {
        e.preventDefault();
        var apiurl = $('#create input[name=url]').val();

        var override_url = $('#create input[name="config[override_url]"]').val();
        if(override_url.length && override_url != '') {
            apiurl = override_url;
        }

        var data = {};
        data['url'] = apiurl;
        $('input.config-item').each(function(index){
            var config = $(this).data('config');
            data[config] = $(this).val();
        });

        $.post('/test_config', { data: data }, function(data) {
            alert(data);
        });

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
