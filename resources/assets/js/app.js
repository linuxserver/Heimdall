$.when( $.ready ).then(function() {

    var base = (document.querySelector('base') || {}).href;

    if($('.message-container').length) {
        setTimeout(
            function()
            {
                $('.message-container').fadeOut();
            }, 3500);
    }

    // from https://developer.mozilla.org/en-US/docs/Web/API/Page_Visibility_API
    // Set the name of the hidden property and the change event for visibility
    var hidden, visibilityChange;
    if (typeof document.hidden !== "undefined") { // Opera 12.10 and Firefox 18 and later support 
        hidden = "hidden";
        visibilityChange = "visibilitychange";
    } else if (typeof document.msHidden !== "undefined") {
        hidden = "msHidden";
        visibilityChange = "msvisibilitychange";
    } else if (typeof document.webkitHidden !== "undefined") {
        hidden = "webkitHidden";
        visibilityChange = "webkitvisibilitychange";
    }

    var livestatsRefreshTimeouts = [];
    var livestatsFuncs = [];
    var livestatsContainers = $('.livestats-container');
    function stopLivestatsRefresh() {
        for (var timeoutId of livestatsRefreshTimeouts) {
            window.clearTimeout(timeoutId);
        }
    }
    function startLivestatsRefresh() {
        for (var fun of livestatsFuncs) {
            fun();
        }
    }

    if (livestatsContainers.length > 0) {
        if (typeof document.addEventListener === "undefined" || hidden === undefined) {
            console.log("This browser does not support visibilityChange");
        } else {
            document.addEventListener(visibilityChange, function() {
                if (document[hidden]) {
                    stopLivestatsRefresh();
                } else {
                    startLivestatsRefresh();
                }
            }, false);
        }

        livestatsContainers.each(function(index){
            var id = $(this).data('id');
            var dataonly = $(this).data('dataonly');
            var increaseby = (dataonly == 1) ? 20000 : 1000;
            var container = $(this);
            var max_timer = 30000;
            var timer = 5000;
            var fun = function worker() {
                $.ajax({
                    url: base+'/get_stats/'+id,
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
                        livestatsRefreshTimeouts[index] = window.setTimeout(worker, timer);
                    }
                });
            };
            livestatsFuncs[index] = fun;
            fun();
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
                base+'/order',
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
        $('.config-item').each(function(index){
            var config = $(this).data('config');
            data[config] = $(this).val();
        });

        $.post(base+'/test_config', { data: data }, function(data) {
            alert(data);
        });

    });
    $('#pinlist').on('click', 'a', function(e) {
        e.preventDefault();
        var current = $(this);
        var id = current.data('id');
        var tag = current.data('tag');
        $.get(base+'/items/pintoggle/'+id+'/true/'+tag, function(data) {
            var inner = $(data).filter('#sortable').html();
            $('#sortable').html(inner);
            current.toggleClass('active');
        });
    });

});
