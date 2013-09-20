<?php
/*

For use with Handlebars.php

$handlebars = new Handlebars_Engine();
$handlebars->addHelper('paginate', ['PaginateHelper', 'paginate']);

*/

class PaginateHelper {
    public static function paginate ($template, $context, $args, $source) {
        $pagination = $context->get('pagination');
        $options = [];
        foreach (explode(' ', $args) as $arg) {
            if (substr_count($arg, '=') == 0) {
                continue;
            }
            $parts = explode('=', $arg);
            $options[$parts[0]] = trim($parts[1], '"');
        }
        $type = 'middle';
        if (isset($options['type'])) {
            $type = $options['type'];
        }
        $ret = '';
        $pageCount = $pagination['pageCount'];
        $page = intval($pagination['page']);
        $limit;
        if (isset($options['limit'])) {
            $limit = $options['limit'];
        }
        
        //page pageCount
        $newContext = [];
        switch ($type) {
            case 'middle':
            if (is_numeric($limit)) {
                $i = 0;
                $leftCount = ceil($limit / 2) - 1;
                $rightCount = $limit - $leftCount - 1;
                if ($page + $rightCount > $pageCount) {
                    $leftCount = $limit - ($pageCount - $page) - 1;
                }
                if ($page - $leftCount < 1) {
                    $leftCount = $page - 1;
                    $start = $page - $leftCount;
                    while ($i < $limit && $i < $pageCount) {
                        $newContext = ['n' => $start];
                        if ($start === $page) {
                            $newContext['active'] = true;
                            $ret .= $template->render($newContext);
                        }
                        $start++;
                        $i++;
                    }
                }
                } else {
                for ($i = 1; $i <= $pageCount; $i++) {
                    $newContext['n'] = $i;
                    if ($i === $page) {
                        $newContext['active'] = true;
                    }
                    $ret .= $template->render($newContext);
                }
            }
            break;
            
            case 'previous':
            if ($page === 1) {
                $newContext = ['disabled' => true, 'n' => 1 ];
                } else {
                $newContext = ['n' =>  $page - 1];
            }
            $ret .= $template->render($newContext);
            break;
            
            case 'next':
            $newContext = [];
            if ($page === $pageCount) {
                $newContext = ['disabled' => true, 'n' => $pageCount];
                } else {
                $newContext = ['n' =>  $page + 1];
            }
            $ret .= $template->render($newContext);
            break;
        }
        
        return $ret;
    }
}