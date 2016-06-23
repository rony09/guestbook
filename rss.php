<?php
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 13 March 2013 20:17:01 GMT
 * ----------------------------------------------
 */
ob_start();
define('LAZ_INCLUDE_PATH', dirname(__FILE__));
require_once LAZ_INCLUDE_PATH.'/admin/version.php';
require_once LAZ_INCLUDE_PATH.'/admin/config.inc.php';
require_once LAZ_INCLUDE_PATH.'/lib/mysql.class.php';
require_once LAZ_INCLUDE_PATH.'/lib/vars.class.php';
require_once LAZ_INCLUDE_PATH.'/lib/template.class.php';

define('LAZ_TABLE_PREFIX', $table_prefix);

$entry = (isset($_GET['entry'])) ? intval($_GET['entry']) : 0;

$db = new guestbook_vars(LAZ_INCLUDE_PATH);
$db->getVars();

header('Content-type: application/xml; charset='.$db->VARS['charset'].'');

function selfURL() 
{ 
  $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
  $protocol = strleft(strtolower($_SERVER['SERVER_PROTOCOL']), '/').$s; 
  $port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':'.$_SERVER['SERVER_PORT']); 
  return $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; 
}

function strleft($s1, $s2) 
{ 
  return substr($s1, 0, strpos($s1, $s2));
}

$whereRwe = selfURL();

$i = 0;
$items = '';
$lastbuild = '';

$result = $db->query('SELECT * FROM '.LAZ_TABLE_PREFIX.'_data WHERE accepted=1 ORDER BY id DESC LIMIT '. $entry .','.$db->VARS['entries_per_page']);
while($lentry = $db->fetch_array($result))
{
  while(preg_match('!<object\b[^>]*>(.*?)value\=\"(.*?)\"(.*?)</object>!i', $lentry['comment']))
  {
    $lentry['comment'] = preg_replace('!<object\b[^>]*>(.*?)value\=\"(.*?)\"(.*?)</object>!i', "[flash]<a href=\"$2\" target=\"_blank\">$2</a>[/flash]", $lentry['comment']);
  }
  if ($i == 0)
  {
    $lastbuild = date("r", $lentry['date']);
  }
  if($db->VARS['included'] > 0)
  {
    if(strpos($db->VARS['laz_url'], '?') !== false)
    {
      $entryLink = $db->VARS['laz_url'] . '&amp;permalink=true&amp;entry=' . $lentry['id'];
    }
    else
    {
      $entryLink = $db->VARS['laz_url'] . '?permalink=true&amp;entry=' . $lentry['id'];
    }
  }
  else
  {
    $entryLink = $db->VARS['base_url'].'/index.php?permalink=true&amp;entry='.$lentry['id'];
  }
  $items .= "<item>\n";
  $items .= '  <title>' . $lentry['name'] . "</title>\n";
  $items .= '  <link>' . $entryLink . "</link>\n";
  $items .= '  <guid>' . $entryLink . "</guid>\n";
  $items .= '  <description>' . htmlspecialchars(nl2br($lentry['comment'])) . "</description>\n";
  $items .= '  <pubDate>' . gmdate("r", $lentry['date']) . "</pubDate>\n";
  //$items .= '  <comments>' . $db->VARS['base_url'].'/comment.php?gb_id='.$lentry['id'] . "</comments>\n";
  $items .= "</item>\n";
  $i++;
}
header ('Content-type: text/xml');
echo '<?xml version="1.0" encoding="'.$db->VARS['charset'].'" ?>'."\n";


echo '<rss version="2.0"  xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>Lazarus Guestbook</title>
    <link>'.$db->VARS['base_url'].'</link>
    <description>Latest Guestbook Entries</description>
    <lastBuildDate>'.$lastbuild.'</lastBuildDate>
    <atom:link href="'.$whereRwe.'" rel="self" type="application/rss+xml" />
'.$items.'
  </channel>
</rss>'; 

$db->close_db();
echo trim(ob_get_clean());
?>