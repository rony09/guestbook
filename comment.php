<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.2 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Sat, 27 July 2013 10:27:57 GMT
 * ----------------------------------------------
 */
 
define('IS_INCLUDE', false); 
 
define('LAZ_INCLUDE_PATH', dirname(__FILE__));
include_once LAZ_INCLUDE_PATH.'/admin/version.php';
include_once LAZ_INCLUDE_PATH.'/admin/config.inc.php';
include_once LAZ_INCLUDE_PATH.'/lib/' . $DB_CLASS;
include_once LAZ_INCLUDE_PATH.'/lib/image.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/template.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/vars.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/comment.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/session.class.php';

define('LAZ_TABLE_PREFIX', $table_prefix);

//
// Convert any data into usable variables
//

$gb_com = new gb_comment(LAZ_INCLUDE_PATH);

if($gb_com->db->VARS['included'] == 1)
{
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: " . $gb_com->db->VARS['laz_url']);
  exit;
}
elseif($gb_com->db->VARS['included'] == 2)
{
  header("HTTP/1.0 404 Not Found");
  exit;
}

$GB_PG['base_url'] = $gb_com->db->VARS['base_url'];
$GB_PG['index']    = $gb_com->db->VARS['base_url'].'/index.php';
$GB_PG['admin']    = $gb_com->db->VARS['base_url'].'/admin.php';
$GB_PG['comment']  = $gb_com->db->VARS['base_url'].'/comment.php';
$GB_PG['addentry'] = $gb_com->db->VARS['base_url'].'/addentry.php';

if ($gb_com->db->VARS['disablecomments'] == 1)
{
   header("Location: $GB_PG[index]");
   die ("Access Denied!");
}
else
{
  if ($gb_com->db->VARS['disablecomments'] == 2)
  {
    $gbsession = (isset($_GET['gbsession'])) ? addslashes($_GET['gbsession']) : '';
    $rid = (isset($_GET['rid'])) ? intval($_GET['rid']) : '';
    $uid = (isset($_GET['uid'])) ? intval($_GET['uid']) : '';
    $included = (isset($_GET['included'])) ? '&amp;included='.intval($_GET['included']) : '';
    $gb_auth = new gb_session(LAZ_INCLUDE_PATH);
    $AUTH = $gb_auth->checkSessionID();
    if(!$AUTH)
    {
       header("Location: $GB_PG[index]");
       die ("Access Denied!");
    }
  }
  $antispam = $gb_com->db->VARS['antispam_word'];
  $gb_com->id = (isset($_GET['gb_id'])) ? $_GET['gb_id'] : '';
  $gb_com->id = (isset($_POST['gb_id'])) ? $_POST['gb_id'] : $gb_com->id;
  $gb_com->comment = (isset($_POST['gb_comment'])) ? $_POST['gb_comment'] : '';
  $gb_com->timehash = (isset($_POST['gb_timehash'])) ? $_POST['gb_timehash'] : '';
  $gb_com->user = (isset($_POST['gb_user'])) ? $_POST['gb_user'] : '';
  $gb_com->email = (isset($_POST['gb_email'])) ? $_POST['gb_email'] : '';
  if(($gb_com->db->VARS['solve_media'] == 1) && ($gb_com->db->VARS['antibottest'] == 2))
  {
    $gb_com->bottest = (isset($_POST['adcopy_response'])) ? trim($_POST['adcopy_response']) : '';
  }
  else
  {
    $gb_com->bottest = (isset($_POST['gb_bottest'])) ? trim($_POST['gb_bottest']) : '';
  }
  $gb_com->pass_comment = (isset($_POST['pass_comment'])) ? trim($_POST['pass_comment']) : '';
  $gb_com->honeypot = (isset($_POST['gb_username'])) ?  1 : 0;
  $gb_action = (isset($_POST['gb_action'.$antispam])) ? $_POST['gb_action'.$antispam] : '';
  $gb_com->comment_action($gb_action);
}

?>
