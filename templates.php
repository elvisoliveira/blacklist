<?php

class Template {

    public static function renderJSON($data = array(), $header = '') {
        if ($header && !empty($header)) {
            header($header);
        }
        header('Content-Type: application/json');
        print json_encode($data);
        exit();
    }

    public static function renderHTML($file, array $params = array()) {
        foreach ($params as $key => $value) {
            ${$key} = $value;
        }
        ob_start();
        include("./templates/{$file}.php");
        $output = ob_get_contents();
        ob_end_clean();
        print self::tidy($output);
        exit();
    }

    private static function tidy($buffer) {
        $evals = array(
            '/\>[^\S]+/s', // Whitespaces after tags, except space.
            '/[^\S]+\</s', // Whitespaces before tags, except space.
            '/(\s)+/s', // Shorten multiple whitespace sequences.
            '/<!--(.|\s)*?-->/', // HTML comments.
            '/\v(?:[\v\h]+)/' // Line breaks.
        );
        $entities = htmlentities($buffer, ENT_SUBSTITUTE);
        $greater = str_replace('&lt;', '<', $entities);
        $lower = str_replace('&gt;', '>', $greater);
        return preg_replace($evals, array('>', '<', '\\1', ''), $lower);
    }

}
