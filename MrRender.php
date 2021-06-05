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
    const _TPL_DIRECTORY = '/content_templates';
    const _CONTENT_DIRECTORY = '/content';
    const _CONTENT_FILEENDING = '.php';
    const _USE_CACHE = false;
    const _CACHE_DIRECTORY = '/cache/';
    const _CACHE_TIME = 86400;
    const _CACHE_FILEENDING = '.html';
    const _CDN_LINK = 'http://localhost/mr-render/content/';
    const _404_TPL = '404.php';
    const _ERROR_TPL = 'error.php';
    const _DEBUG = false;

    function __construct($ROUTES_ARRAY) 
    {
        $FLATTEN = $this->flatten($ROUTES_ARRAY);
        $REQUEST = $_SERVER['REQUEST_URI'];
        $UNIQUE = __DIR__ . self::_CACHE_DIRECTORY . base64_encode($REQUEST) . self::_CACHE_FILEENDING;

        if (!!self::_USE_CACHE && file_exists($UNIQUE) && time() - filemtime($UNIQUE) <= self::_CACHE_TIME) {
            $TPL_STR = @file_get_contents($UNIQUE);
            echo $TPL_STR;

            die();
        } else {
            if ('' !== array_search($REQUEST, $FLATTEN)) {
                $TPL_STR = @file_get_contents(__DIR__ . self::_TPL_DIRECTORY . DIRECTORY_SEPARATOR . $FLATTEN[(array_search($REQUEST, $FLATTEN) + 1)]);
                $TPL_STR = str_replace([
                    '{@ Content @}',
                    '{@ Navigation @}',
                    '{@ PageName @}',
                    '{@ CDNLink @}',
                    '{@ JsonNavigation @}'
                ], [
                    $this->content(strtolower($FLATTEN[(array_search($REQUEST, $FLATTEN) - 1)])),
                    $this->navigation($ROUTES_ARRAY, 0),
                    $FLATTEN[(array_search($REQUEST, $FLATTEN) - 1)],
                    self::_CDN_LINK,
                    $this->jsonNavigation($ROUTES_ARRAY)
                ], $TPL_STR);

                if(true === self::_DEBUG && false === self::_USE_CACHE){
                    $TPL_STR .= $this->debugMrRender($REQUEST, $UNIQUE);
                }

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

                if (true === self::_DEBUG && false === self::_USE_CACHE) {
                    $TPL_STR .= $this->debugMrRender($REQUEST, $UNIQUE);
                }

                echo $TPL_STR . "\n" . '<!-- MrRender ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                die();
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

    private function debugMrRender($REQUEST, $UNIQUE): string
    {
        $TPL_STR = '';
        $TPL_STR .= '<div style="background:#ff0066;color:#fff;padding: 25px 25px 25px 25px;">';
        $TPL_STR .= '<p><strong>MrRender Debug-Output:</strong></p>';
        $TPL_STR .=  '<p>$REQUEST: ' . $REQUEST . '</p>';
        $TPL_STR .=  '<p>Cache-File: ' . $UNIQUE . '</p>';
        $TPL_STR .=  '<p>_BASE_URL: ' . self::_BASE_URL . '</p>';
        $TPL_STR .=  '<p>_TPL_DIRECTORY: ' . self::_TPL_DIRECTORY . '</p>';
        $TPL_STR .=  '<p>_CONTENT_DIRECTORY: ' . self::_CONTENT_DIRECTORY . '</p>';
        $TPL_STR .=  '<p>_CONTENT_FILEENDING: ' . self::_CONTENT_FILEENDING . '</p>';
        $TPL_STR .=  '<p>_USE_CACHE: ' . self::_USE_CACHE . '</p>';
        $TPL_STR .=  '<p>_CACHE_DIRECTORY: ' . self::_CACHE_DIRECTORY . '</p>';
        $TPL_STR .=  '<p>_CACHE_TIME: ' . self::_CACHE_TIME . '</p>';
        $TPL_STR .=  '<p>_CACHE_FILEENDING: ' . self::_CACHE_FILEENDING . '</p>';
        $TPL_STR .=  '<p>_CDN_LINK: ' . self::_CDN_LINK . '</p>';
        $TPL_STR .=  '<p>_404_TPL: ' . self::_404_TPL . '</p>';
        $TPL_STR .=  '<p>_ERROR_TPL: ' . self::_ERROR_TPL . '</p>';
        $TPL_STR .=  '<p>_DEBUG: ' . self::_DEBUG . '</p>';
        $TPL_STR .= '</div>';

        return $TPL_STR;
    }

    private function content($WHAT): string
    {
        $ret = '';

        if (file_exists(__DIR__ . self::_CONTENT_DIRECTORY . '/' . $WHAT . self::_CONTENT_FILEENDING)) {
            $ret .= @file_get_contents(__DIR__ . self::_CONTENT_DIRECTORY . '/' . $WHAT . self::_CONTENT_FILEENDING);
        }

        return $ret;
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