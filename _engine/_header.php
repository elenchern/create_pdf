<?php
if(CFG_ERRORS)
{
   error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
   ini_set('display_errors', 1);
}
else
{	  error_reporting(0);}
header("Content-type: text/html; charset=utf-8");
mb_internal_encoding("UTF-8");
?>