<?php
;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once('spyc.php');
require_once('fillinform.php');

require_once('li-carp.php');
require_once('li-core.php');
require_once('li-dispatcher.php');
require_once('li-validator.php');
require_once('li-param.php');
require_once('li-request.php');
require_once('li-response.php');
require_once('li-cookie.php');
require_once('li-session.php');
require_once('li-db.php');
require_once('li-db-model.php');
require_once('li-db-sql.php');
require_once('li-view-php.php');
require_once('li-view-json.php');
require_once('li-url.php');
require_once('li-controller-db.php');

mb_language("ja");
mb_internal_encoding("UTF-8");

date_default_timezone_set('Asia/Tokyo');
define('DATE_DATETIME', 'Y-m-d H:i:s');
define('DATE_DATE',     'Y-m-d');
define('DATE_TIME',     'H:i:s');

