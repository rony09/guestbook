<?php
include $this->db->include_path.'/lang/english.php';
if (($this->VARS['lang'] != 'english') && file_exists($this->db->include_path.'/lang/'.$this->VARS['lang'].'.php')) {
	include $this->db->include_path.'/lang/'.$this->VARS['lang'].'.php';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.1 Transitional//EN">
<html>
<head>
<title>Guestbook - Edit Comment</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script> 
</head>
<body>
<?php 
$include_path = dirname(__FILE__);
include($include_path.'/menu.php'); 
?>
<div class="contenttable">
<form method="post" action="<?php echo $this->SELF; ?>">
  <table cellspacing="1" cellpadding="4" align="center">
    <tr>
      <td colspan="2" height="25" class="section">Edit the guestbook comment</td>
    </tr>
    <tr>
      <td class="lefttd"><?php echo $LANG['FormName']; ?>:</td>
      <td class="righttd2"><input type="text" name="name" size="44" maxlength="50" value="<?php echo $row['name']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><?php echo $LANG['FormEmail']; ?>:</td>
      <td class="righttd2"><input type="text" name="email" size="44" maxlength="50" value="<?php echo $row['email']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd" valign="top"><?php echo $LANG['EmailMess2']; ?>:</td>
      <td class="righttd2">
        <textarea name="comments" id="comments" cols="42" rows="10" wrap="VIRTUAL" class="input"><?PHP echo $row['comments']; ?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS["base_url"]; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('comments','up');" /><br /><img src="<?php echo $this->VARS["base_url"]; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('comments','down');" /></div>
      </td>
    </tr>
  </table>
	<br>
	<center>
        <input type="submit" class="submit" value="Save Changes">
        <input type="reset" class="reset" value="Reset"> 
        <input type="button" class="goback" value="Go Back" onclick="javascript:history.go(-1)">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo $row['com_id']; ?>">
        <input type="hidden" name="gbsession" value="<?php echo $this->gbsession; ?>">
        <input type="hidden" name="uid" value="<?php echo $this->uid; ?>">
        <input type="hidden" name="tbl" value="<?php echo $tbl; ?>">
        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
        </center>
</form>
</div>
