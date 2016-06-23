<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Guestbook - Password Settings</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<style type="text/css">
table { background: #BBD8E7; }
</style>
<script type="text/javascript" src="lazjs.php?jspage=admin"></script> 
<script language="JavaScript">
<!--
function checkForm() {
  if (document.FormPwd.NEWadmin_pass.value != document.FormPwd.confirm.value) {
    alert("The passwords do not match!");
    return false;
  }
}
//-->
</script>
</head>
<body>
<?php 
$include_path = dirname(__FILE__);
include($include_path.'/menu.php'); 
?>
<div class="contenttable">
<form action="<?php echo $this->SELF; ?>" name="FormPwd" method="post">
  <table>
    <tr>
      <td colspan="2" height="25" class="section">Guestbook Username/Password</td>
    </tr>
    <tr>
      <td colspan="2" class="subsection">Below you can change the username and/or password for the guestbook admin.</td>
    </tr>
    <tr>
      <td class="lefttd"><b>Your UserName</b><br>
      <small>Leave this alone unless you want to change your username.</small></td>
      <td class="righttd"><input type="text" name="NEWadmin_name" value="<?php echo $row["username"]; ?>" size="30" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"> <b>Enter New Password</b></td>
      <td class="righttd"><input type="password" name="NEWadmin_pass" size="30" class="input"></td>
    </tr>
    <tr>
      <td class="lefttd"><b>Confirm New Password</b></td>
      <td class="righttd">
        <input type="password" name="confirm" size="30" class="input">
        <input type="hidden" value="password" name="panel">
      </td>
    </tr>
  </table>
  <br>
  <table>
    <tr>
      <td colspan="2" height="25" class="section">Database Settings</td>
    </tr>
    <tr>
      <td colspan="2" class="subsection">Below are database settings for your mySQL database.</td>
    </tr>
    <tr>
      <td class="lefttd"><b>Database Name</b></td>
      <td class="righttd"><b><?php echo $this->db->db['dbName']; ?></b></td>
    </tr>
    <tr>
      <td class="lefttd"><b>MySQL Hostname</b><br>
        <small>Default is 'localhost'.</small></td>
      <td class="righttd"><b><?php echo $this->db->db['host']; ?></b></td>
    </tr>
    <tr>
      <td class="lefttd"> <b>MySQL Username</b><br>
        <small>Your mySQL username for the database.</small></td>
      <td class="righttd"><b><?php echo $this->db->db['user']; ?></b></td>
    </tr>
  </table>
  <br>
  <center>
    <input type="submit" class="submit" value="Submit Settings" onclick="return checkForm()">
    <input type="reset" class="reset" value="Reset">
    <input type="hidden" value="<?php echo $this->uid; ?>" name="uid">
    <input type="hidden" value="<?php echo $this->gbsession; ?>" name="gbsession">
    <input type="hidden" value="password" name="panel">
    <input type="hidden" value="save" name="action">
  </center>
</form>
</div>
