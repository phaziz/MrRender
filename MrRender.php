<?php

    /*
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
    */

    namespace MrRender;

    class MrRender
    {
        const version = '1.6.25';
        const baseUrl = 'http://localhost';
        const tplDirectory = '/content_templates';
        const contentDirectory = '/content';
        const pluginDirectory = '/plugins/';
        const contentFileending = '.php';
        const useCache = false;
        const cacheDirectory = '/cache/';
        const cacheFileending = '.html';
        const cdnLink = 'http://localhost/mr-render' . self::tplDirectory . '/';
        const tpl404 = '404.php';
        const tplError = 'error.php';
        const debugger = false;

        /**
         * @param array $routesArray
         * @param array $pluginsArray
         */
        function __construct($routesArray = [], $pluginsArray = [])
        {
            $flatten = $this->flatten($routesArray);
            $request = $_SERVER['REQUEST_URI'];
            $unique = __DIR__ . self::cacheDirectory . base64_encode($request) . self::cacheFileending;

            if (!!self::useCache && file_exists($unique)){
                echo file_get_contents($unique);
                
                die();
            } else {
                if ('' !== array_search($request, $flatten)){
                    $tplString = file_get_contents(__DIR__ . self::tplDirectory . DIRECTORY_SEPARATOR . $flatten[(array_search($request, $flatten) + 1)]);
                    
                    $tplString = str_replace(
                        [
                            '{@ content @}',
                            '{@ navigation @}',
                            '{@ pageName @}',
                            '{@ cdnLink @}'
                        ], 
                        [
                            $this->content(strtolower($flatten[(array_search($request, $flatten) - 1)])),
                            $this->navigation($routesArray, 0),
                            $flatten[(array_search($request, $flatten) - 1)],
                            self::cdnLink
                        ]
                    , $tplString);

                    $tplString = $this->pluginLoadr($tplString, $pluginsArray);

                    if('true' === $_GET['debug'] || true === self::debugger && false === self::useCache){
                        $tplString .= $this->debugMrRender($request, $unique, $pluginsArray);
                    }

                    $tplString .= "\n" . '<!-- MrRender v.' . self::version . ' ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                    if (!!self::useCache){
                        file_put_contents($unique, $tplString, LOCK_EX);
                    }

                    echo $tplString;

                    die();
                } else {
                    $tplString = file_get_contents(__DIR__ . self::tplDirectory . DIRECTORY_SEPARATOR . self::tpl404);
                    $tplString = str_replace(
                        [
                            '{@ navigation @}',
                            '{@ pageName @}',
                            '{@ request @}',
                            '{@ unique @}',
                            '{@ cdnLink @}'
                        ], 
                        [
                            $this->navigation($routesArray, 0),
                            '404 - not found',
                            $request,
                            $unique,
                            self::cdnLink
                        ]
                    , $tplString);

                    $tplString = $this->pluginLoadr($tplString, $pluginsArray);

                    if ('true' === $_GET['debug'] || true === self::debugger && false === self::useCache){
                        $tplString .= $this->debugMrRender($request, $unique);
                    }

                    echo $tplString . "\n" . '<!-- MrRender v.' . self::version . ' ' . date('Y-m-d, H:i:s') . ' | https://github.com/phaziz -->';

                    die();
                }
            }
        }

        /**
         * @param string $tplString
         * @param array $pluginsArray
         * @return string
         */
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

        /**
         * @param array $arr
         * @return array
         */
        private function flatten($arr = []): array
        {
            if (!is_array($arr)){
                return array($arr);
            }

            $RESULT = [];

            foreach ($arr as $val){
                $RESULT = array_merge($RESULT, $this->flatten($val));
            }

            return $RESULT;
        }

        /**
         * @param string $request
         * @param string $unique
         * @param array $pluginsArray
         * @return string
         */
        private function debugMrRender($request = '', $unique = '', $pluginsArray = []): string
        {
            $pluginString = '';

            foreach($pluginsArray as $plugin){
                $pluginString .= $plugin['name'] . ' ';
            }

            return '<div style="background:#ff0066;color:#fff;padding: 25px 25px 25px 25px;">' .
                '<p><strong>MrRender v.' . self::version . ' Debug-Output:</strong></p>' .
                '<p>$request: ' . $request . '</p>' .
                '<p>Cache-File: ' . $unique . '</p>' .
                '<p>baseUrl: ' . self::baseUrl . '</p>' .
                '<p>tplDirectory: ' . self::tplDirectory . '</p>' .
                '<p>contentDirectory: ' . self::contentDirectory . '</p>' .
                '<p>contentFileending: ' . self::contentFileending . '</p>' .
                '<p>useCache: ' . self::useCache . '</p>' .
                '<p>cacheDirectory: ' . self::cacheDirectory . '</p>' .
                '<p>cacheFileending: ' . self::cacheFileending . '</p>' .
                '<p>cdnLink: ' . self::cdnLink . '</p>' .
                '<p>tpl404: ' . self::tpl404 . '</p>' .
                '<p>tplError: ' . self::tplError . '</p>' .
                '<p>plugnis: ' . $pluginString . '</p>' .
                '<p>debugger: ' . self::debugger . '</p>' .
                '</div>';
        }

        /**
         * @param [type] $what
         * @return string
         */
        private function content($what = null): string
        {
            $ret = '';

            if (null !== $what && file_exists(__DIR__ . self::contentDirectory . '/' . $what . self::contentFileending)){
                $ret .= file_get_contents(__DIR__ . self::contentDirectory . '/' . $what . self::contentFileending);
            }

            return $ret;
        }

        /**
         * @param array $arr
         * @param [type] $level
         * @return string
         */
        private function navigation($arr = [], $level = null): string
        {
            $request = $_SERVER['REQUEST_URI'];

            if (!empty($arr)){
                if ($level > 0){
                    $ret = '<ul class="topnav_' . $level . '">';
                } else {
                    $ret = '<ul class="topnav">';
                }

                foreach ($arr as $key => $val){
                    if (!is_int($key)){
                        $level++;

                        $ret .=  '<li><a href="#">' . $key . '</a>' .                   
                            $this->navigation($val, $level) .
                            '</li>';
                    } else {
                        $request = trim(str_replace(['/mr-render/', '/'], ['', ''], $request));
                        $active = '';

                        if($request === trim(strtolower($val['name']))){
                            $active = ' class="active" ';
                        }

                        $ret .= '<li><a ' . $active  . ' href="' . self::baseUrl . $val['url'] . '">' . 
                            strtoupper($val['name']) . 
                            '</a></li>';
                    }
                }

                $ret .= '</ul>';

                return $ret;
            } else {
                return '';
            }
        }
    }