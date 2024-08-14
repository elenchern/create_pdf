<?php
  require_once(__DIR__.'/_engine/__starter.php');
  $itemId=intval(key($_GET));

  function err($txt)
  {
  	  exit($txt);
  }

  if(!$itemId)
  {
    err('['.__LINE__.'] No item id');
  }

  // собираем всю необходимую инфу
  {
     // общая инфа об объекте
     $wpItem=array();
     {
        $sql="SELECT *
                FROM `wp_posts` AS `wp`
               WHERE `post_type` LIKE 'ychastki' AND
                     `post_status` LIKE 'publish' AND
                     `ID`=?";
        $vals=array($itemId);
        $result=$GLOBALS['DB']->query($sql, $vals);
      //   print_r($result); die();
        unset($sql, $vals);
        if(!$result['rows'])
        {
        	 err('['.__LINE__.'] Item not found');
        }

        $wpItem=$result['data'][0];
        unset($result);
     }

     // свойства объекта
     $wpItemMeta=array();
     {
        $sql="SELECT *
                FROM `wp_postmeta` AS `wpm`
               WHERE `post_id`=?";
        $vals=array($wpItem['ID']);
        $result=$GLOBALS['DB']->query($sql, $vals);
        unset($sql, $vals);

        foreach($result['data'] as $resKey=>$baseData)
        {
           $wpItemMeta[$baseData['meta_key']]=$baseData['meta_value'];
        } unset($result,$baseData,$query);
     }

     // главное фото
     $mainFoto='';
     if($wpItemMeta['img'])
     {
        $sql="SELECT *
                FROM `wp_postmeta` AS `wpm`
               WHERE `post_id`=? AND
                     `meta_key` LIKE '_wp_attached_file'";
        $vals=array($wpItemMeta['img']);
        $result=$GLOBALS['DB']->query($sql, $vals);
        unset($sql, $vals);
        if($result['rows'])
        {
           $mainFoto=$result['data'][0]['meta_value'];
        }
     }

     // корневой раздел
     $parent=array();
     {
        $sql="SELECT *
                FROM `wp_term_relationships` AS `wtr`
                JOIN `wp_term_taxonomy` AS `wtt` USING(`term_taxonomy_id`)
                JOIN `wp_terms` AS `wt` USING(`term_id`)
               WHERE `object_id`=?";
        $vals=array($wpItem['ID']);
        $result=$GLOBALS['DB']->query($sql, $vals);

        $parent=$result['data'][0];

        if($parent['parent'])
        {
           $sql="SELECT *
                   FROM `wp_terms` AS `wt`
                  WHERE `term_id`=?";
           $vals=array($parent['parent']);
           $result=$GLOBALS['DB']->query($sql, $vals);
           $parent=$result['data'][0];
        }

        if(!count($parent))
        {
           err('['.__LINE__.'] Parent not found');
        }



        unset($sql, $vals);

     }
  }

  if(0)
  {
     Exit
     ('
        $wpItem
        <pre>'.print_r($wpItem,true).'</pre>
        <hr>
        $wpItemMeta
        <pre>'.print_r($wpItemMeta,true).'</pre>
        <hr>
        $mainFoto
        <pre>'.$mainFoto.'</pre>
        <hr>
        $parent
        <pre>'.print_r($parent,true).'</pre>
     ');
  }

  // цвета для шрифта в PDF
  $colorSet=array
  (
     'gray'=>'#96989D',
     'grayLight'=>'#EDEDED',
     'black'=>'#000000',
     'orange'=>'#FF7802',
  );

  require(__DIR__.'/_engine/tcpdf_php8/tcpdf.php');

  $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);


		$pdf->setPrintHeader(false);$pdf->setPrintFooter(false);
		$pdf->SetMargins(7, 5, 7);$pdf->SetFooterMargin(5);
		$pdf->SetAutoPageBreak(false, 0);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		//$pdf->setLanguageArray($l);

		$pdf->SetDisplayMode('real','default');

  $pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(120,120,120);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetLineWidth(0.1);

		$pdf->addPage();

  $pdf->SetFont('montserrat','',12);

  $html='';
  // шапка
  {
     $html.='<table cellpadding="0" cellspacing="0" border="0">';
        $html.='<tr>';
           $html.='<td style="border-bottom:1px solid '.$colorSet['grayLight'].'">';


               $html.='<table cellpadding="0" cellspacing="0" border="0">';
                  $html.='<tr>';
                     $html.='<td width="290"><img src="pdf_img/logo.jpg" width="150" height="auto"  border="0"></td>';
                     $html.='<td width="20"></td>';
                     $html.='<td>';

                        $html.='<table cellpadding="0" cellspacing="3" border="0">';
                           $html.='<tr>';
                              $html.='<td style="width:130; text-align:left; color:'.$colorSet['gray'].'">Телефон</td>';
                              $html.='<td style="width:30"></td>';
                              $html.='<td style="width:90; text-align:left; color:'.$colorSet['gray'].'">Сайт</td>';
                              $html.='<td style="width:10"></td>';
                              $html.='<td style="width:110; text-align:left; color:'.$colorSet['gray'].'">E-mail</td>';
                           $html.='</tr>';

                           $html.='<tr>';
                              $html.='<td style="text-align:left; color:'.$colorSet['black'].'">+7 (4862) 630-111</td>';
                              $html.='<td></td>';
                              $html.='<td style="text-align:left; color:'.$colorSet['black'].'"><u>sts57.ru</u></td>';
                              $html.='<td></td>';
                              $html.='<td style="text-align:left; color:'.$colorSet['black'].'"><u>sales@sts57.ru</u></td>';
                           $html.='</tr>';
                        $html.='</table>';

                     $html.='</td>';
                  $html.='</tr>';
               $html.='</table>';

               $html.='<br />';

           $html.='</td>';
        $html.='</tr>';
     $html.='</table>';

     $html.='<br />';

     $pdf->writeHTML($html, true, 0, true, 0);
  }


  // заголовок и адрес
  {
     $html='';
     $html.='<table cellpadding="0" cellspacing="0" border="0">';
        $html.='<tr>';
           $html.='<td style="font-size:18; color:'.$colorSet['black'].'">'.$wpItem['post_title'].', '.$parent['name'].'</td>';
        $html.='</tr>';
     $html.='</table>';
     $html.='<table cellpadding="0" cellspacing="0" border="0">';
        $html.='<tr>';
           $html.='<td width="30"><img src="pdf_img/map.jpg" width="20" height="auto" border="0"></td>';
           $html.='<td style="font-size:14; color:'.$colorSet['gray'].'">'.$wpItemMeta['adress_kvartiry'].'</td>';
        $html.='</tr>';
     $html.='</table>';

     $html.='<br />';

     $pdf->writeHTML($html, true, 0, true, 0);

  }


  if($mainFoto)
  {
     $html='';
     $html.='<table cellpadding="0" cellspacing="0" border="0">';
        $html.='<tr>';
           $html.='<td style="text-align:center"><img src="'.CFG_SITE.'/wp-content/uploads/'.$mainFoto.'" width="400" height="auto" border="0"></td>';
        $html.='</tr>';
     $html.='</table>';

     $html.='<br />';

     $pdf->writeHTML($html, true, 0, true, 0);
  }


  // характеристики
  {
     $tblStart=$pdf->getY();

     $chars=array
     (
        array
        (
           'Этаж'=>$wpItemMeta['etash'],
           'Подъезд'=>$wpItemMeta['podezd'],
           'Номер квартиры'=>$wpItemMeta['nomer_kvartiry'],
           'Жилая площадь'=>(($t=$wpItemMeta['zhilaya_ploshhad'])?$t.' м²':''),
           'Общая площадь'=>(($t=$wpItemMeta['ploshat'])?$t.' м²':''),
           'Санузел'=>$wpItemMeta['sanuzel'],
           'Балкон'=>$wpItemMeta['balkon'],
           'Вид из окна'=>$wpItemMeta['vid_iz_okna'],
        ),

        array
        (
           'Жилая комната 1'=>(($t=$wpItemMeta['zhilaya_komnata_1'])?$t.' м²':''),
           'Жилая комната 2'=>(($t=$wpItemMeta['zhilaya_komnata_2'])?$t.' м²':''),
           'Жилая комната 3'=>(($t=$wpItemMeta['zhilaya_komnata_3'])?$t.' м²':''),
           'Кухня'=>$wpItemMeta['kuhnya'].' м²',
        )
     );

     // подложка с точками
     {
        $strPad=str_repeat(' .', 40);

        $html='';
        $html.='<table cellpadding="0" cellspacing="0" border="0">';
           $html.='<tr>';
              foreach($chars as $chKey=>$charsList)
              {
                 $html.='<td width="50%">';
                    $html.='<table cellpadding="0" cellspacing="0" border="0">';
                       foreach($charsList as $key=>$val)
                       {
                          if(!$val){continue;}
                          $html.='<tr>';
                             $html.='<td style="height:30; font-size:12; color:'.$colorSet['gray'].'">'.$strPad.'</td>';
                          $html.='</tr>';
                       }
                    $html.='</table>';
                 $html.='</td>';
              }
           $html.='</tr>';
        $html.='</table>';

        $pdf->writeHTML($html, true, 0, true, 0);
     }


     // данные по верх полоожки
     {
        $pdf->setY(($tblStart-0.5));

        $html='';
        $html.='<table cellpadding="0" cellspacing="0" border="0">';
           $html.='<tr>';
              foreach($chars as $chKey=>$charsList)
              {
                 $html.='<td width="50%">';
                    $html.='<table cellpadding="0" cellspacing="0" border="0">';
                       foreach($charsList as $key=>$val)
                       {
                          if(!$val){continue;}
                          $html.='<tr>';
                             $html.='<td style="width:160; height:30; color:'.$colorSet['gray'].'"><span style="background-color:#FFFFFF">'.$key.'&nbsp;</span></td>';
                             $html.='<td style="width:130; height:30; text-align:right; color:'.$colorSet['black'].'"><span style="background-color:#FFFFFF">&nbsp;'.$val.'</span></td>';
                          $html.='</tr>';
                       }
                    $html.='</table>';

                    if($chKey==1)
                    {
                       $html.='<br /><br />';
                       $html.='<table cellpadding="0" cellspacing="0" border="0">';
                          $html.='<tr>';
                             $html.='<td style="color:'.$colorSet['gray'].'">Стоимость квартиры</td>';
                          $html.='</tr>';
                          $html.='<tr>';
                             $html.='<td style="color:'.$colorSet['orange'].'; font-size:20">'.SOW::digit($wpItemMeta['price'],0,'.',' ').'</td>';
                          $html.='</tr>';
                       $html.='</table>';
                    }

                 $html.='</td>';
              }
           $html.='</tr>';
        $html.='</table>';

        $html.='<br />';

        $pdf->writeHTML($html, true, 0, true, 0);
     }
  }


  // подвал
  {
     $footerStart=$pdf->getY();


     $html='';
     $html.='<table cellpadding="0" cellspacing="0" border="0">';
        $html.='<tr>';
           $html.='<td style="border-top:1px solid '.$colorSet['grayLight'].'">';

               $html.='<br /><br />';

               $html.='<table cellpadding="0" cellspacing="0" border="0">';
                  $html.='<tr>';
                     $html.='<td width="110" style="color:'.$colorSet['gray'].'; font-size:8; text-align:center;">Квартира<br />на сайте</td>';
                     $html.='<td width="60"></td>';
                     $html.='<td>';

                        $html.='<table cellpadding="0" cellspacing="3" border="0">';
                           $html.='<tr>';
                              $html.='<td style="width:250; text-align:left; color:'.$colorSet['gray'].'">Офис продаж:</td>';
                              $html.='<td style="width:10"></td>';
                              $html.='<td style="width:150; text-align:left; color:'.$colorSet['gray'].'">График работы:</td>';
                           $html.='</tr>';

                           $html.='<tr>';
                              $html.='<td style="text-align:left; color:'.$colorSet['black'].'">г. Орел, ул. Пушкина, д. 54</td>';
                              $html.='<td></td>';
                              $html.='<td style="text-align:left; color:'.$colorSet['black'].'">';

                                 $html.='Пн-Пт: 09:00-18:30<br />';
                                 $html.='Сб: 10:00-15:00<br />';
                                 $html.='<span style="color:'.$colorSet['orange'].'">Вс: Выходной</span>';
                              $html.='</td>';
                           $html.='</tr>';
                        $html.='</table>';

                     $html.='</td>';
                  $html.='</tr>';
               $html.='</table>';


           $html.='</td>';
        $html.='</tr>';
     $html.='</table>';

     $html.='<br />';

     $pdf->writeHTML($html, true, 0, true, 0);
  }


  $pdf->write2DBarcode(CFG_SITE.'/project/p/'.$itemId.'/', 'QRCODE,L', 7, ($footerStart+15), 30, 30, $style=array(), 'N');



  $expFileName='item'.$itemId;
		$pdf->Output($expFileName,"I");
  exit;
?>