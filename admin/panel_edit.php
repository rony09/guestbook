<?php
include $this->db->include_path.'/lang/english.php';
if (($this->VARS['lang'] != 'english') && file_exists($this->db->include_path.'/lang/'.$this->VARS['lang'].'.php')) {
	include $this->db->include_path.'/lang/'.$this->VARS['lang'].'.php';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.1 Transitional//EN">
<html>
<head>
<title>Guestbook - Edit Entry</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script> 
</head>
<body>
<h4 align="center">G U E S T B O O K &nbsp; A D M I N</h4>
<?php 
$include_path = dirname(__FILE__);
include($include_path.'/menu.php'); 
?>
<div class="contenttable">
<form method="post" action="<?php echo $this->SELF; ?>">
  <table cellspacing="1" cellpadding="4" align="center">
    <tr>
      <td colspan="2" height="25"class="section">Edit the guestbook entry</td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/user.gif" alt=""> <?php echo $LANG['FormName']; ?>: </td>
      <td class="righttd2"><input type="text" name="name" size="44" maxlength="50" value="<?php echo $row['name']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/email.gif" alt=""> <?php echo $LANG['FormEmail']; ?>: </td>
      <td class="righttd2"><input type="text" name="email" size="44" maxlength="60" value="<?php echo $row['email']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/location.gif" alt=""> <?php echo $LANG['FormLoc']; ?>: </td>
      <td class="righttd2"><input type="text" name="location" size="44" maxlength="60" value="<?php echo $row['location']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/home.gif" alt=""> <?php echo $LANG['FormUrl']; ?>: </td>
      <td class="righttd2"><input type="text" name="url" size="44" maxlength="60" value="<?php echo $row['url']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/icq.gif" alt=""> ICQ: </td>
      <td class="righttd2"><input type="text" name="icq" size="44" maxlength="60" value="<?php if ($row['icq']!=0) {echo $row['icq'];} ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/aim.gif" alt=""> Aim: </td>
      <td class="righttd2"><input type="text" name="aim" size="44" maxlength="60" value="<?php echo $row['aim']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/ym.gif" alt=""> Yahoo: </td>
      <td class="righttd2"><input type="text" name="yahoo" size="44" maxlength="35" value="<?php echo $row['yahoo']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/msn.gif" alt=""> MSN: </td>
      <td class="righttd2"><input type="text" name="msn" size="44" maxlength="60" value="<?php echo $row['msn']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><img src="img/skype.gif" alt=""> Skype: </td>
      <td class="righttd2"><input type="text" name="skype" size="44" maxlength="35" value="<?php echo $row['skype']; ?>" class="input"></td>
    </tr>    		    
    <tr>
      <td class="lefttd"><?php echo $LANG['FormGender']; ?>: </td>
      <td class="righttd2"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><input type="radio" name="gender" value="m"<?php if ($row['gender']=='m') {echo ' checked';} ?>><?php echo $LANG['FormMale']; ?>
        <input type="radio" name="gender" value="f"<?php if ($row['gender']=='f') {echo ' checked';} ?>><?php echo $LANG['FormFemale']; ?>
				<input type="radio" name="gender" value="x"<?php if (!$row['gender'] || $row['gender']=='x') { echo ' checked'; }?>>Not Saying</td>
    </tr>
    <tr>
      <td class="lefttd">Host: </td>
      <td class="righttd2"><input type="text" name="host" size="44" maxlength="60" value="<?php echo $row['host']; ?>" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd">Browser: </td>
      <td class="righttd2"><input type="text" name="browser" size="44" maxlength="250" value="<?php echo $row['browser']; ?>" class="input"></td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"><?php echo $LANG['FormMessage']; ?>: </td>
      <td class="righttd2">
        <textarea name="comment" id="comment" cols="42" rows="10" wrap="VIRTUAL" class="input"><?PHP echo $row['comment']; ?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS["base_url"]; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('comment','up');" /><br /><img src="<?php echo $this->VARS["base_url"]; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('comment','down');" /></div>
      </td>
    </tr>
  </table>
  <br>
	<center>
        <input type="submit" class="submit" value="Save Changes">
        <input type="reset" class="reset" value="Reset">
        <input type="button" class="goback" value="Go Back" onclick="javascript:history.go(-1)">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <input type="hidden" name="gbsession" value="<?php echo $this->gbsession; ?>">
        <input type="hidden" name="uid" value="<?php echo $this->uid; ?>">
        <input type="hidden" name="tbl" value="<?php echo $tbl; ?>">
        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
	</center>
</form>
</div>
