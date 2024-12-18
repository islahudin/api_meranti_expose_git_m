<?php
require __DIR__ . '/Config.php';

$database_config = [

  "driver" => "mysql",
  "host" => DB_HOST,
  "database" => DB_NAME,
  "username" => DB_USERNAME,
  "password" => DB_PASSWORD,
  "charset" => "utf8",
  "collation" => "utf8_unicode_ci",
  "prefix" => ""

];

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($database_config);
$capsule->setAsGlobal();
$capsule->bootEloquent();

return $capsule;
