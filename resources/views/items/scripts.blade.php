<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
        $( function() {

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

            var availableTags = @json(App\Application::all()->pluck('name'));

            $( "#appname" ).autocomplete({
                source: availableTags,
                select: function( event, ui ) {
                    var appvalue = ui.item.value;
                    appload(appvalue);
                }
            });
            $('#appname').on('keyup change', function(e) {
                $('#tile-preview .title').html($(this).val());
            })
            $('#apptype').on('change', function(e) {
                appload($(this).find('option:selected').text());
            });
            $('#appcolour').on('change', function(e) {
                $('#tile-preview .item').css('backgroundColor', $(this).val());
            })

            $('.tags').select2();

            function appload(appvalue) {
                $.post('{{ route('appload') }}', { app: appvalue }, function(data) {
                        // Main details
                        $('#appimage').html("<img src='"+data.iconview+"' /><input type='hidden' name='icon' value='"+data.icon+"' />");
                        $('input[name=colour]').val(data.colour);
                        $('select[name=class]').val(data.class);
                        hueb.setColor( data.colour );
                        $('input[name=pinned]').prop('checked', true);
                        // Preview details
                        $('#tile-preview .app-icon').attr('src', data.iconview);
                        $('#tile-preview .title').html(data.name);
                        if(data.config != null) {
                            $.get('/view/'+data.config, function(getdata) {
                                $('#sapconfig').html(getdata).show();
                            });
                        }
                    }, "json");

            }

        });
</script>
