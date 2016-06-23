<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 19:59:38 GMT
 * ----------------------------------------------
 */

class guestbook_vars extends gbook_sql
{

  var $VARS;
  var $LANG;
  var $table = array();
  var $SMILIES;
  var $template;
  var $badwords = array();
  var $logArray = array();

  function __CONSTRUCT($path = '')
  {
    global $GB_TBL;
    $this->table =& $GB_TBL;
    parent::__CONSTRUCT();
    $this->connect();
    $this->template = new gb_template($path);
  }
  
// Retrieve our settings from the database  

  function getVars()
  {
    global $_COOKIE, $laz_build;
    $this->VARS = $this->fetch_array($this->query('SELECT * FROM '.LAZ_TABLE_PREFIX.'_config'));
    $this->free_result($this->result);
    date_default_timezone_set($this->VARS['offset']);
    if (isset($_COOKIE['lang']) && !empty($_COOKIE['lang']))
    {
      $this->template->set_lang($_COOKIE['lang']);
    }
    else
    {
      $this->template->set_lang($this->VARS['lang']);
    }
    $this->LANG = $this->template->get_content();
    if ($this->VARS['laz_version'] != LAZBUILD)
    {
      die ("<div style=\"border: 2px solid #D00; width: 400px; height: 80px; font-size: 12px; color: #000; padding: 2px;\"><h3 style=\"font-size: 20px; margin: 0 0 5px 0; padding: 0;\">You need to update your database!</h3>Visit <a href=\"".$this->VARS['base_url']."/install.php\">install.php</a> and do a smart update.<br />Database Version: ".$this->VARS['laz_version']."<br />Lazarus Version: ".LAZBUILD."</div>");
    }
    define('EXTERNAL_CSS', $this->VARS['external_css']);
    return $this->VARS;
  }

// This function converts a string into the relevant ascii HTML entities.

  function html_encode($string)
  {
    $ret_string = '';
    $len = strlen( $string );
    for ($x = 0; $x < $len; $x++) 
    {
      $enctype = rand(1, 3);
      switch ($enctype) 
      {
        case 1:
          $ret_string .= '&#'.ord($string[$x]).';';
          break;
        case 2:
          $ret_string .= '&#x'.dechex(ord($string[$x])).';';
          break;
        case 3:
          $ret_string .= $string[$x];
          break;
        default:
          $ret_string .= $string[$x];
          break;
      }
    } 
    return $ret_string;
  } 
  
// If they are previewing their entry we need to undo the HTMLspecialchars function

  function undo_htmlspecialchars($string)
  {
    $html = array (
      '&amp;'  => '&',
      '&quot;' => '"',
      '&lt;'   => '<',
      '&gt;'   => '>'
    );
    $string = strtr($string, $html);
    return ($string);
  } 
  
// Lets turn our smiley codes into img tags

  function emotion($message)
  {
    global $GB_PG;
    if (!isset($this->SMILIES))
    {
      $this->query('SELECT * FROM '.LAZ_TABLE_PREFIX.'_smilies');
      while ($this->fetch_array($this->result))
      {
        $this->SMILIES[$this->record['s_code']] = '<img src="'.$GB_PG['base_url'].'/img/smilies/'.$this->record['s_filename'].'" width="'.$this->record['width'].'" height="'.$this->record['height'].'" alt="'.$this->record['s_emotion'].'" title="'.$this->record['s_emotion'].'" align="bottom" />';
      }
    }
    if (isset($this->SMILIES))
    {
      for (reset($this->SMILIES); $key = key($this->SMILIES); next($this->SMILIES))
      {
        $message = str_replace("$key",$this->SMILIES[$key],$message);
      }
    }
    return $message;
  }
  
//
// Generate the smileys box content to be used on the forms.
//    
    
   function generate_smilies()
   {
      global $GB_PG;
      $ourSmileys = '';
      $smileyq = $this->query('SELECT * FROM '.LAZ_TABLE_PREFIX.'_smilies ORDER BY id ASC');
      while ($thesmiley = $this->fetch_array($smileyq))
      {
         $ourSmileys .= " <a href=\"javascript:emoticon('".$thesmiley['s_code']."')\"><img src=\"".$GB_PG['base_url']."/img/smilies/".$thesmiley['s_filename']."\" alt=\"".$thesmiley['s_code']."\" title=\"".$thesmiley['s_emotion']."\" border=\"0\" style=\"margin: 3px;\" /></a> ";
      }
      return $ourSmileys;
   }  
  
// Lets turn our timestamp into a legible date  

  function DateFormat($timestamp, $gb_time = 0)
  {
    //$timestamp += $this->VARS['offset']*3600;
    list($wday,$mday,$month,$year,$hour,$minutes,$hour12,$ampm) = explode(' ', date("w j n Y H i h A", $timestamp));
    if ($this->VARS['tformat'] == 'AMPM')
    {
      $newTime = ' ' . $hour12 . ':' . $minutes . ' ' . $ampm;
    }
    else
    {
      $newTime = ' ' . $hour . ':' . $minutes;
    }
    if ($this->VARS['dformat'] == 'ISO')
    {
      $newDate = " $year-$month-$mday";
    }
    elseif ($this->VARS['dformat'] == 'USx')
    {
      $newDate = " $month-$mday-$year";
    }
    elseif ($this->VARS['dformat'] == 'US')
    {
      $newDate = $this->template->WEEKDAY[$wday].", ".$this->template->MONTHS[$month-1]." $mday, $year";
    }
    elseif ($this->VARS['dformat'] == 'Euro')
    {
      $newDate = $this->template->WEEKDAY[$wday].", $mday ".$this->template->MONTHS[$month-1]." $year";
    }
    else
    {
      $newDate = "$mday.$month.$year";
    }
    // Check if they want to use Smart time and the date is within the smart time parameters
    // How to do our Today, Yesterday or Date
    if ($this->VARS['smarttime'] == 1 && $gb_time == 0)
    {
      $timeNow = time() + ($this->VARS['offset']*3600);
      $serverTime = time();
      $dateToday = date('j/n/Y', $timeNow);
      if (($dateToday == $mday.'/'.$month.'/'.$year))
      {
        $howLong = $serverTime - $timestamp;
        if ($howLong < 60)
        {
          return str_replace('%T', $howLong, '<span title="' . $newDate . $newTime . '">' . $this->template->TIMES['seconds'] . '</span>'); // If posted within the minute
        }
        elseif ($howLong < 3600)
        {
          return str_replace('%T', intval($howLong / 60), '<span title="' . $newDate . $newTime . '">' . $this->template->TIMES['minutes'] . '</span>');  // If posted within the hour
        }
        else
        {
          return str_replace('%T', $newTime, '<span title="' . $newDate . $newTime . '">' . $this->template->TIMES['today'] . '</span>'); // If posted Today
        }
      }
      elseif (($dateToday == ($mday + 1).'/'.$month.'/'.$year))
      {
        return str_replace('%T', $newTime, '<span title="' . $newDate . $newTime . '">' . $this->template->TIMES['yesterday'] . '</span>'); // If posted Yesterday
      }
      if ($timeNow <= ($timestamp + 518400))
      {
        return '<span title="' . $newDate . $newTime . '">' . $this->template->WEEKDAY[$wday] . ' ' . $newTime . ' </span>'; // If posted within the last 7 days
      }
    }    
    return $newDate . $newTime;
  }
  
// Convert any AGcode in the entry into the correct HTML  
  
  function AGCode($string)
  {
    $string = ' '.$string;
    if ($this->VARS['allow_urlagcode'] == 1)
    {
      // This bit will automatically put [url] and [/url] around any URLs missing them
      $string = preg_replace("#(^|[\n ])((http|https|ftp)+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#uis", "$1[url]$2[/url]", $string);
      $string = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#uis", "$1[url]$2[/url]", $string); 
    }
    // No point doing these checks if there is no AGcode
    if (!(strpos($string, '[') && strpos($string, ']')))
    {
      $string = substr($string, 1);
      return $string;
    }
    if ($this->VARS['allow_flashagcode'] == 1)
    {
      while (preg_match('!\[flash\]((http|https|ftp)://[^[]+)\[/flash\]!i', $string, $flash_url))
      {
        if (isset($flash_url[1]))
        {
          $isYouTube = false;
          if (preg_match('!https?://[a-z]*\.*youtube.com/(.+?)v=([a-z0-9_-]+)!i', $flash_url[1]))
          {
            $isYouTube = true;
            $flashURL = preg_replace('!https?://[a-z]*\.*youtube.com/(.+?)v=([a-z0-9_-]+)(.*)!i', "https://www.youtube.com/v/$2", $flash_url[1]);
          }
          elseif (preg_match('!https?://[a-z]*\.*youtu.be/([a-z0-9_-]+)!i', $flash_url[1]))
          {
            $isYouTube = true;
            $flashURL = preg_replace('!https?://[a-z]*\.*youtu.be/([a-z0-9_-]+)!i', "https://www.youtube.com/v/$1", $flash_url[1]);
          }
          else
          {
            $flashURL = urlencode($flash_url[1]);
          }
          if ($isYouTube)
          {        
            $imgsize = $this->check_image_size(640, 480);
            $string = preg_replace('!\[flash\]'.preg_quote($flash_url[1]).'\[/flash\]!i',"<object type=\"application/x-shockwave-flash\" data=\"".$flashURL."\" $imgsize[2]><param name=\"movie\" value=\"".$flashURL."\"><param name=\"wmode\" value=\"opaque\"><p>You need <a href=\"http://www.adobe.com/go/getflashplayer\" target=\"_blank\">Flash</a> to view this object</p></object>",$string);
          }
          if (!$isYouTube)
          {
            $imagesize = getimagesize($flashURL);
            if (is_array($imagesize)) 
            {              
              if ($imagesize[2] == 4 || $imagesize[2] == 13)
              {
                $imgsize = $this->check_image_size($imagesize[0], $imagesize[1]);
                $string = preg_replace('!\[flash\]'.preg_quote($flashURL).'\[/flash\]!i',"<object type=\"application/x-shockwave-flash\" data=\"".$flashURL."\" $imgsize[2]><param name=\"movie\" value=\"".$flashURL."\"><param name=\"wmode\" value=\"opaque\"><p>You need <a href=\"http://www.adobe.com/go/getflashplayer\" target=\"_blank\">Flash</a> to view this object</p></object>",$string);
              }
              else
              {
                $string = preg_replace('!\[flash\]'.preg_quote($flashURL).'\[/flash\]!i',"[img]".$flashURL."[/img]",$string);  
              }
            }
            else
            {
              $string = preg_replace('!\[flash\]'.preg_quote($flashURL).'\[/flash\]!i',"[url]".$flashURL."[/url]",$string);
            }
          }
        }
      }
    }
    if ($this->VARS['allow_imgagcode'] == 1)
    {
      while (preg_match('!\[img\]((http|https|ftp)://[^[]+)\[/img\]!i', $string, $img_url))
      {
        if (isset($img_url[1]) && ($this->VARS['agcode_img_width'] || $this->VARS['agcode_img_height']))
        {
          $imagesize = getimagesize($img_url[1]);
          if (is_array($imagesize))
          {
            $imgsize = $this->check_image_size($imagesize[0], $imagesize[1]);
            $string = preg_replace('!\[img\]'.preg_quote($img_url[1]).'\[/img\]!i',"<a href=\"".$img_url[1]."\" target=\"_blank\"><img src=\"".$img_url[1]."\" border=\"0\" alt=\"\" $imgsize[2] /></a>",$string);
          }
          else
          {
            $string = preg_replace('!\[img\]'.preg_quote($img_url[1]).'\[/img\]!i',"<a href=\"".$img_url[1]."\" target=\"_blank\"><img src=\"".$img_url[1]."\" border=\"0\" alt=\"\" /></a>",$string);
          }
        }
        else
        {
          $string = preg_replace('!\[img\]'.preg_quote($img_url[1]).'\[/img\]!i',"<a href=\"".$img_url[1]."\" target=\"_blank\"><img src=\"".$img_url[1]."\" border=\"0\" alt=\"\" /></a>",$string);
        }
      }
    }
    $agcode_search = array(
      '/\[b\](.+?)\[\/b\]/is',                                
      '/\[i\](.+?)\[\/i\]/is',                                
      '/\[u\](.+?)\[\/u\]/is'                       
    );
    
    $agcode_replace = array(
      '<strong>$1</strong>',
      '<em>$1</em>',
      '<u>$1</u>'
    );
                
    if ($this->VARS['allow_emailagcode'] == 1)
    {
      $agcode_search[]  = '/\[email\]([a-z0-9\-_\.\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+?)\[\/email\]/ies';
      $agcode_replace[] = "'<a href=\"'.\$this->html_encode('mailto:$1').'\">'.\$this->html_encode('$1').'</a>'";
    }
    if ($this->VARS['allow_urlagcode'] == 1)
    {
      $string = preg_replace("/\\[url\\]www\\./i", "[url]http://www.", $string);
      $agcode_search[]  = "/\[url\]((http|https|ftp):\/\/([\w\.+]*)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~]*?)\[\/url\]/uis";
      $agcode_replace[] = '[<a href="$1" target="_blank" rel="nofollow" title="$1">$3</a>]';
      $agcode_search[]  = "/\[url=((http|https|ftp)+?:\/\/[\w\#$%&~\/.\-;:=,?@\[\]+]*)\](.+?)\[\/url\]/uis";
      $agcode_replace[] = '<a href="$1" target="_blank" title="$1" rel="nofollow">$3</a>';
    }    
    $string = substr($string, 1);
    $string = preg_replace ($agcode_search, $agcode_replace, $string);
    return $string;
  }
  
// Generate a mildly encrypted timestamp

  function generate_timehash($timehash = '') 
  {
    global $GB_DB;
    $hashkey = md5($GB_DB['dbName'].$GB_DB['user']);
    if ($timehash != '')
    {
      return (($timehash / ord($hashkey[7])) + ord($hashkey[13])) - ord($hashkey[24]);
    }
    else
    {
      return (((time() + ord($hashkey[24])) - ord($hashkey[13])) * ord($hashkey[7]));
    }
  }  

// Code to allow certain HTML tags
   
  function allowed_html($string)
  {
    $allowed_tags = explode(',', $this->VARS['allowed_tags']);
    for ($i = 0; $i <= (count($allowed_tags) - 1); $i++)
    {
      $allowed_tags[$i] = trim($allowed_tags[$i]);
      $string = preg_replace("!&lt;$allowed_tags[$i]&gt;([^\\[]*?)&lt;/$allowed_tags[$i]&gt;!","<$allowed_tags[$i]>$1</$allowed_tags[$i]>", $string);
    }
    return $string;
  }

// Remove spaces from the ends of the strings and any double spaces from the middle

  function FormatString($strg)
  {
    $strg = trim($strg);
    $strg = preg_replace('/[ ]+/', ' ', $strg);
    return $strg;
  }

// Make sure there are no stupidly long or short words

  function CheckWordLength($strg)
  {
    $word_array = preg_split("/[ |\n]/",$strg);
    for ($i=0;$i<sizeof($word_array);$i++)
    {
      if ((preg_match("/^\\[(url|img|email|flash)\\].+\\]/i",$word_array[$i])) || (preg_match("/^(http:\/\/)|(https:\/\/)|(ftp:\/\/)|(www\\.)/i", $word_array[$i])))
      {
        if (strlen($word_array[$i]) > 150)
        {
          return false;
        }
      }
      elseif (strlen($word_array[$i]) > $this->VARS['max_word_len'])
      {
        return false;
      }
    }
    return true;
  }

// Have we banned the ip they are posting from?

  function isBannedIp($ip, $bannedIPs = 0, $sfs = 0)
  {
    $olderThan24 = time() - 86400; // Timestamp for 24 hours ago
    $this->query('DELETE FROM '.LAZ_TABLE_PREFIX.'_ban WHERE (timestamp<' . $olderThan24 . ' AND timestamp>0)'); // Delete old SFS IP block
    $this->query('SELECT * FROM '.LAZ_TABLE_PREFIX.'_ban');
    if (!$this->result)
    {
      return 0;
    }
    while ($row = $this->fetch_array($this->result))
    {
      if ((strpos($row['ban_ip'], '-') !== false) && ($bannedIPs == 1))  // IP check using range
      {
        list($startIP, $endIP) = explode('-', $row['ban_ip']);
        $startIP = sprintf('%u', ip2long($startIP));
        $endIP = sprintf('%u', ip2long($endIP));
        $userIP = sprintf('%u', ip2long($ip));
        if (($userIP >= $startIP) && ($userIP <= $endIP))
        {
          return 1;
        }
      }
      elseif ((strpos($row['ban_ip'], '/') !== false) && ($bannedIPs == 1)) // IP check using CIDR
      {
        list ($startIP, $rPrefix) = explode('/', $row['ban_ip']);
        $rPrefix = (~0) << (32 - $rPrefix);
        $startIP = ip2long($startIP) & $rPrefix;
        $userIP = ip2long($ip) & $rPrefix;
        if ($userIP == $startIP)
        {
          return 1;
        }
      }
      elseif ((strpos($ip, $row['ban_ip'])) === 0)  // normal IP check
      {
        if ((!empty($bannedIPs)) && empty($row['timestamp']))
        {
          return 1;
        }
        elseif (!empty($sfs) && !empty($row['timestamp']))
        {
          return 2;
        }
      }
    }
    return 0;
  }
  
// Lets ask Stop Forum Spam if they think they IP is a spammer or not

  function SFSCheck($ip, $email='')
  {
    ini_set('default_socket_timeout',15);
    $email = (!empty($email)) ? '&email=' . urlencode(iconv($this->VARS['charset'], 'UTF-8', $email)) : '';
    $SFSurl = 'http://api.stopforumspam.org';
    $SFSget = '/api?ip=' . $ip . $email . '&f=serial';
    if (ini_get('allow_url_fopen') == 1)
    {
      $SFSdata = @file_get_contents($SFSurl . $SFSget);
    }
    else
    {
      $fp = @fsockopen("api.stopforumspam.org", 80);
      if($fp)
      {
        $out  = "GET " . $SFSget . " HTTP/1.0\r\n";
        $out .= "Host: api.stopforumspam.org\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        while (!feof($fp))
        {
          $foo = fgets($fp);
          if(strpos($foo, 'a:') === 0)
          {
            $SFSdata = $foo;
          }
        }
      }
      else
      {
        $SFSdata = false;
      }
    }
    if ($SFSdata === false)
    {
      return 3;
    }
    else
    {
      $SFSdata = @unserialize($SFSdata);
      if ($SFSdata !== false)
      {
        if (isset($SFSdata['ip']['confidence']))
        {
          if ($SFSdata['ip']['confidence'] >= $this->VARS['sfs_confidence'])
          {
            $this->query("INSERT INTO " . LAZ_TABLE_PREFIX . "_ban (ban_ip, timestamp) VALUES('$ip', " . time() . ")");
            return 1;
          }
        }
        if (isset($SFSdata['email']['confidence']))
        {
          if ($SFSdata['email']['confidence'] >= $this->VARS['sfs_confidence'])
          {
            return 2;
          }
        }
      }
    }
    return 0;
  }
  
// Lets count how many urls they have put in their post. Have to convert them to AGcode first.  

  function urlCounter($str)
  {
    $str = ' '.$str;
    $str = preg_replace("#(^|[\n ])((http|https|ftp)://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1[url]$2[/url]", $str);
    $str = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1[url]$2[/url]", $str);
    $str = preg_replace("/\\[url\\]www\\./i", "[url]http://www.", $str);
    $urlCount = preg_match_all("/\[url\]((http|https|ftp):\/\/([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~]*?)\[\/url\]/is",$str,$blah);
    $urlCount += preg_match_all("/\[url=((http|https|ftp):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%#]+?)\](.+?)\[\/url\]/is",$str,$blah);
    
    return $urlCount;
  }  

// Have they posted within the last x seconds? 

  function FloodCheck($ip)
  {
    $the_time = time()-$this->VARS['flood_timeout'];
    $this->query("DELETE FROM ".LAZ_TABLE_PREFIX."_ip WHERE (timestamp < $the_time)");
    $this->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_ip WHERE (guest_ip = '$ip')");
    $this->fetch_array($this->result);
    return ($this->record) ? true : false;
  }

// Convert any naughty words in their post

  function CensorBadWords($strg)
  {
    $replace = '#@*%!';
    if (empty($this->badwords))
    {
      $this->query('SELECT word FROM '.LAZ_TABLE_PREFIX.'_words WHERE type=1');
      while ($row = $this->fetch_array($this->result))
      {
        if ($this->VARS['use_regex'] == 0)
        {
          $this->badwords[] = $row['word'];
        }
        else
        {
          if (!empty($row['word']))
          {
            $this->badwords[] = '/' . str_replace('/', '\/', $row['word']) . '/i';
          }   
        } 
      }
    }

    if ($this->VARS['use_regex'] == 0)
    {
      $strg = str_ireplace($this->badwords, $replace, $strg);
    }
    else
    {
      if (count($this->badwords) > 0)
      {
        $strg = preg_replace($this->badwords, $replace, $strg);
      }
    }
    return $strg;
  }
  
// Check their post for naughty words so we can block them  
  
  function BlockBadWords($strg,$type = 2)
  {
    $this->query('SELECT word FROM '.LAZ_TABLE_PREFIX.'_words WHERE type=' . $type);
    if ($this->VARS['use_regex'] == 0)
    {
      while ($row = $this->fetch_array($this->result))
      {
        if (stripos($strg, $row['word']) !== false)
        {
          return true;
        }
      }
    }
    else
    {
      while ($row = $this->fetch_array($this->result))
      {
        if (preg_match('^'.$row['word'].'^i', $strg))
        {
          return true;
        }
      }
    }
    return false;
  }

// Something is wrong so lets report it

  function gb_error($ERROR, $block_type = 0)
  {
    global $GB_PG;
    /*
      Spam blocks for logging purposes
      -1 - Time spam block count was started/reset
      0  - Total count
      1  - Filled in the Honeypot
      2  - You have banned their IP
      3  - They didn't fill in the anti bot test
      4  - They got the anti bot test wrong
      5  - No timehash
      6  - They failed the header check
      7  - Post contained a blocked word
      8  - Post contained to many URLs
      9  - Stop Forum Spam thinks they are a spammer
    */
    if ($this->VARS['count_blocks'] == 1 && $block_type > 0)
    {
      // Check if this is the first time thi sihas been called or if it's still using old method
      if (empty($this->VARS['block_count']) || (strpos($this->VARS['block_count'], 'a:11:') !== 0))
      {
        $this->VARS['block_count'] = 'a:11:{i:-1;i:' . time() . ';i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;}';
      }
      $stats = @unserialize($this->VARS['block_count']);
      if (is_array($stats))
      {
        $stats[0]++;
        $stats[$block_type]++;
        $stats = serialize($stats);
        $this->query('UPDATE ' . LAZ_TABLE_PREFIX . '_config SET block_count="' . $stats . '" WHERE config_id=' . $this->VARS['config_id']);
      }
    }
    $LANG =& $this->LANG;
    $VARS =& $this->VARS;
    $EMAILJS = '';
    $error_html = '';
    eval("\$error_html .= \"".$this->template->get_template('error')."\";");
    return $error_html;
  }

// Lets show a nice picture of what browser they posted with

  function browser_detect($useragent)
  {
    $browsers = array('opera', 'opr\/', 'trident', 'flock', 'android', 'firefox', 'chrome', 'iphone', 'ipad', 'safari', 'konqueror', 'netscape', 'aol', 'camino', 'chimera', 'crazy', 'galeon', 'k-meleon', 'maxthon', 'slimbrowser', 'amaya', 'avantbrowser', 'msie', 'gecko', 'mozilla');
    $browserCount = sizeof($browsers);
    for ($i=0;$i<$browserCount;$i++)
    {
      if (preg_match("/$browsers[$i]/i", $useragent))
      {
        $theirbrowser = $browsers[$i];
        $theirbrowser = ($theirbrowser == 'mozilla') ? 'ns' : $theirbrowser;
        $theirbrowser = ($theirbrowser == 'gecko') ? 'mozilla' : $theirbrowser;
        $theirbrowser = ($theirbrowser == 'opr\/') ? 'opera' : $theirbrowser;
        $theirbrowser = ($theirbrowser == 'trident') ? 'msie' : $theirbrowser;
        $theirbrowser = ($theirbrowser == 'ipad') ? 'iphone' : $theirbrowser;        
        break;
      }
    }
    $theirbrowser = (!isset($theirbrowser)) ? 'question' : $theirbrowser;
    return $theirbrowser;
  }
  
// Is their email address in the valid format  
// Code from http://www.linuxjournal.com/article/9585
  
  function check_emailaddress($email)
  {
    $isValid = true;
    $atIndex = strrpos($email, '@');
    if (is_bool($atIndex) && !$atIndex)
    {
      $isValid = false;
    }
    else
    {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      elseif ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      elseif ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      elseif (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      elseif (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      elseif(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
    }
    return $isValid;
  }
  
// Create or AGCode/Smiley button bar

  function create_buttons($LANG_CODES)
  {
    $display_tags = '<div style="margin: 0;padding: 0;clear: right;"><div id="agcode" style="display:none;margin: 0;padding: 0;">';
    if ($this->VARS['agcode'] == 1)
    {
      $display_tags .= '
      <button type="button" onclick="agCode(\'b\'); return false;" style="width:34px;font-weight:bold;padding:2px 0;margin:0;" /><img src="'.$this->VARS['base_url'].'/img/agcode/bold.gif" alt="B" /></button>
      <button type="button" onclick="agCode(\'i\'); return false;" style="width:34px;font-style:italic;padding:2px 0;margin:0;" /><img src="'.$this->VARS['base_url'].'/img/agcode/italic.gif" alt="I" /></button>
      <button type="button" onclick="agCode(\'u\'); return false;" style="width:34px;text-decoration:underline;padding:2px 0;margin:0;" /><img src="'.$this->VARS['base_url'].'/img/agcode/underline.gif" alt="U" /></button>
      ';
      if ($this->VARS['allow_emailagcode'] == 1)
      {
        $display_tags .= '<button type="button" onclick="agCode(\'email\'); return false;" style="width:34px;padding:2px 0;margin:0;"><img src="'.$this->VARS['base_url'].'/img/agcode/email.gif" alt="" /></button>
        ';
      }      
      if ($this->VARS['allow_imgagcode'] == 1)
      {
        $display_tags .= '<button type="button" onclick="agCode(\'img\'); return false;" style="width:34px;padding:2px 0;margin:0;"><img src="'.$this->VARS['base_url'].'/img/agcode/img.gif" alt="" /></button>
        ';
      }
      if ($this->VARS['allow_urlagcode'] == 1)
      {
        $display_tags .= '<button type="button" onclick="agCode(\'url\'); return false;" style="width:34px;padding:2px 0;margin:0;" /><img src="'.$this->VARS['base_url'].'/img/agcode/url.gif" alt="" /></button>
        ';
      }
      if ($this->VARS['allow_flashagcode'] == 1)
      {
        $display_tags .= '<button type="button" onclick="agCode(\'flash\'); return false;" style="width:34px;padding:2px 0;margin:0;" /><img src="'.$this->VARS['base_url'].'/img/agcode/flash.gif" alt="" /></button>
        ';
      }
    }
    if ($this->VARS['smilies'] == 1)
    {
      $display_tags .= '<button type="button" onclick="toggleSlide(\'LazSmileys\'); return false;" style="width:34px;padding:2px 0;margin:0;" /><img src="'.$this->VARS['base_url'].'/img/smilies/a1.gif" height="18" width="18" alt="" border="0" /></button>
      ';
    }     
    $display_tags .= '</div>';
    if ($this->VARS['smilies'] == 1)
    {
     $display_tags .= '<a href="'.$LANG_CODES.'?show=smilies" target="_blank" style="margin:0;padding:0;" id="show_smileys"><button type="button" style="width:34px;padding:2px 0;margin:0;" /><img src="'.$this->VARS['base_url'].'/img/smilies/a1.gif" id="showSmileys" height="18" width="18" alt="" border="0" /></button></a>
     ';
    }
    $display_tags .= '</div>';
    $display_tags .= "
    <script type=\"text/javascript\">
    <!--
      if (document.getElementById) {
        document.getElementById('agcode').style.display = 'block';";
    if ($this->VARS['smilies'] == 1)
    {
      $display_tags .= "\n  document.getElementById('show_smileys').style.display = 'none';";
    }
    $display_tags .= "
     }
     else if (document.all) {
       document.all['agcode'].style.display = 'block';";
    if ($this->VARS['smilies'] == 1)
    {
      $display_tags .= "\n  document.all['show_smileys'].style.display = 'none';";
    }
    $display_tags .= "
     }
     else if (document.layers)   {
       document.layers['agcode'].style.display = 'block';";
    if ($this->VARS['smilies'] == 1)
    {
      $display_tags .= "  document.layers['show_smileys'].style.display = 'none';";
    }
    $display_tags .= "
     }
    // -->
    </script>";
    return $display_tags;
  }  

// Work out the captcha code and compare it with what the submitted
  
  function captcha_test($thecode, $hash)
  {
    global $GB_DB;
    $realcode = md5($GB_DB['user']) . md5($hash);
    $realcode = md5($realcode);
    $realcode = $realcode[0] . $realcode[7] . $realcode[14] . $realcode[21] . $realcode[28];
    // Just an array for turning numbers into letters
    $captchanum = array(0 => 'V', 1 => 'H', 2 => 'K', 3 => 'M', 4 => 'P', 5 => 'S', 6 => 'T', 7 => 'W', 8 => 'X', 9 => 'Z');
    for($i = 0; $i <= 4; $i++)
    {
      $realcode[$i] = (is_numeric($realcode[$i])) ? $captchanum[$realcode[$i]] : $realcode[$i];
    }
    if (strtolower($realcode) == strtolower($thecode))
    {
      return true;
    }
    return false;
  }
  
// Check the size of their image funnily enough  
  
  function check_image_size($img_width, $img_height)
  {
    $max_width = $this->VARS['agcode_img_width'];
    $max_height = $this->VARS['agcode_img_height'];
    if ($img_width>$max_width && ($max_width > 0)) 
    {
      $tag_height = ($max_width/$img_width)*$img_height;
      $tag_width = $max_width;
      if ($tag_height>$max_height) 
      {
         $tag_width = ($max_height/$tag_height)*$tag_width;
         $tag_height = $max_height;
      }
    } 
    elseif ($img_height>$max_height && ($max_height > 0)) 
    {
      $tag_width = ($max_height/$img_height)*$img_width;
      $tag_height = $max_height;
      if ($tag_width>$max_width) 
      {
        $tag_height = ($max_width/$tag_width)*$tag_height;
        $tag_width = $this->max_width;
      }
    } 
    else 
    {
      $tag_width = $img_width;
      $tag_height = $img_height;
    }
    $tag_width = round($tag_width);
    $tag_height = round($tag_height);
    return array(
      "$tag_width",
      "$tag_height",
      "width=\"$tag_width\" height=\"$tag_height\""
    );  
  }
  
// My wonderful email function to send as both plain text and HTML. Also attachs any picture they uploaded  
  
  function send_email($emailto, $emailsubject, $emailmessage, $emailheaders, $emailfrom = '', $imagedata = array('mime' => '', 'name' => '', 'data' => ''))
  {
    // Just to convert flash objects back to AGCode since email clients don't like them
    while(preg_match('!<object\b[^>]*>(.*?)value\=\"(.*?)\"(.*?)</object>!i', $emailmessage, $flash_url))
    {
      $emailmessage = preg_replace('!<object\b[^>]*>(.*?)value\=\"(.*?)\"(.*?)</object>!i', "[flash]<a href=\"$2\" target=\"_blank\">$2</a>[/flash]", $emailmessage);
    }
    // End of Flash AGCode reverse
    $emailboundary = md5(time()); // Just something random for the boundary
    $emailboundary = '----=Email_Boundary_'.$emailboundary; // Full boundary
    if(!empty($imagedata['mime']) && !empty($imagedata['name']) && !empty($imagedata['data']))
    {
      $emailheaders .= "\nDate: " . date('r') . "\nX-Mailer: Lazarus Guestbook\nMIME-Version: 1.0\nContent-type: multipart/mixed;\n   boundary=\"".$emailboundary."\"";
      
      $emailbody = "\n\nThis is a multi-part message in MIME format.\nIf you are reading this then consider updating your email program.\n\n--".$emailboundary."\nContent-type: multipart/alternative;\n   boundary=\"".$emailboundary."Laz\"\n\n".
      '--'.$emailboundary."Laz\nContent-type: text/plain; charset=".$this->VARS['charset']."; format=flowed\nContent-Transfer-Encoding: 7bit\nContent-Disposition: inline\n\n".
      $this->undo_htmlspecialchars(strip_tags($emailmessage)).
      "\n\n--".$emailboundary."Laz\nContent-type: text/html; charset=".$this->VARS['charset']."\nContent-Transfer-Encoding: 7bit\nContent-Disposition: inline\n\n".
      "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n
      <html>\n
      <body>\n".
      $emailmessage.
      "\n</body>\n</html>\n\n--".$emailboundary.
      "Laz--\n\n--".$emailboundary."\nContent-type: ".$imagedata['mime']."; name=\"".$imagedata['name']."\"\n".
      "Content-Transfer-Encoding: base64\nContent-Disposition: attachment; filename=\"".$imagedata['name']."\"\n\n".$imagedata['data'].
      "--".$emailboundary."--\n";;      
    }
    else
    {
      $emailheaders .= "\nX-Mailer: Lazarus Guestbook\nMIME-Version: 1.0\nContent-type: multipart/alternative;\n   boundary=\"".$emailboundary."\"";
      
      $emailbody = "\n\nThis is a multi-part message in MIME format.\nIf you are reading this then consider updating your email program.\n\n".
      '--'.$emailboundary."\nContent-type: text/plain; charset=\"".$this->VARS['charset']."\"\nContent-Transfer-Encoding: 7bit\n\n".
      $this->undo_htmlspecialchars(strip_tags($emailmessage)).
      "\n\n--".$emailboundary."\nContent-type: text/html; charset=\"".$this->VARS['charset']."\"\nContent-Transfer-Encoding: 7bit\n\n".
      "<html>\n<body>\n".
      $emailmessage.
      "\n</body>\n</html>\n\n--".$emailboundary.'--';
    }
    if($this->VARS['mail_type'] == 2)
    {
      $this->smtpEmail($emailfrom, $emailto, $emailsubject, $emailbody, $emailheaders);
    }
    else
    {
      if (ini_get('safe_mode'))
      {
        mail($emailto, $emailsubject, $emailbody, $emailheaders);
      }
      else
      {
        mail($emailto, $emailsubject, $emailbody, $emailheaders, '-f '.$emailfrom);
      }
    }
    return;
  }
  
  // SMTP email sending
  function smtpEmail($from, $to, $subject, $message, $headers)
  {
    $timeout = '30'; // how long to keep trying
    $localhost = 'localhost'; // How to identify ourselves
    
    /* * * * POP Login if required * * */  /*
    if(!empty($this->VARS['popServer']))
    {
      $ssl = ($this->VARS['mailSSL'] != 0) ? (($this->VARS['mailSSL'] == 1) ? 'ssl://' : 'tls://') : ''; // If SSL or TLS add it
      $popConnect = @fsockopen($ssl.$this->VARS['popServer'], $this->VARS['popPort'], $errno, $errstr, $timeout); // Connect
      if(!$popConnect) // If we fail to connect...
      {
        $this->logArray['POPconnect'] = $errstr . '(' . $errno . ')'; // Log the given reason...
        $this->logMailError(); // And output to the log file.
        return false;
      }
      else
      {
        $this->logArray['POPconnect'] = @fgets($popConnect, 515)); // POP servers only return single line replies. Or should.
        if(!$this->mailPackets('AUTH LOGIN', $popConnect, 'SMTPauth')) //Request Auth Login
        {
          return false;
        }
        if(!$this->mailPackets('USER ' . $smtpUser, $popConnect, 'POPuser')) // Send username. POP is plaintext
        {
          return false;
        }    
        if(!$this->mailPackets('PASS ' . $smtpPass, $popConnect, 'POPpass')) // Send password, again in plaintext
        {
          return false;
        }
        if(!$this->mailPackets('QUIT', $popConnect, 'POPquit')) // Say bye to the server
        {
          return false;
        }    
        fclose($popConnect); // Close connection
      }
    }     
    /* * * * End of POP Login * * * * */
    
    /* * * * Start of SMTP stuff * * * */
    $ssl = ($this->VARS['mailSSL'] != 0) ? (($this->VARS['mailSSL'] == 1) ? 'ssl://' : 'tls://') : ''; // Set the encryption if needed
    $smtpConnect = @fsockopen($ssl.$this->VARS['smtp_server'], $this->VARS['smtp_port'], $errno, $errstr, $timeout); // Connect
    if (!$smtpConnect) // If we fail to connect...
    {
      $this->logArray['SMTPconnect'] = $errstr . '(' . $errno . ')'; // Add the reason to the log...
      $this->logMailError(); // Then output the log
      return;
    }
    else
    {
      $cnectKey = 0;
      do
      {
        $smtpResponse = @fgets($smtpConnect, 515);
        $cnectKey++;
        $this->logArray['SMTPconnect' . $cnectKey] = $smtpResponse;
        $responseCode = substr($smtpResponse, 0, 3); // Grab the response code from start of the response
        // If we get an error terminate the sending and log the results so far
        if($responseCode >= 400)
        {  
          $this->logMailError($smtpConnect);
          return;
        }        
      }
      while ((strlen($smtpResponse) > 3) && (strpos($smtpResponse, ' ') != 3));
      $ehlo = $this->mailPackets('EHLO ' . $localhost, $smtpConnect, 'SMTPehlo'); // Let's try using EHLO first
      if ($ehlo != 250) // Server said it didn't like EHLO so drop back to HELO
      {
        if (!$this->mailPackets('HELO ' . $localhost, $smtpConnect, 'SMTPhelo')) // Send HELO. No EHLO means server doesn't support AUTH
        {
          return;
        }
      }
      if (!empty($this->VARS['smtp_username']) && ($ehlo == 250)) // We have a username and server supports EHLO so send login credentials
      {
        if (!$this->mailPackets('AUTH LOGIN', $smtpConnect, 'SMTPauth')) // Request Auth Login
        {
          return;
        }
        if (!$this->mailPackets(base64_encode($this->VARS['smtp_username']), $smtpConnect, 'SMTPuser')) // Send username
        {
          return;
        }
        if (!$this->mailPackets(base64_encode($this->VARS['smtp_password']), $smtpConnect, 'SMTPpass')) // Send password
        {
          return;
        }
      }
      if (!$this->mailPackets('MAIL FROM:<' . $from . '>', $smtpConnect, 'SMTPfrom')) // Email From
      {
        return;
      }
      if (!$this->mailPackets('RCPT TO:<' . $to . '>', $smtpConnect, 'SMTPrcpt')) // Email To
      {
        return;
      }
      if (!$this->mailPackets('DATA', $smtpConnect, 'SMTPmsg')) // We are about to send the message
      {
        return;
      }
      // First lets make sure both the message and additional headers do not contain anythign that might be seen as end of message marker
      $message = preg_replace(array("/(?<!\r)\n/", "/\r(?!\n)/", "/\r\n\./"), array("\r\n", "\r\n", "\r\n.."), $message);
      $headers = (!empty($headers)) ? "\r\n" . preg_replace(array("/(?<!\r)\n/", "/\r(?!\n)/", "/\r\n\./"), array("\r\n", "\r\n", "\r\n.."), $headers) : '';
      // Create the default headers, attach any additonal headers
      $headers = "To: <".$to.">\r\nSubject: ".$subject."\r\nDate: " . gmdate('D, d M Y H:i:s') . " -0000".$headers;
      if (!$this->mailPackets($headers."\r\n\r\n".$message."\r\n.", $smtpConnect, 'SMTPbody')) // The message
      {
        return;
      }
      $this->mailPackets('QUIT', $smtpConnect, 'SMTPquit'); // Say Bye to SMTP server
      fclose($smtpConnect); // Be nice and close the connection
      return; // Return the fact we sent the message
    }
  }
  
  // This function sends the actual packets then logs the reponses and parses the reponse code
  function mailPackets($sendStr,$mailConnect,$logName = '')
  {
    $newLine = "\r\n"; // LEAVE THIS ALONE. Some servers demand Windows line endings 
    $keyCount = 0;  // Just an incremental counter for when we get more than a single line response
    fputs($mailConnect,$sendStr . $newLine); // Send the packet 
    do // Start grabbing the responses until we either get a terminal error or told we are at the end
    {
      $mailResponse = @fgets($mailConnect, 515); // Receive the response
      $keyCount++; // Increment the key count
      $this->logArray[$logName . $keyCount] = $mailResponse; // Put the response in to the log array
      $responseCode = substr($mailResponse, 0, 3); // Grab the response code from start of the response
      // Check for error codes except on ehlo, auth, and user details as they are not always fatal
      if ((($logName != 'SMTPauth') && ($logName != 'SMTPuser') && ($logName != 'SMTPehlo') && ($logName != 'SMTPpass')) && ($responseCode >= 400))
      {
         $this->logMailError($mailConnect);
         return false;
      }
      elseif (substr($responseCode, 0, 1) == 4 || $responseCode >= 521 && $logName != 'SMTPehlo')
      {
         $this->logMailError($mailConnect);
         return false;
      }
    }
    while ((strlen($mailResponse) > 3) && (strpos($mailResponse, ' ') != 3)); // Loop until we get the end response
    return $responseCode; // Return the response code
  }
  
  function logMailError($mailServer = '')
  {
    if (!empty($mailServer)) 
    {
      fclose($mailServer); // Be nice and close the connection
    }
    if (is_writeable(LAZ_INCLUDE_PATH . '/smtplog.txt')) 
    {
      $fd = fopen(LAZ_INCLUDE_PATH . '/smtplog.txt', 'a');
      $smtpresults = print_r($this->logArray, true);
      fwrite($fd,$smtpresults);
      fclose ($fd);
    }
    return;
  }
  
  // Just a function to put headers in correct case
  function fix_case($header)
  {
    $header = str_replace('HTTP_', '', $header);
    $header = strtolower(str_replace('_', ' ', $header));
    $header = str_replace(' ', '-', ucwords($header));
    return $header;
  }
  
  // Ok this is the function to create an array of the headers
  function fetch_headers()
  {
    if (!is_callable('getallheaders')) 
    {
      $headers = array();
      foreach ($_SERVER as $h => $v) 
      {
        if (strpos($h, 'HTTP_') === 0) 
        {
          $headers[$this->fix_case($h)] = $v;
        }
      }
    } 
    else 
    {
      $headers = getallheaders();
    }
    return $headers;
  }
  
  // Just a function to check what headers have been sent
  // Called by 1 = entry form, 2 = comment form, 3 = captcha, 4 = nothing
  function check_headers($called_by = 4, $theirIP)
  {
    $headers = $this->fetch_headers();
    
    // Browsers always send Accept
    if (!array_key_exists('Accept', $headers) || array_key_exists('Proxy-Connection', $headers) || array_key_exists('Range', $headers)) 
    {      
      return 1;
    }
    
    // Do we have a useragent?
    if (array_key_exists('User-Agent', $headers)) 
    {
      // If user agent claims to be IE do some IE only tests.
      if (strpos(strtolower($headers['User-Agent']), 'msie') !== false) 
      {      
        if (preg_match('/(Windows ME)|(Windows XP)|(Windows 2000)|(Win32)/i', $headers['User-Agent'])) 
        {
          return 2;
        }
      }
      
      // Check for some known bad useragents
      if ($headers['User-Agent'] === 'Mozilla/5.0' || strpos($headers['User-Agent'], 'Java/') === 0 || strpos($headers['User-Agent'], 'libwww-perl') === 0) 
      {          
        return 3;
      }
    }
    
    // The browser should post using the form type we set
    if ($called_by < 3 && !empty($headers['Content-Type'])) 
    {
      if (strpos(strtolower($headers['Content-Type']), 'multipart/form-data;') !== 0) 
      {
        return 4;
      }
    }
    
    // Just a check for some proxys
    if ($headers['Accept-Encoding'] === 'identity') 
    {
      return 5;
    }
    
    // Check for known bad hosts via rDNS
    $badHosts = array(
      'ovh.net',
      'servinio.com',
      'turnkeyinternet.net',
      'kimsufi.com',
      'steephost.com',
      'starnet.md',
      'amazonaws.com',
      'bezeqint.net',
      '.server.de',
      'your-server.de',
      'ertelecom.ru',
      'hostnoc.net',
      'dedibox.fr',
      'dimenoc.com',
      'ukrtel.net',
      'ubiquityservers.com',
      'ltdomains.com',
      '.unit-is.com',
      'leaseweb.com'
    );
   
    $theirHost = gethostbyaddr($theirIP);
   
    foreach ($badHosts as $badHost) 
    {
      if (strpos($theirHost, $badHost) !== false) 
      {
        return 6;
      }
    }
    
    return 0;
  }

}
?>