<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
        $( function() {

            var elem = $('.color-picker')[0];
            var hueb = new Huebee( elem, {
              // options
            });

            var availableTags = @json(App\Application::all()->pluck('name'));

            $( "#appname" ).autocomplete({
                source: availableTags,
                select: function( event, ui ) {
                    $.post('/appload', { app: ui.item.value }, function(data) {
                        $('#appimage').html("<img src='"+data.iconview+"' /><input type='hidden' name='icon' value='"+data.icon+"' />");
                        $('input[name=colour]').val(data.colour);
                        $('select[name=class]').val(data.class);
                        hueb.setColor( data.colour );
                        $('input[name=pinned]').prop('checked', true);
                        if(data.config != null) {
                            $.get('/view/'+data.config, function(getdata) {
                                $('#sapconfig').html(getdata).show();
                            });
                        }
                    }, "json");
                }
            });

            $('.tags').select2();

        });
</script>
