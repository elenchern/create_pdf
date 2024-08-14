<?php
class SOW // str one view
{
    public static function size($bytes, $binaryPrefix=false)
    {
        if ($binaryPrefix)
        {
            $unit=array('B','KiB','MiB','GiB','TiB','PiB');
            if ($bytes==0) return '0 ' . $unit[0];
            return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
        }
        else
        {
            $unit=array('B','KB','MB','GB','TB','PB');
            if ($bytes==0) return '0 ' . $unit[0];
            return @round($bytes/pow(1000,($i=floor(log($bytes,1000)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
        }
    }

    // проверяет поле названия (ФИО, город, приводит к виду Xxx)
    public static function name($name)
    {
        $name=(string)$name;
        $name = preg_replace("/[^\p{L}\.\- ]+/u", "", $name);
        $name = preg_replace('/\s*-\s*/', '-', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name=trim($name);

        $name = mb_strtolower($name, "UTF-8");
        $name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");

        return $name;
    }

    // числа  к формату $prec число знаков дробно части, $d используемый делитель вывода (./,), $format - отбивка тысяч
    public static function digit($digit, $prec=2, $d='.', $format='', $cropZero=0)
    {
       $digit=(string)$digit;

       $m=( mb_substr($digit ,0,1 )=='-'?'-':'' );
       $digit=preg_replace('/[^0-9\.\,]/ius','',$digit);
       $digit=str_replace(',','.',$digit);

       if(!$prec and $prec!==0)
       {          $t=explode('.', $digit);
          $prec=intval(mb_strlen($t[1]));       }

       $digit=$m.number_format((float)$digit, $prec, $d, $format);

       if($cropZero and $prec>0)
       {          $digit=rtrim($digit,'0');       }  //exit($digit);

       if($d!=='.')
       {       	  $digit=str_replace('.',$d, $digit);       }

       return $digit;
    }

    public static function digitShoter($n)
 			{
 			    if($n>-1000 and $n<1000){return $n;}

        $nInp=$n;
 			    $n = (float)$n;

 			    if ($n <= 9999 and $n >= -9999)  {
 			        $format = number_format($n, 0, '.', '');
 			    } else if($n <= 999999 and $n >= -999999){
 			        $format = number_format($n/1e3, 2, '.', '') + 0 .'К';
 			    } else if($n <= 999999999 and $n >= -999999999){
 			        $format = number_format($n/1e6, 2, '.', '') + 0 .'М';
 			    } else {
 			        $format = number_format($n/1e9, 2, '.', '') + 0 .'Г';
 			    }
 			    return '<dgt title="'.$nInp.'">'.$format.'<dgt>';
 			}

 			public static function html($text)
 			{
 			    $allowedTags = '<b><a><i><br><font>';
        $strippedText = strip_tags($text, $allowedTags);

        $safeText = htmlspecialchars($strippedText, ENT_QUOTES, 'UTF-8');

        // Восстанавливаем разрешенные теги обратно
        $safeText = str_replace(array('&lt;', '&gt;', '&quot;', '&amp;'), array('<', '>', '"', '&'), $safeText);

        return $safeText;
 			}

 			public static function userDir($userId)
 			{       return mb_substr(md5('-:'.$userId.':-'),0,15).$userId; 			}


 			public static function wpf($num, $titles)
    {
       $cases = array (2, 0, 1, 1, 1, 2);
       return $titles[ ($num%100>4 && $num%100<20)? 2 : $cases[min($num%10, 5)] ];
    }

    public static function inp($str)
 			{
 			   $str=str_replace('"','&quot;',$str);
 			   $str=str_replace("'",'&#039;',$str);
       return $str;
 			}

 			public static function utf8($str)
 			{
 			   $str = iconv('', 'UTF-8//IGNORE', $str);
	      return trim($str);
 			}
}
?>