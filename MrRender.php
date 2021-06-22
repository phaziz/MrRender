<?php

    /*
    ***************************************************************************

        DO what THE FUCK YOU WANT TO PUBLIC LICENSE
        Version 5, 2021
        Copyright (C) 2021 Christian Becher | phaziz.com <phaziz@gmail.com>

        Everyone is permitted to copy and distribute verbatim or modified
        copies of this license document, and changing it is allowed as long
        as the name is changed.

        DO what THE FUCK YOU WANT TO PUBLIC LICENSE
        TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
        0. YOU JUST DO what THE FUCK YOU WANT TO!

        +++ Visit https://github.com/phaziz/MrRender/tree/master +++

    ***************************************************************************
    */

namespace MrRender;

class MrRender
{

    const baseUrl = 'http://localhost';
    const tplDirectory = '/content_templates';
    const contentDirectory = '/content';
    const pluginDirectory = '/plugins/';
    const contentFileending = '.php';
    const useCache = false;
    const cacheDirectory = '/cache/';
    const cacheTime = 86400;
    const cacheFileending = '.html';
    const cdnLink = 'http://localhost/mr-render/content_templates/';
    const tpl404 = '404.php';
    const tplError = 'error.php';
    const debugger = false;

    function __construct($routesArray = [], $pluginsArray = [])
    {
        $flatten = $this->flatten($routesArray);
        $request = $_SERVER['REQUEST_URI'];
        $unique = __DIR__ . self::cacheDirectory . base64_encode($request) . self::cacheFileending;

        if (!!self::useCache && file_exists($unique) && time() - filemtime($unique) <= self::cacheTime) {
            $tplString = file_get_contents($unique);
            echo $tplString;

            die();
        } else {
            if ('' !== array_search($request, $flatten)) {
                $tplString = file_get_contents(__DIR__ . self::tplDirectory . DIRECTORY_SEPARATOR . $flatten[(array_search($request, $flatten) + 1)]);
                                
                $tplString = str_replace([
                    '{@ content @}',
                    '{@ navigation @}',
                    '{@ pageName @}',
                    '{@ cdnLink @}',
                    '{@ jsonNavigation @}'
                ], [
                    $this->content(strtolower($flatten[(array_search($request, $flatten) - 1)])),
                    $this->navigation($routesArray, 0),
                    $flatten[(array_search($request, $flatten) - 1)],
                    self::cdnLink,
                    $this->jsonNavigation($routesArray)
                ], $tplString);

                $tplString = $this->pluginLoadr($tplString, $pluginsArray);

                if(true === self::debugger && false === self::useCache){
                    $tplString .= $this->debugMrRender($request, $unique, $pluginsArray);
                }

                $newCache = $tplString . "\n" . '<!-- MrRender ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                if (!!self::useCache) {
                    file_put_contents($unique, $newCache, LOCK_EX);
                }

                echo $newCache;

                die();
            } else {
                $tplString = @file_get_contents(__DIR__ . self::tplDirectory . DIRECTORY_SEPARATOR . self::tpl404);
                $tplString = str_replace([
                    '{@ navigation @}',
                    '{@ pageName @}',
                    '{@ request @}',
                    '{@ unique @}',
                    '{@ cdnLink @}'
                ], [
                    $this->navigation($routesArray, 0),
                    '404 - not found',
                    $request,
                    $unique,
                    self::cdnLink
                ], $tplString);

                if (true === self::debugger && false === self::useCache) {
                    $tplString .= $this->debugMrRender($request, $unique);
                }

                echo $tplString . "\n" . '<!-- MrRender ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                die();
            }
        }
    }

    private function pluginLoadr($tplString = '', $pluginsArray = []) : string
    {
        $ret = '';

        if('' !== $tplString){
            foreach($pluginsArray as $plugin){
                if(file_exists(__DIR__ . self::pluginDirectory . $plugin['name'] . '.php')){
                    require_once __DIR__ . self::pluginDirectory . $plugin['name'] . '.php';
                    
                    $tplString = str_replace('{@ plugin[' . $plugin['name'] . '] @}', $plugin['name'](), $tplString);
                }
            }

            $ret = $tplString;
        }

        return $ret;
    }

    private function flatten($arr = []): array
    {
        if (!is_array($arr)) {
            return array($arr);
        }

        $RESULT = [];

        foreach ($arr as $val) {
            $RESULT = array_merge($RESULT, $this->flatten($val));
        }

        return $RESULT;
    }

    private function debugMrRender($request = '', $unique = '', $pluginsArray = []): string
    {
        $pluginString = '';

        foreach($pluginsArray as $plugin){
            $pluginString .= $plugin['name'] . ' ';
        }

        $tplString  = '';
        $tplString .= '<div style="background:#ff0066;color:#fff;padding: 25px 25px 25px 25px;">';
        $tplString .= '<p><strong>MrRender Debug-Output:</strong></p>';
        $tplString .= '<p>$request: ' . $request . '</p>';
        $tplString .= '<p>Cache-File: ' . $unique . '</p>';
        $tplString .= '<p>baseUrl: ' . self::baseUrl . '</p>';
        $tplString .= '<p>tplDirectory: ' . self::tplDirectory . '</p>';
        $tplString .= '<p>contentDirectory: ' . self::contentDirectory . '</p>';
        $tplString .= '<p>contentFileending: ' . self::contentFileending . '</p>';
        $tplString .= '<p>useCache: ' . self::useCache . '</p>';
        $tplString .= '<p>cacheDirectory: ' . self::cacheDirectory . '</p>';
        $tplString .= '<p>cacheTime: ' . self::cacheTime . '</p>';
        $tplString .= '<p>cacheFileending: ' . self::cacheFileending . '</p>';
        $tplString .= '<p>cdnLink: ' . self::cdnLink . '</p>';
        $tplString .= '<p>tpl404: ' . self::tpl404 . '</p>';
        $tplString .= '<p>tplError: ' . self::tplError . '</p>';
        $tplString .= '<p>plugnis: ' . $pluginString . '</p>';
        $tplString .= '<p>debugger: ' . self::debugger . '</p>';
        $tplString .= '</div>';

        return $tplString;
    }

    private function content($what = null): string
    {
        $ret = '';

        if (null !== $what && file_exists(__DIR__ . self::contentDirectory . '/' . $what . self::contentFileending)) {
            $ret .= file_get_contents(__DIR__ . self::contentDirectory . '/' . $what . self::contentFileending);
        }

        return $ret;
    }

    private function navigation($arr = [], $level = null): string
    {
        $request = $_SERVER['REQUEST_URI'];

        if (!empty($arr)) {
            if ($level > 0) {
                $ret = '<ul class="topnav_' . $level . '">';
            } else {
                $ret = '<ul class="topnav">';
            }

            foreach ($arr as $KEY => $val) {
                if (!is_int($KEY)) {
                    $ret .=  '<li><a href="#">' . $KEY . '</a>';
                    $level++;
                    $ret .= $this->navigation($val, $level);
                    $ret .=  '</li>';
                } else {
                    $active = '';
                    $request = trim(str_replace(['/mr-render/', '/'], ['', ''], $request));

                    if(trim(strtolower($val['name'])) === $request){
                        $active = 'class="active"';
                    }

                    $ret .= '<li><a ' . $active  . ' href="' . self::baseUrl . $val['url'] . '">' . strtoupper($val['name']) . '</a></li>';
                }
            }

            $ret .= '</ul>';

            return $ret;
        } else {
            return '';
        }
    }

    private function jsonNavigation($arr = []): String
    {
        if (!!$arr) {
            return json_encode($arr, true);
        } else {
            return '';
        }
    }

}