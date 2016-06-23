<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Tue, 30 July 2013 20:36:00 GMT
 * ----------------------------------------------
 */
 
if ((strpos(' '.$_SERVER['REQUEST_URI'], 'gbinclude.php')) || (strpos(' '.$_SERVER['PHP_SELF'], 'gbinclude.php')))
{
  die ("You cannot access this file directly.<br>\nThis file is designed to make integrating the guestbook into your site easier.<br>\nPlace the code you will find in the Style section of the admin into a webpage where you want the guestbook to appear.<br>\nPlease remember that the page you place the include code on must end with the .php extension and not .htm nor .html");
}

define('LAZ_INCLUDE_PATH', dirname(__FILE__));
global $GB_DB, $GB_PG;
include_once LAZ_INCLUDE_PATH.'/admin/version.php';
include_once LAZ_INCLUDE_PATH.'/admin/config.inc.php';
include_once LAZ_INCLUDE_PATH.'/lib/' . $DB_CLASS;
include_once LAZ_INCLUDE_PATH.'/lib/image.class.php';
include_once LAZ_INCLUDE_PATH.'/lib/template.class.php';

define('LAZ_TABLE_PREFIX', $table_prefix);

define('IS_INCLUDE', true);

ob_start();

$agbook = (isset($_GET['agbook'])) ? $_GET['agbook'] : '';

function display_entries()
{
  global $GB_PG;
  include_once LAZ_INCLUDE_PATH.'/lib/gb.class.php';
  $gb = new guestbook(LAZ_INCLUDE_PATH);
  $GB_PG['base_url'] = $gb->db->VARS['base_url'];
  $GB_PG['admin'] = $GB_PG['base_url'].'/admin.php';
  $GB_PG['index']= $gb->db->VARS['laz_url'];
  $GB_PG['comment']  = (strpos($gb->db->VARS['laz_url'], '?')) ? $gb->db->VARS['laz_url'].'&amp;agbook=comment' : $gb->db->VARS['laz_url'].'?agbook=comment';
  $GB_PG['addentry'] = (strpos($gb->db->VARS['laz_url'], '?')) ? $gb->db->VARS['laz_url'].'&amp;agbook=addentry' : $gb->db->VARS['laz_url'].'?agbook=addentry';
  $entry = (isset($_GET['entry'])) ? $_GET['entry'] : 0;
  $entry = (isset($_POST['entry'])) ? $_POST['entry'] : $entry;
  $entry = (intval($entry) < 0) ? 0 : intval($entry);
  $gb->searchfield = (isset($_GET['searchfield'])) ? trim($_GET['searchfield']) : '';
  $gb->searchtext = (isset($_GET['searchtext'])) ? trim(urldecode($_GET['searchtext'])) : '';
  $gb->searchfield = (isset($_POST['searchfield'])) ? trim($_POST['searchfield']) : $gb->searchfield;
  $gb->searchtext = (isset($_POST['searchtext'])) ? trim(urldecode($_POST['searchtext'])) : $gb->searchtext; 
  echo $gb->show_entries($entry);
  $gb->db->close_db();
}

switch ($agbook)
{

  case 'comment':
    include_once LAZ_INCLUDE_PATH.'/lib/vars.class.php';
    include_once LAZ_INCLUDE_PATH.'/lib/comment.class.php';
    $gb_com = new gb_comment(LAZ_INCLUDE_PATH);
    if ($gb_com->db->VARS['disablecomments'] > 0)
    {
      display_entries();
      break;
    }    
    $GB_PG['base_url'] = $gb_com->db->VARS['base_url'];
    $GB_PG['admin'] = $GB_PG['base_url'].'/admin.php';
    $GB_PG['index'] = $gb_com->db->VARS['laz_url'];
    $GB_PG['comment']  = (strpos($gb_com->db->VARS['laz_url'], '?')) ? $gb_com->db->VARS['laz_url'].'&amp;agbook=comment' : $gb_com->db->VARS['laz_url'].'?agbook=comment';
    $GB_PG['addentry'] = (strpos($gb_com->db->VARS['laz_url'], '?')) ? $gb_com->db->VARS['laz_url'].'&amp;agbook=addentry' : $gb_com->db->VARS['laz_url'].'?agbook=addentry';
    $antispam = $gb_com->db->VARS['antispam_word'];
    $gb_com->id = (isset($_GET['gb_id'])) ? $_GET['gb_id'] : '';
    $gb_com->id = (isset($_POST['gb_id'])) ? $_POST['gb_id'] : $gb_com->id;
    $gb_com->id = intval($gb_com->id);
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
    $gb_com->db->close_db();
    break;

  case 'addentry':
    include_once LAZ_INCLUDE_PATH.'/lib/vars.class.php';
    include_once LAZ_INCLUDE_PATH.'/lib/add.class.php';
    $gb_post = new addentry(LAZ_INCLUDE_PATH);
    $GB_PG['base_url'] = $gb_post->db->VARS['base_url'];
    $GB_PG['admin']= $GB_PG['base_url'].'/admin.php';
    $GB_PG['index']= $gb_post->db->VARS['laz_url'];
    $GB_PG['comment']  = (strpos($gb_post->db->VARS['laz_url'], '?')) ? $gb_post->db->VARS['laz_url'].'&amp;agbook=comment' : $gb_post->db->VARS['laz_url'].'?agbook=comment';
    $GB_PG['addentry'] = (strpos($gb_post->db->VARS['laz_url'], '?')) ? $gb_post->db->VARS['laz_url'].'&amp;agbook=addentry' : $gb_post->db->VARS['laz_url'].'?agbook=addentry';
    $antispam = $gb_post->db->VARS['antispam_word'];
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
      $gb_post->timehash = (isset($_POST['gb_timehash'])) ? $_POST['gb_timehash'] : '';
      if(($gb_post->db->VARS['solve_media'] == 1) && ($gb_post->db->VARS['antibottest'] == 2))
      {
        $gb_post->bottest = (isset($_POST['adcopy_response'])) ? trim($_POST['adcopy_response']) : '';
      }
      else
      {
        $gb_post->bottest = (isset($_POST['gb_bottest'])) ? trim($_POST['gb_bottest']) : '';
      }
      $gb_post->gender = (isset($_POST['gb_gender'])) ? $_POST['gb_gender'] : '';
      $gb_post->keep_pic = (isset($_POST['keep_pic'])) ? 1 : 0;
      $gb_post->userfile = (isset($_FILES['userfile']['tmp_name']) && $_FILES['userfile']['tmp_name'] != '') ? $_FILES : '';
      $gb_post->user_img = ((isset($_POST['gb_user_img'])) && ($gb_post->keep_pic == 1) && (empty($gb_post->userfile))) ? $_POST['gb_user_img'] : '';
      $gb_post->preview = (isset($_POST['gb_preview'])) ? 1 : 0;
      $gb_post->private = (isset($_POST['gb_private'])) ? 1 : 0;
      $gb_post->honeypot = (isset($_POST['gb_username'])) ?  1 : 0;
      $gb_action = (isset($_POST['agb_preview_'.$antispam])) ? 'preview' : 'submit';
      echo $gb_post->process($gb_action);
    }
    else
    {
      echo $gb_post->process();
    }
    $gb_post->db->close_db();
    break;

  default:
    include_once LAZ_INCLUDE_PATH.'/lib/vars.class.php';
    display_entries();
}

ob_end_flush();

?>