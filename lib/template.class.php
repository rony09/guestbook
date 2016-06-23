<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 20:03:24 GMT
 * ----------------------------------------------
 */

class gb_template
{

   var $template = array();
   var $root_dir;
   var $LANG;
   var $plain_html = array();

   function __CONSTRUCT($path = '') 
   {
      if ($path != '' && is_dir($path)) 
      {
     	   $this->root_dir = $path;
      } 
      else 
      {
        	$this->root_dir = dirname(dirname(__FILE__));
    	}
   }

   function set_rootdir($tpl_dir)
   {
      if (!is_dir($tpl_dir))
      {
         return false;
      }
      $this->root_dir = $tpl_dir;
      return true;
   }

   function set_lang($language = '')
   {
      $illegalChars = array('?' => '',"\\" => '',':'  => '','*' => '','"' => '','<' => '','>' => '','|' => '','../' => '','./' => '',"\n" => '',"\r" => '',"\t" => '');
      $language = trim(strtr($language, $illegalChars));
      if (!empty($language) && file_exists($this->root_dir.'/lang/'.$language.'.php'))
      {
         $this->language = $language;
      }
      else
      {
         $this->language = 'english';
      }
      return $this->language;
   }

   function get_content()
   {
      if (!isset($this->LANG))
      {
         include $this->root_dir.'/lang/english.php';
         include $this->root_dir.'/lang/'.$this->language.'.php';
         $this->LANG =& $LANG;
         $this->TIMES =& $times;
         $this->WEEKDAY =& $weekday;
         $this->MONTHS =& $months;
      }
      return $this->LANG;
   }

   function get_template($tpl, $show_footer = true)
   {
      if (!isset($this->template[$tpl]))
      {
         $filename = $this->root_dir.'/templates/classic/'.$tpl.'.tpl';
         if ((IS_MODULE || IS_INCLUDE) && (($tpl == 'header') || ($tpl == 'footer') || ($tpl == 'success_header')))
         {
            $this->template[$tpl] = '';
         }
         elseif (file_exists($filename))
         {
            $this->template[$tpl] = file_get_contents($filename);
            $this->template[$tpl] = str_replace('"', '\"', $this->template[$tpl]);
         }
         else
         {
            die("$filename does not exists");
         }
         if (((IS_MODULE) || (IS_INCLUDE) || (isset($included))) && (!EXTERNAL_CSS))
         { 
            $styles = array(
            'class=\"font1\"' => 'style=\"font-family:$VARS[font_face];font-size:$VARS[tb_font_1];color:$VARS[text_color];\"',
            'class=\"font2\"' => 'style=\"font-family:$VARS[font_face];font-size:$VARS[tb_font_2];color:$VARS[text_color];\"',
            'class=\"font3\"' => 'style=\"font-family:Arial,Helvetica,sans-serif;font-size:7.5pt;color:$VARS[text_color];font-weight: bold;\"',
            'class=\"input\"' => 'style=\"font-family:$VARS[font_face];font-size:9pt\"',
            'class=\"select\"' => 'style=\"font-family:$VARS[font_face];font-size:9pt\"',
            'class=\"gbsearch\"' => 'style=\"font-family:$VARS[font_face];font-size:$VARS[tb_font_2];color:$VARS[search_font_color];background:$VARS[search_bg_color];\"',
            'class=\"lazTop\"' => 'style=\"background: $VARS[pbgcolor]; font-family: $VARS[font_face]; font-size: $VARS[tb_font_2]; color: $VARS[laz_top_font_color]; text-align: left;\"',
            'class=\"lazTopNum\"' => 'style=\"color: $VARS[laz_top_num_color]; font-weight:bold;\"'
            );
            $this->template[$tpl] = strtr($this->template[$tpl], $styles);
         }
         if (($tpl == 'footer') && ($show_footer))
         {
            $this->template['footer'] = $this->get_template('laz_footer')."\n".$this->template['footer'];
         }
      }
      return $this->template[$tpl];
   }

}

?>