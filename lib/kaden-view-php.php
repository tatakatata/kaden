<?php

;

class Kaden_View_PHP{
    var $cx;

    function __construct($cx){
        $this->cx = $cx;
    }

    function view(){
        if( $this->cx->var['template'] ){
            $contents = $this->extract_template($this->cx->config['template_dir'] . '/' . $this->cx->var['template'] . '.html');
            if( $this->cx->var['fillin'] ){
                $fif =& new FillInForm;
                $opt = array(scalar => $contents) + $this->cx->var['fillin'];
                $contents = $fif->fill($opt);
            }

            if( $this->cx->var['encoding'] ){
                $contents = mb_convert_encoding($contents, $this->cx->var['encoding'], 'UTF-8');
            }

            echo $contents;
        } else {
            Kaden_Carp::carp('var.template is not set.');
        }
    }

    function extract_template($template, $cx = null){
        if( !isset($cx) ){
            extract($this->cx->res->param);
            $cx = $this->cx;
        } else {
            extract($cx->res->param);
        }
        ob_start();
        include( $template );
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents; 
    }
};

/* <?= h($hogehoge) ?> */
function h($str){
    return htmlspecialchars($str);
}

/* <?= line_break( h($text) ) ?> */
function line_break($str){
    return preg_replace($str, '/\r?\n/', '<br>');
}
