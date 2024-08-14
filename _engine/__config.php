<?php
define( 'IS_LOCAL', ($_SERVER['REMOTE_ADDR']=='127.0.0.1'?1:0) ); // 'dev.'.
define('CFG_ERRORS',1); // показывать ли ошибки PHP

define('CFG_SITE','https://1xn.ru');

if(IS_LOCAL)
{
  define('CFG_DB_HOST','localhost');
  define('CFG_DB_PORT','3306');

  define('CFG_DB_BASE','sts');
  define('CFG_DB_LOGIN','root');
  define('CFG_DB_PASS','123456');

  define('CFG_HTTPS',0);
}
else
{
  // данные для подключения к БД

  define('CFG_DB_HOST','localhost');
  define('CFG_DB_PORT','3306');

  define('CFG_DB_BASE','sts57');
  define('CFG_DB_LOGIN','sts57');
  define('CFG_DB_PASS','kS1lC1cE7m');

  define('CFG_HTTPS',1);
}
?>