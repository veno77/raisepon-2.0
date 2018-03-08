<?php

function type2ponid ($slot, $pon_port) {
        $slot = decbin($slot);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $pon_id = bindec($slot . $pon_port);
        return $pon_id;
}


echo type2ponid("1","1");

?>

