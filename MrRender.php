<?php

    /*
    ***************************************************************************

        DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
        Version 5, 2021
        Copyright (C) 2021 Christian Becher | phaziz.com <phaziz@gmail.com>

        Everyone is permitted to copy and distribute verbatim or modified
        copies of this license document, and changing it is allowed as long
        as the name is changed.

        DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
        TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
        0. YOU JUST DO WHAT THE FUCK YOU WANT TO!

        +++ Visit https://github.com/phaziz/MrRender/tree/master +++

    ***************************************************************************
    */

namespace MrRender;

class MrRender
{
    const _BASE_URL = 'http://localhost';
    const _BASE_TITLE = 'Welcome!';
    const _TPL_DIRECTORY = '/content';
    const _USE_CACHE = false;
    const _CACHE_DIRECTORY = '/cache/';
    const _CACHE_TIME = 86400;
    const _CACHE_FILEENDING = '.html';
    const _CDN_LINK = 'http://localhost/mr-render/content/';
    const _MAIN_TPL = 'startseite.php';
    const _404_TPL = '404.php';
    const _ERROR_TPL = 'error.php';

    function __construct($ROUTES_ARRAY) 
    {
        $REQUEST = $_SERVER['REQUEST_URI'];
        $UNIQUE = __DIR__ . self::_CACHE_DIRECTORY . base64_encode($REQUEST) . self::_CACHE_FILEENDING;

        if (!!self::_USE_CACHE && file_exists($UNIQUE) && time() - filemtime($UNIQUE) <= self::_CACHE_TIME) {
            $TPL_STR = @file_get_contents($UNIQUE);
            echo $TPL_STR;

            die();
        } else {
            $FLATTEN = $this->flatten($ROUTES_ARRAY);

            if ('' === $REQUEST || '/' === $REQUEST) {
                $TPL_STR = @file_get_contents(__DIR__ . self::_TPL_DIRECTORY . DIRECTORY_SEPARATOR . self::_MAIN_TPL);
                $TPL_STR = str_replace('{@ Navigation @}', $this->navigation($ROUTES_ARRAY, 0), $TPL_STR);
                $TPL_STR = str_replace('{@ PageName @}', self::_BASE_TITLE, $TPL_STR);
                $TPL_STR = str_replace('{@ CDNLink @}', self::_CDN_LINK, $TPL_STR);
                $NEW_CACHE = $TPL_STR . "\n" . '<!-- MrRender ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                if (!!self::_USE_CACHE) {
                    @file_put_contents($UNIQUE, $NEW_CACHE, LOCK_EX);
                }

                echo $NEW_CACHE;

                die();
            } else {
                if ('' !== array_search($REQUEST, $FLATTEN)) {
                    $TPL_STR = @file_get_contents(__DIR__ . self::_TPL_DIRECTORY . DIRECTORY_SEPARATOR . $FLATTEN[(array_search($REQUEST, $FLATTEN) + 1)]);
                    $TPL_STR = str_replace([
                        '{@ Navigation @}',
                        '{@ PageName @}',
                        '{@ CDNLink @}',
                        '{@ JsonNavigation @}'
                    ], [
                        $this->navigation($ROUTES_ARRAY, 0),
                        $FLATTEN[(array_search($REQUEST, $FLATTEN) - 1)],
                        self::_CDN_LINK,
                        $this->jsonNavigation($ROUTES_ARRAY)
                    ], $TPL_STR);

                    $NEW_CACHE = $TPL_STR . "\n" . '<!-- MrRender ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                    if (!!self::_USE_CACHE) {
                        @file_put_contents($UNIQUE, $NEW_CACHE, LOCK_EX);
                    }

                    echo $NEW_CACHE;

                    die();
                } else {
                    $TPL_STR = @file_get_contents(__DIR__ . self::_TPL_DIRECTORY . DIRECTORY_SEPARATOR . self::_404_TPL);
                    $TPL_STR = str_replace([
                        '{@ Navigation @}',
                        '{@ PageName @}',
                        '{@ Request @}',
                        '{@ Unique @}',
                        '{@ CDNLink @}'
                    ], [
                        $this->navigation($ROUTES_ARRAY, 0),
                        '404 - not found',
                        $REQUEST,
                        $UNIQUE,
                        self::_CDN_LINK
                    ], $TPL_STR);

                    echo $TPL_STR . "\n" . '<!-- MrRender ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                    die();
                }
            }
        }
    }

    private function flatten($ARRAY): array
    {
        if (!is_array($ARRAY)) {
            return array($ARRAY);
        }

        $RESULT = array();

        foreach ($ARRAY as $VALUE) {
            $RESULT = array_merge($RESULT, $this->flatten($VALUE));
        }

        return $RESULT;
    }

    private function navigation($ARRAY, $LEVEL): string
    {
        $REQUEST = $_SERVER['REQUEST_URI'];

        if (!empty($ARRAY)) {
            if ($LEVEL > 0) {
                $RET = '<ul class="topnav_' . $LEVEL . '">';
            } else {
                $RET = '<ul class="topnav">';
            }

            foreach ($ARRAY as $KEY => $VALUE) {
                if (!is_int($KEY)) {
                    $RET .=  '<li><a href="#">' . $KEY . '</a>';
                    $LEVEL++;
                    $RET .= $this->navigation($VALUE, $LEVEL);
                    $RET .=  '</li>';
                } else {
                    $ACTIVE = '';
                    $REQUEST = trim(str_replace(['/mr-render/', '/'], ['', ''], $REQUEST));

                    if(trim(strtolower($VALUE['name'])) === $REQUEST){
                        $ACTIVE = 'class="active"';
                    }

                    $RET .= '<li><a ' . $ACTIVE  . ' href="' . self::_BASE_URL . $VALUE['url'] . '">' . strtoupper($VALUE['name']) . '</a></li>';
                }
            }

            $RET .= '</ul>';

            return $RET;
        } else {
            return '';
        }
    }

    private function jsonNavigation($ARRAY): String
    {
        if (!!$ARRAY) {
            return json_encode($ARRAY, true);
        } else {
            throw new \InvalidArgumentException('$Array not set');
        }
    }
}