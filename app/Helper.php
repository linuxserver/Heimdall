<?php

function format_bytes($bytes, $is_drive_size = true, $beforeunit = '', $afterunit = '')
{
	$btype = ($is_drive_size === true) ? 1000 : 1024;
    $labels = array('B','KB','MB','GB','TB');
    for($x = 0; $bytes >= $btype && $x < (count($labels) - 1); $bytes /= $btype, $x++); // use 1000 rather than 1024 to simulate HD size not real size
    if($labels[$x] == "TB") return(round($bytes, 3).$beforeunit.$labels[$x].$afterunit);
    elseif($labels[$x] == "GB") return(round($bytes, 2).$beforeunit.$labels[$x].$afterunit);
    elseif($labels[$x] == "MB") return(round($bytes, 2).$beforeunit.$labels[$x].$afterunit);
    else return(round($bytes, 0).$beforeunit.$labels[$x].$afterunit);
}
