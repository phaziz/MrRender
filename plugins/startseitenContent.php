<?php

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