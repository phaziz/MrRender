<?php

    /*
        DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
        Version 5, 2021
        Copyright (C) 2021 Christian Becher | phaziz.com <phaziz@gmail.com>

        Everyone is permitted to copy and distribute verbatim or modified
        copies of this license document, and changing it is allowed as long
        as the name is changed.

        DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
        TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
        0. YOU JUST DO WHAT THE FUCK YOU WANT TO!

        +++ Visit https://github.com/phaziz +++
    */

    function startseitenContent()
    {
        $lottozahlen = range(1, 49);
        $zusatzzahlen = range(0 , 9);
        $tipp = '';

        for($i = 0; $i < 6; $i++){
            $tipp .= $lottozahlen[rand(1, 49)] . ' - ';
        }

        $zusatzzahl = '<strong>' . $zusatzzahlen[rand(0, 9)] . '</strong>';

        return '+ + + Lottozahlen-Tipp: <i>' . $tipp . '</i>' . $zusatzzahl . ' (PluginOutput) + + +';
    }