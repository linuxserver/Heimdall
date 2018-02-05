<script>
        $( function() {
            var availableTags = [
            <?php
                $supported = App\Item::supportedOptions();
                foreach($supported as $sapp) {
                    echo '"'.$sapp.'",';
                }
            ?>
            ];
            $( "#appname" ).autocomplete({
                source: availableTags,
                select: function( event, ui ) {
                    $.post('/appload', { app: ui.item.value }, function(data) {
                        $('#appimage').html("<img src='/storage/"+data.icon+"' /><input type='hidden' name='icon' value='"+data.icon+"' />");
                        $('input[name=colour]').val(data.colour);
                    }, "json");
                }
            });
        });
</script>