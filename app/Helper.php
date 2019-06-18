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

function get_brightness($hex) {
    // returns brightness value from 0 to 255
    // strip off any leading #
    $hex = str_replace('#', '', $hex);
    if(strlen($hex) == 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
   
    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));
   
    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

function title_color($hex)
{
    if(get_brightness($hex) > 130) {
        return ' black';
    } else {
        return ' white';
    }
}

function getLinkTargetAttribute()
{
    $target = \App\Setting::fetch('window_target');

    if($target === 'current') {
        return '';
    } else {
        return ' target="' . $target . '"';
    }
}



function className($name)
{
    $name = preg_replace('/\PL/u', '', $name);
    return $name;
}

