<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Tue, 30 July 2013 20:35:33 GMT
 * ----------------------------------------------
 */
 
define('IS_INCLUDE', false);

define('LAZ_INCLUDE_PATH', dirname(__FILE__));
//LAZ_INCLUDE_PATH = dirname(__FILE__);
include_once LAZ_INCLUDE_PATH.'/admin/version.php';
include_once LAZ_INCLUDE_PATH.'/admin/config.inc.php';
include_once LAZ_INCLUDE_PATH.'/lib/' . $DB_CLASS;
include_once LAZ_INCLUDE_PATH.'/lib/image.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/template.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/vars.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/add.class.php';

define('LAZ_TABLE_PREFIX', $table_prefix);

$gb_post = new addentry(LAZ_INCLUDE_PATH);

if($gb_post->db->VARS['included'] == 1)
{
  header("HTTP/1.1 301 Moved Permanently");
  header ("Location: " . $gb_post->db->VARS['laz_url']);
  exit;
}
elseif($gb_post->db->VARS['included'] == 2)
{
  header("HTTP/1.0 404 Not Found");
  exit;
}

$GB_PG['base_url'] = $gb_post->db->VARS['base_url'];
$GB_PG['index']    = $gb_post->db->VARS['base_url'].'/index.php';
$GB_PG['admin']    = $gb_post->db->VARS['base_url'].'/admin.php';
$GB_PG['comment']  = $gb_post->db->VARS['base_url'].'/comment.php';
$GB_PG['addentry'] = $gb_post->db->VARS['base_url'].'/addentry.php';

$antispam = $gb_post->db->VARS['antispam_word'];

//
// Here we just check if anything was submitted and if so handle it
//

if (isset($_POST['agb_submit_'.$antispam]) || isset($_POST['agb_preview_'.$antispam]))
{
   $gb_post->name = (isset($_POST['gb_name'])) ? $_POST['gb_name'] : '';
   $gb_post->email = (isset($_POST['gb_email'])) ? $_POST['gb_email'] : '';
   $gb_post->url = (isset($_POST['gb_url'])) ? $_POST['gb_url'] : '';
   $gb_post->comment = (isset($_POST['gb_comment'])) ? $_POST['gb_comment'] : '';
   $gb_post->location = (isset($_POST['gb_location'])) ? $_POST['gb_location'] : '';
   $gb_post->icq = (isset($_POST['gb_icq'])) ? $_POST['gb_icq'] : 0;
   $gb_post->aim = (isset($_POST['gb_aim'])) ? $_POST['gb_aim'] : '';
   $gb_post->msn = (isset($_POST['gb_msn'])) ? $_POST['gb_msn'] : '';
   $gb_post->yahoo = (isset($_POST['gb_yahoo'])) ? $_POST['gb_yahoo'] : '';
   $gb_post->skype = (isset($_POST['gb_skype'])) ? $_POST['gb_skype'] : '';
   $gb_post->gender = (isset($_POST['gb_gender'])) ? $_POST['gb_gender'] : '';
   if(($gb_post->db->VARS['solve_media'] == 1) && ($gb_post->db->VARS['antibottest'] == 2))
   {
     $gb_post->bottest = (isset($_POST['adcopy_response'])) ? trim($_POST['adcopy_response']) : '';
   }
   else
   {
     $gb_post->bottest = (isset($_POST['gb_bottest'])) ? trim($_POST['gb_bottest']) : '';
   }
   $gb_post->timehash = (isset($_POST['gb_timehash'])) ? $_POST['gb_timehash'] : '';
   $gb_post->keep_pic = (isset($_POST['keep_pic'])) ? 1 : 0;
   $gb_post->userfile = (isset($_FILES['userfile']['tmp_name']) && $_FILES['userfile']['tmp_name'] != '') ? $_FILES : '';
   $gb_post->user_img = ((isset($_POST['gb_user_img'])) && ($gb_post->keep_pic == 1) && (empty($gb_post->userfile))) ? $_POST['gb_user_img'] : '';
   $gb_post->preview = (isset($_POST['gb_preview'])) ? 1 : 0;
   $gb_post->private = (isset($_POST['gb_private'])) ? 1 : 0;
   $gb_post->honeypot = (isset($_POST[$antispam])) ? 1 : 0;
   $gb_action = (isset($_POST['agb_preview_'.$antispam])) ? 'preview' : 'submit';
   echo $gb_post->process($gb_action);
}
else
{
   echo $gb_post->process();  // nothing submitted so display the form
}

?>