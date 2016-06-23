<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Guestbook - Smilies</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script> 
</head>
<body>
<h4 align="center">G U E S T B O O K &nbsp; S M I L I E S</h4>
<?php 
$include_path = dirname(__FILE__);
include($include_path.'/menu.php'); 
?>
<div class="contenttable">
<form action="<?php echo $this->SELF; ?>" name="FormMain" method="post">
  <table cellspacing="1" cellpadding="7" align="center" style="background: #BBD8E7;">
        <tr> 
            <td colspan="6" align="center" height="25" class="section">Smilies</td>
        </tr>
        <tr> 
            <td height="25" class="subsection"><b>Smilie</b></td>
            <td class="subsection"><b>Filename</b></td>
            <td class="subsection"><b>Code</b></td>
            <td class="subsection"><b>Alt Text</b></td>
            <td colspan="2" class="subsection"><b>Action</b></td>
          </tr>
<?php
if (isset($smilie_data)) {
    echo "
          <tr class=\"lefttd\"> 
            <td><img src=\"img/smilies/$smilie_data[s_filename]\" width=\"$smilie_data[width]\" height=\"$smilie_data[height]\"></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">$smilie_data[s_filename]</font></td>
            <td><input type=\"text\" name=\"s_code\" value=\"".htmlspecialchars($smilie_data['s_code'])."\" size=\"15\"></td>
            <td><input type=\"text\" name=\"s_emotion\" value=\"".htmlspecialchars($smilie_data['s_emotion'])."\" size=\"25\"><input type=\"hidden\" name=\"edit_smilie\" value=\"$smilie_data[id]\"></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>\n";
} else {
    $this->db->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_smilies ORDER BY s_filename ASC");
    while ($this->db->fetch_array($this->db->result)) {
        echo "
          <tr class=\"lefttd\"> 
            <td><img src=\"img/smilies/".$this->db->record['s_filename']."\" width=\"".$this->db->record['width']."\" height=\"".$this->db->record['height']."\"></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".htmlspecialchars($this->db->record['s_filename'])."</font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".htmlspecialchars($this->db->record['s_code'])."</font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".htmlspecialchars($this->db->record['s_emotion'])."</font></td>
            <td class=\"righttd\"><a href=\"$this->SELF?action=smilies&amp;gbsession=$this->gbsession&amp;uid=$this->uid&amp;edit_smilie=".$this->db->record['id']."\">edit</a></td>
            <td class=\"righttd\"><a href=\"$this->SELF?action=smilies&amp;gbsession=$this->gbsession&amp;uid=$this->uid&amp;del_smilie=".$this->db->record['id']."\">delete</a></td>
          </tr>\n";
    }
}
if (isset($smilie_list)) {
reset($smilie_list);
    foreach ($smilie_list as $key => $value)
    {
        echo "
          <tr bgcolor=\"#f7f7f7\"> 
            <td>$value</td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">$key</font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><input type=\"text\" name=\"new_smilie[$key]\" size=\"15\"></font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><input type=\"text\" name=\"new_emotion[$key]\" size=\"25\"></font></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>\n";
    }
}
?>
        </table>
        <div align="center"><br>
          <font face="Verdana, Arial, Helvetica, sans-serif" size="2">You can add more smileys by simply uploading them to the <i>smilies</i> directory located in the <i>img</i> directory then clicking the link below.<br>
          <b><a href="<?php echo $this->SELF; ?>?action=smilies&amp;gbsession=<?php echo $this->gbsession; ?>&amp;uid=<?php echo $this->uid; ?>&amp;scan_dir=1">Scan directory (img/smilies)</a></b><br><br>
          </font></div>
  <br>
  <center>
    <input type="submit" class="submit" value="Submit Settings">
    <input type="reset" class="reset" value="Reset">
    <input type="hidden" name="uid" value="<?php echo $this->uid; ?>">
    <input type="hidden" name="gbsession" value="<?php echo $this->gbsession; ?>">
    <input type="hidden" name="action" value="smilies">
    <input type="hidden" name="add_smilies" value="1">
  </center>
</form>
</div>
