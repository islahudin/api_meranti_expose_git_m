<?php

/**
 * Database configuration
 */
## mode local DEV
// define('DB_DRIVER', 'mysql');
// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', '');
// define('DB_HOST', 'localhost');
// // define('DB_NAME', 'db_slim3');
// define('DB_NAME', 'db_meranti_expose');

// ## mode release PRODUCTION
// define('DB_DRIVER', 'mysql');
// define('DB_USERNAME', 'n1577716_ProMetago');
// define('DB_PASSWORD', 'LigaMan2023#');
// define('DB_HOST', 'qordinate.com'); //45.130.231.249 //217.21.72.73
// define('DB_NAME', 'n1577716_db_meranti_expose');

## mode release PRODUCTION
define('DB_DRIVER', 'mysql');
define('DB_USERNAME', 'sql_topapi_qordi');
define('DB_PASSWORD', '4baf33726e51a');
define('DB_HOST', '103.127.138.42'); //45.130.231.249 //217.21.72.73
define('DB_NAME', 'sql_topapi_qordi');

// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', '');
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'db_meranti_expose');

// define('BASE_URL', 'https://apismartererp.ayrtonware.com/');
define('BASE_URL', "https://" . $_SERVER['SERVER_NAME'] . "/");
// define('BASE_URL', "http://".$_SERVER['REMOTE_ADDR']."/");
define('ENDPOINT', 'api_meranti_expose/');
define('API_GMAPS', 'AIzaSyAXq4Ir5cXO5pVTSThC5gKi_BgSCoTp9og');
define('TOKEN_MAPBOX', 'pk.eyJ1IjoicW9yZGluYXRlLXRlY2hubyIsImEiOiJjbGlpdWozcncwMXJnM2VwYmVxMzBzc3hvIn0.QfE3x1awLDfaYLoNYjRdxg');

define('STATUS_200', 'SUCCESS');
define('STATUS_400', 'API_VALIDATION_ERROR_OR_PARAM_VALIDATION_ERROR');
define('STATUS_401', 'INVALID_API_KEY');
define('STATUS_403', 'REQUEST_FORBIDDEN_ERROR');
define('STATUS_404', 'DATA_NOT_FOUND');
define('STATUS_405', 'NOT_ALLOWED');
define('STATUS_409', 'CONFLICT_OR_DUPLICATE_ERROR');
define('STATUS_500', 'INTERNAL_SERVER_ERROR');

define('OTP', 1);
