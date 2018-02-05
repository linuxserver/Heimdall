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
                        alert(data);
                    });
                }
            });
        });
</script>