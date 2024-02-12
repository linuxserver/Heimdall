<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
        $( function() {

            var base = (document.querySelector('base') || {}).href;

            var elem = $('.color-picker')[0];
            var hueb = new Huebee( elem, {
              // options
              setBGColor: '.set-bg-elem'
            });

            hueb.on( 'change', function( color, hue, sat, lum ) {
                $.get('{{ route('titlecolour') }}', {color}, function(data) {
                    $('#tile-preview .title').removeClass("black white");
                    $('#tile-preview .link').removeClass("black white");
                    $('#tile-preview .title').addClass(data);
                    $('#tile-preview .link').addClass(data);
                });
            })

            var availableTags = @json(App\Application::autocomplete());
            console.log(availableTags)
            $( "#appname" ).autocomplete({
                source: availableTags,
                select: function( event, ui ) {
                    event.preventDefault();
                    // appload(ui.item.value);
                    $( "#appname" ).val(ui.item.label)
                    $('#apptype').val(ui.item.value).change()
                }
            });
            // initial load
            $('#tile-preview .title').text($('#appname').val());
            $('#tile-preview .item').css('backgroundColor', $('#appcolour').val());
            $('#tile-preview .app-icon').attr('src', $('#appimage img').attr('src'));

            // Updates
            $('#appname').on('keyup change', function(e) {
                $('#tile-preview .title').text($(this).val());
            })
            $('#apptype').on('change', function(e) {
                appload($(this).find('option:selected').val());
            });
            $('#appcolour').on('change', function(e) {
                $('#tile-preview .item').css('backgroundColor', $(this).val());
            })

            $('#websiteiconoptions').on('click', '.iconbutton', function (e) {
                const src = $('.selecticon', this).attr('src')
                $('#appimage').html("<img src='"+src+"' /><input type='hidden' name='icon' value='"+src+"' />");
                $('#tile-preview .app-icon').attr('src', src);

            }).on('click', '.selectclose', function () {
                $('#websiteiconoptions').html('')
            })

            $('.tags').select2();
            document.getElementById('optdetails-button').addEventListener("click", function(event) {
                $('#apptype').select2({
                    placeholder: "Select...",
                    allowClear: true,
                    width: "100%"
                });
                }, {once: true});

            if($('#appurl').val() !== '') {
                if ($('#appurl').val().indexOf("://") !== -1) {
                    $('#appurl').parent().find('.help').hide()
                }
            }             
            if($('#website').val() !== '') {
                if ($('#website').val().indexOf("://") !== -1) {
                    $('#website').parent().find('.help').hide()
                }
            }             
            $('#appurl, #website').on('input', function () {
                if ($(this).val().indexOf("://") !== -1) {
                    $(this).parent().find('.help').hide()
                }
            })


            $('#searchwebsite').on('click', 'button.btn', function (e) {
                e.preventDefault()
                let websiteurl = $('#searchwebsite input').val()
                website = btoa(websiteurl)
                $.get(base + 'items/websitelookup/' + website, function (data) {
                    const url = new URL(websiteurl)
                    const websitedata = {}
                    const parser = new DOMParser()
                    const document = parser.parseFromString(data, 'text/html')

                    const links = document.getElementsByTagName('link')
                    websitedata.title = document.getElementsByTagName('title')[0].innerText
                    const metas = document.getElementsByTagName('meta')
                    const icons = []

                    for (let i = 0; i < metas.length; i++) {
                        if (metas[i].getAttribute('name') === 'description') {
                            websitedata.description = metas[i].getAttribute('content')
                        }
                    }

                    for (let i = 0; i < links.length; i++) {
                        const link = links[i]
                        const rel = link.getAttribute('rel')
                        if (rel) {
                            if (rel.toLowerCase().indexOf('icon') > -1) {
                                const href = link.getAttribute('href')
                                // Make sure href is not null / undefined
                                if (href) {
                                    if (href.toLowerCase().indexOf('https:') === -1 && href.toLowerCase().indexOf('http:') === -1 && href.indexOf('//') !== 0) {
                                        let finalBase = ''
                                        if (websiteurl.endsWith('/')) {
                                            const baseurl = websiteurl.split('/')
                                            baseurl.pop()
                                            finalBase = baseurl.join('/')
                                        } else {
                                            finalBase = websiteurl
                                        }

                                        let absoluteHref = finalBase

                                        if (href.indexOf('/') === 0) {
                                            absoluteHref += href
                                        } else {
                                            const path = url.pathname.split('/')
                                            path.pop()
                                            const finalPath = path.join('/')
                                            absoluteHref += finalPath + '/' + href
                                        }
                                        icons.push(encodeURI(absoluteHref))
                                    } else if (href.indexOf('//') === 0) {
                                        // Absolute url with no protocol
                                        const absoluteUrl = url.protocol + href
                                        icons.push(encodeURI(absoluteUrl))
                                    } else {
                                    // Absolute
                                        icons.push(encodeURI(href))
                                    }
                                }
                            }
                        }
                    }
                    websitedata.icons = icons

                    if ($('#appname').val() === '') $('#appname').val(websitedata.title)
                    if ($('#appurl').val() === '') $('#appurl').val(websiteurl)
                    $('input[name=pinned]').prop('checked', true);
                    // $('#appimage').html("<img src='"+websitedata.icons[0]+"' /><input type='hidden' name='icon' value='"+websitedata.icons[0]+"' />");
                    $('#tile-preview .app-icon').attr('src', $('#appimage img').attr('src'));
                    $('#tile-preview .title').text($('#appname').val());
                    $('#websiteiconoptions').html('<div class="header"><span>Select Icon</span><span class="selectclose">Close</span></div><div class="results"></div>')
                    icons.forEach(icon => {
                        $('#websiteiconoptions .results').append('<div class="iconbutton"><img class="selecticon" src="' + icon + '" /></div>')
                    })
                    console.log(websitedata)
                })
                console.log(website)
            })

            $('.optdetails button.dark').on('click', function (e) {
                e.preventDefault()
                $(this).parent().next().toggleClass('active')
            })

            function appload(appvalue) {
                if(appvalue == 'null') {
                    $('#sapconfig').html('').hide();
                    $('#tile-preview .app-icon').attr('src', '/img/heimdall-icon-small.png');
                    $('#appimage').html("<img src='/img/heimdall-icon-small.png' />");
                    $('#sapconfig').html('').hide();
                } else {
                    $.post('{{ route('appload') }}', { app: appvalue }, function(data) {
                        // Main details
                        $('#appimage').html("<img src='"+data.iconview+"' /><input type='hidden' name='icon' value='"+data.iconview+"' />");
                        $('input[name=colour]').val(data.colour);
                        $('select[name=appid]').val(data.appid);
                        hueb.setColor( data.colour );
                        $('input[name=pinned]').prop('checked', true);
                        // Preview details
                        $('#tile-preview .app-icon').attr('src', data.iconview);
                        $('#appdescription').val(data.description);
                        if($('#appname').val() === '') {
                            $('#appname').val(data.name)
                        }
                        $('#tile-preview .title').text($('#appname').val());
                        if(data.custom != null) {
                            $.get(base+'view/'+data.custom, function(getdata) {
                                $('#sapconfig').html(getdata).show();
                            });
                        } else {
                            $('#sapconfig').html('').hide();
                        }
                    }, "json");
                }

            }

        });
</script>
