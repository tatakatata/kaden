<?php

require_once('lib/lime.php');
require_once('lib/kaden-validator-functions.php');

$t = new lime_test(1 + 6 + 2 + 2 + 7 + 6 + 5
                   , new lime_output_color());

$t->ok( $kf = new Kaden_Validator_Functions );

/* length */
$t->ok( $kf->length('Textあ', 1, 5),
        '2 arguments length(): valid' );
$t->ok( $kf->length('Textあ', 1, 3) === false,
        '2 arguments length(): invalid - too large' );
$t->ok( $kf->length('Textあ', 7, 10) === false,
        '2 arguments length(): invalid - too small' );

$t->ok( $kf->length('Textあ', 5),
        '1 arguments length(): valid' );
$t->ok( $kf->length('Textあ', 3) === false,
        '1 arguments length(): invalid - too large' );

$t->ok( $kf->message('length'), 'message()' );

/* maxlen */
$t->ok( $kf->maxlen('日本語', 5), 'maxlen(): valid' );
$t->ok( $kf->maxlen('日本語です', 3) === false,
        'maxlen(): invalid' );

/* minlen */
$t->ok( $kf->minlen('日本語', 2), 'minlen(): valid' );
$t->ok( $kf->minlen('日本語です', 10) === false,
        'minlen(): invalid' );

/* ascii */
$t->ok( $kf->ascii('Alphabet'), 'ascii(): valid - alphabet' );
$t->ok( $kf->ascii('Text including space'), 'ascii(): valid - space' );
$t->ok( $kf->ascii('!"#$%&\'()-=^~\\|'),  'ascii(): valid - !"#$%&\'()-=^~\\|' );
$t->ok( $kf->ascii('@`[{;+:*]},<.>/?_'),  'ascii(): valid - @`[{;+:*]},<.>/?_' );

$t->ok( $kf->ascii('Ａｌｐｈａｂｅｔ') === false,
        'ascii(): invalid - zenkaku alphabet' );
$t->ok( $kf->ascii('！”＃＄＆’（）') === false,
        'ascii(): invalid - ！”＃＄％＆（' );
$t->ok( $kf->ascii('日本語') === false,
        'ascii(): invalid - 日本語' );

/* number */
$t->ok( $kf->number(123), 'number(): valid' );
$t->ok( $kf->number("12342534235789374598374598374"), 'number(): valid' );
$t->ok( $kf->number(-445123) === false,
        'number(): invalid - minus' );
$t->ok( $kf->number(0.445123) === false,
        'number(): invalid - float' );
$t->ok( $kf->number('AAA') === false,
        'number(): invalid' );
$t->ok( $kf->number('１２３４５６') === false,
        'number(): invalid - zenkaku number' );

/* numeric */
$t->ok( $kf->numeric(123), 'numeric(): valid' );
$t->ok( $kf->numeric("12342534235789374598374598374"), 'numeric(): valid' );
$t->ok( $kf->numeric(-445123), 'numeric(): valid - minus' );
$t->ok( $kf->numeric(0.445123), 'numeric(): valid - float' );
$t->ok( $kf->numeric('AAA') === false,
        'numeric(): invalid' );

