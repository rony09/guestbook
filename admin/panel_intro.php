<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Guestbook - Admin Panel</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script> 
<style type="text/css">
<!--
.contentholder {  padding: 0; margin: 0;  border-width: 0 1px 1px 1px; border-style: solid; border-color: #BBD8E7; background-color: #FFF; }
.optionsTitle { color: #0A3E75; background-color: #EAF3FA; font-weight: bold; font-size: 20px; height: 26px;  margin: 0; padding: 0; text-align: center; border-width: 1px 0; border-style: solid; border-color: #BBD8E7; }
.adminSection { color: #000; background-color: #FFF; font-size: 12px; margin: 0; padding: 0; }
.adminSection p { padding: 5px; margin: 0; }
-->
</style>
</head>
<body>
<h4 align="center">G U E S T B O O K &nbsp; A D M I N</h4>
<?php 
$include_path = dirname(__FILE__);
include($include_path.'/menu.php'); 
?>
<div class="contenttable">
<div class="contentholder">
<div class="optionsTitle">Administration</div>
<div class="adminSection">
<p>Welcome to the administration section of your Lazarus guestbook.</p>
<p>In the <b>MESSAGES</b> section you can see the full details of every post that has been made as well as edit, delete, accept, unaccept and remove any uploaded images from them.</p>
<p>In the <b>SETTINGS</b> section you can modify all the settings available to you in Lazarus.</p>
<p><i><b>general</b></i> are usually settings you set the first time you use Lazarus and then forget about such as which character set to use, which language to use, which AGCodes (if any) you want to allow.</p>
<p><i><b>fields</b></i> is where you can select which fields you want to use in your guestbook entries such as the guests location, their website, their IM identity and if you want to force them to supply and email address.</p>
<p><i><b>email</b></i> is where you can say if you want to send a thank you email to guests when they post and even specify the wording of the email. You can also supply your own address and be notified of new entries and comments</p>
<p><i><b>security</b></i> lets you turn on image verification to prevent bots from posting, block ip addresses, block posts containing words that you have specified or just replace the words with <i><b>#@*%!</b></i> and much more.</p>
<p><i><b>style</b></i> is where you can specify what colours and fonts to use in the guestbook.</p>
<p><i><b>smilies</b></i> lets you specify what smiley faces (emoticons) to use in your guestbook, what code to use to make them appear and some text to say what they represent.</p>
<p><i><b>templates</b></i> lets you edit the templates used in Lazarus so long as you have set the templates files permissions to 777.</p>
<p><i><b>date / time</b></i> lets you specify what format to display the time and date in as well as the time difference between yourself and the server.</p>
<p><i><b>ad block</b></i> is where you can supply some text or html to appear on every page of your guestbook and even specify which position you want it to appear at.</p>
<p><b>INFO</b> is just that. Information.</p>
<p><b><i>include code</i></b> is the code you require to full integrate the guestbook into your web site. It also comes with instructions on how to use it.</p>
<p><b><i>php info()</i></b> is just information about your servers settings.</p>
</div>
</div>
<div class="contentholder">
<p> </p>
<div class="optionsTitle">Information</div>
<div class="adminSection">
<p><b>Lazarus Version:</b> <?php echo $this->VARS['laz_version']; ?></p>
<p><b>Unaccepted Entries Or Entries With Unaccepted Comments: </b><?php
$this->db->query('SELECT COUNT(DISTINCT a.ID) FROM '.LAZ_TABLE_PREFIX.'_data as a LEFT OUTER JOIN '.LAZ_TABLE_PREFIX.'_com as b ON a.ID=b.ID WHERE a.accepted = 0 or b.comaccepted = 0');
$theTotal = $this->db->fetch_array($this->db->result);
echo $theTotal['COUNT(DISTINCT a.ID)'].' (<a href="'.$this->SELF.'?action=show&amp;tbl=gb&amp;gbsession='.$this->gbsession.'&amp;unacc=true&amp;uid='.$this->uid.'">VIEW</a>)</p>';
?>
<p><b>Server:</b> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
<p><b>PHP Version:</b> <?php echo phpversion(); ?></p>
<p><b>MySQL Version:</b> <?php
$result = $this->db->query('SELECT VERSION()');
$mysqlver = $this->db->fetch_array($this->db->result);
echo $mysqlver['VERSION()'];
$this->db->free_result($this->db->result);
?>
</div>
</div>
</div>