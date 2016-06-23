<?php
include $this->db->include_path.'/lang/english.php';
if (($this->VARS['lang'] != 'english') && file_exists($this->db->include_path.'/lang/'.$this->VARS['lang'].'.php'))
{
  include $this->db->include_path.'/lang/'.$this->VARS['lang'].'.php';
}
$unacc = (isset($_GET['unacc'])) ? '&amp;unacc=true' : '';
$GB_PG['base_url'] = $this->VARS['base_url'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script>
<script language="JavaScript">
<!--
function CheckValue() {
  if(!(document.FormMain.record.value >= 1)) {
  alert('<?php echo $LANG['AdminJSInvalidRec']; ?>');
  document.FormMain.record.focus();
  return false;
  }
}
function CheckValue2() {
  if(!(document.FormMain2.record.value >= 1)) {
  alert('<?php echo $LANG['AdminJSInvalidRec']; ?>');
  document.FormMain2.record.focus();
  return false;
  }
}
function delete_post(AlertMessage) {
  if (confirm(AlertMessage)) {
  return true;
  }
  else {
  alert ('<?php echo $LANG['AdminJSNoAction']; ?>');
  return false;
  }
}
function doMultiple(AlertMessage) {
  if (confirm(AlertMessage)) {
  flag = 0;
  }
  else {
  flag = 1;
  alert ('<?php echo $LANG['AdminJSNoAction']; ?>');
  }
}

function toggleClass(objClass){
  if (getElementByClass(objClass).style.visibility == 'hidden'){
   showClass(objClass);
  } else {
   hideClass(objClass);
  }
}

function hideClass(objClass){
//  This function will hide Elements by object Class
//  Works with IE and Mozilla based browsers

var elements = document.getElementsByTagName('*');
  for (i=0; i<elements.length; i++){
   if (elements[i].className == objClass){
    elements[i].style.visibility = 'hidden';
   }
  }
}

function showClass(objClass){
//  This function will show Elements by object Class
//  Works with IE and Mozilla based browsers
var elements = document.getElementsByTagName('*');
  for (i=0; i<elements.length; i++){
   if (elements[i].className == objClass){
    elements[i].style.visibility = 'visible';
   }
  }
}

function hideLinks(id) {
  itm = document.getElementById(id);
  itm.style.visibility = 'hidden';
}
function showLinks(id) {
  itm = document.getElementById(id);
  itm.style.visibility = 'visible';
}

var checked = false;
function checkedAll () {
  var aa= document.getElementById('lazEntries'); 
  if (checked == false) {
    checked = true
  } else {
    checked = false
  }
  for (var i =0; i < aa.elements.length; i++) { 
    aa.elements[i].checked = checked;
  }
}

//-->
</script>
<script type="text/javascript" src="<?php echo $this->VARS['base_url']; ?>/enlargeit.php"></script>
<script type="text/javascript">
hs.graphicsDir = '<?php echo $this->VARS['base_url']; ?>/img/hs/';
hs.outlineType = 'rounded-white';
</script>
</head>
<body>
<?php
$include_path = dirname(__FILE__);
include($include_path.'/menu.php');
?>
<div class="contenttable">
<?php echo $amessage; ?>
<form method="post" action="<?php echo $this->SELF; ?>" name="FormMain" onsubmit="return CheckValue()">
  <table cellspacing="0" cellpadding="2" align="center" width="100%">
  <tr>
  <td class="section" style="border-width: 1px 0 0 1px; border-style: solid; border-color: #BBD8E7;">
   <input type="text" name="record" size="12">
   <input type="submit" value="Jump to record">
   <input type="hidden" name="action" value="show">
   <input type="hidden" name="gbsession" value="<?php echo $this->gbsession; ?>">
   <input type="hidden" name="uid" value="<?php echo $this->uid; ?>">
   <input type="hidden" name="tbl" value="<?php echo $tbl; ?>">
  </td>
  <td align="right" class="section" style="border-width: 1px 1px 0 0; border-style: solid; border-color: #BBD8E7;">&nbsp;
<?php
echo "<a href=\"$this->SELF?action=show&amp;tbl=$tbl&amp;entry=0&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\">Goto Top</a>\n";
if ($prev_page >= 0) {
 echo "  &nbsp;&nbsp;<a href=\"$this->SELF?action=show&amp;tbl=$tbl&amp;entry=$prev_page&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\">Previous Page</a>\n";
}
if ($next_page < $total) {
 echo "  &nbsp;&nbsp;<a href=\"$this->SELF?action=show&amp;tbl=$tbl&amp;entry=$next_page&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\">Next Page</a>\n";
}

?> </td>
  </tr>
  </table>
</form>
<form action="<?php echo $this->SELF; ?>" method="post" id="lazEntries">
<input type="hidden" name="gbsession" value="<?php echo $this->gbsession; ?>">
<input type="hidden" name="uid" value="<?php echo $this->uid; ?>">
<input type="hidden" name="tbl" value="<?php echo $tbl; ?>">
<input type="hidden" name="action" value="multi">
<input type="hidden" name="rid" value="<?php echo $entry; ?>">
<?php
if ($total >= 1)
{ ?>
  <table cellspacing="1" cellpadding="5" align="center" width="100%" style="background:#BBD8E7;">
  <tr style="background:#F9F9F9;color:#1C62AA;">
  <td width="30%"><font size="2"><b><?php echo $LANG['FormName']; ?></b></font></td>
  <td width="68%"><font size="2"><b><?php echo $LANG['BookMess7']; ?></b></font></td>
  <td width="1%">&nbsp;</td>
  <td width="1%"><input name="checkall" onclick="checkedAll();" type="checkbox" /></td>
  </tr>

<?php
}
$id = $total-$entry;
$i=0;
if ($total < 1)
{
  echo ('<h3>No entries found</h3>');
}
else
{
while ($row = $this->db->fetch_array($result)) {

$name = $row['name'];
$delpic = '';
$delpic2 = '';
$date = date("D, F j, Y H:i", $row['date']);
$comment = nl2br($row['comment']);
$bgcolor = ($i % 2) ? "#FFFFFF" : "#EDFBFC";
$bgcolor = ($row['accepted'] == 0) ? '#FFC0CB' : $bgcolor;
$i++;

if ($this->VARS["allow_gender"]==1) {
  if ($row['gender']=='f') {
  $theGender = '<img src="img/female.gif" alt="'.$LANG['FormFemale'].'" title="'.$LANG['FormFemale'].'">';
  }
  elseif ($row['gender']=='m')
  {
  $theGender =  '<img src="img/male.gif" alt="'.$LANG['FormMale'].'" title="'.$LANG['FormMale'].'">';
  }
  else
  {
  $theGender = '';
  }
}
else
{
  $theGender = '';
}
echo " <tr bgcolor=\"$bgcolor\">\n  <td width=\"30%\" valign=\"top\">
  <table cellspacing=\"0\" cellpadding=\"2\" style=\"background-color:$bgcolor;\">\n  <tr>
  <td style=\"text-align:left;width:50px;\"><font size=\"1\">$id)</font></td>
  <td><font size=\"2\"><b>$name</b></font>$theGender</td>\n  </tr>\n  <tr>\n";
if ($row['email']) {
  echo '  <td><img src="img/email.gif" alt="'.$LANG['FormEmail'].'" title="'.$LANG['FormEmail'].'"></td>
  <td><font size="1"><a href="mailto:'.$row['email'].'">'.$row['email']."</a></font></td>\n  </tr>\n";
}
if ($row['url']) {
  echo "  <tr>\n  <td><img src=\"img/home.gif\" alt=\"".$LANG['FormUrl']."\" title=\"".$LANG['FormUrl']."\"></td>
  <td><a href=\"$row[url]\" target=\"_blank\"><font size=\"1\">$row[url]</font></a></td>\n  </tr>\n";
}
if ($row['icq'] && $this->VARS["allow_icq"]==1) {
  echo "  <tr>\n  <td><img src=\"img/icq.gif\" alt=\"ICQ\" title=\"ICQ\"></td>
  <td><font size=\"1\">$row[icq]</font></td>\n  </tr>\n";
}
if ($row['aim'] && $this->VARS["allow_aim"]==1) {
  echo "  <tr>\n  <td><img src=\"img/aim.gif\" alt=\"AIM\" title=\"AIM\"></td>
  <td><font size=\"1\">$row[aim]</font></td>\n  </tr>\n";
}
if ($row['yahoo'] && $this->VARS["allow_yahoo"]==1) {
  echo "  <tr>\n  <td><img src=\"img/ym.gif\" alt=\"Yahoo\" title=\"Yahoo\"></td>
  <td><font size=\"1\">$row[yahoo]</font></td>\n  </tr>\n";
}
if ($row['msn'] && $this->VARS["allow_msn"]==1) {
  echo "  <tr>\n  <td><img src=\"img/msn.gif\" alt=\"MSN\" title=\"MSN\"></td>
  <td><font size=\"1\">$row[msn]</font></td>\n  </tr>\n";
}
if ($row['skype'] && $this->VARS["allow_skype"]==1) {
  echo "  <tr>\n  <td><img src=\"img/skype.gif\" alt=\"Skype\" title=\"Skype\"></td>
  <td><font size=\"1\">$row[skype]</font></td>\n  </tr>\n";
}
if ($row['location']) {
  echo "  <tr>\n  <td><img src=\"img/location.gif\" alt=\"".$LANG['FormLoc']."\" title=\"".$LANG['FormLoc']."\"></td>
  <td><font size=\"1\">$row[location]</font></td>\n  </tr>\n";
}
$hostname = (preg_match('/^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$/', $row['host'])) ? 'IP' : 'Host';
$their_ip = (preg_match('/^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$/', $row['host'])) ? $row['host'] : '';
$their_ip = ($row['ip'] != '') ? $row['ip'] : $their_ip;
$locate_ip = ($their_ip != '') ? ' <a href="http://www.geoiptool.com/en/?IP=' . $their_ip . '" target="_blank" title="' . $LANG['FormLoc'] . '"><img src="img/location.gif" alt="' . $LANG['FormLoc'] . '" height="10" width="10" /></a>' : '';
$resolve_ip = ($their_ip != '') ? " (<a href=\"http://whois.domaintools.com/".$their_ip."\" target=\"_blank\" title=\"Resolve\">".$their_ip."</a>$locate_ip)" : '';
$bandelete = ($their_ip != '') ? "<br>\n<a href=\"".$this->SELF."?action=banip&amp;ip=".$their_ip."&amp;tbl=".$tbl."&amp;rid=".$entry."&amp;gbsession=".$this->gbsession."&amp;uid=".$this->uid.$unacc."\" onclick=\"return delete_post('".$LANG['AdminJSBanIP']." ".$their_ip."')\">".$LANG['AdminBanIP']."</a> | <a href=\"".$this->SELF."?action=bandel&amp;ip=".$their_ip."&amp;tbl=".$tbl."&amp;id=".$row['id']."&amp;rid=".$entry."&amp;gbsession=".$this->gbsession."&amp;uid=".$this->uid.$unacc."\" onclick=\"return delete_post('".$LANG['AdminJSBanDel']." ".$their_ip."')\">".$LANG['AdminBanIPDel']."</a> | <a href=\"".$this->SELF."?action=banunaccept&amp;ip=".$their_ip."&amp;tbl=".$tbl."&amp;id=".$row['id']."&amp;rid=".$entry."&amp;gbsession=".$this->gbsession."&amp;uid=".$this->uid.$unacc."\">".$LANG['AdminBanIPUnaccept']."</a>" : '';
echo "  </table>\n  </td>\n  <td width=\"60%\" valign=\"top\"><font face=\"Arial\" size=\"1\"><b>".$date." ".$hostname.": ".$row['host'].$resolve_ip.$bandelete."</b></font>\n  <hr size=\"1\">
  <div style=\"font-size:12px;margin:0;\" onmouseover=\"showLinks('adminLinks_".$row['id']."');\" onmouseout=\"hideLinks('adminLinks_".$row['id']."');\">";
if ($row['p_filename'] && preg_match("/^img-/",$row['p_filename']))
{
  $new_img_size = $img->get_img_size_format($row['width'], $row['height']);
  $row['p_filename2'] = (file_exists('./public/t_'.$row['p_filename'])) ? 't_'.$row['p_filename'] : $row['p_filename'];
  //echo "<a href=\"javascript:gb_picture('$row[p_filename]',$row[width],$row[height])\"><img src=\"$GB_UPLOAD/$row[p_filename2]\" align=\"left\" border=\"0\" style=\"float:left;\" $new_img_size[2]></a>";
  echo '<a href="'.$GB_PG['base_url'].'/public/'.$row['p_filename'].'" target="_blank" onclick="return hs.expand(this)" class="highslide"><img src="'.$GB_PG['base_url'].'/public/'.$row['p_filename2'].'" style="float:left;margin-right:3px;" alt="('.$id.')" border="0" '.$new_img_size[2].'></a>';
  $fromtable = ($tbl == 'gb') ? 'pub' : 'priv';
  $delpic = "<br>\n<a href=\"$this->SELF?action=del&amp;tbl=".$fromtable."pics&amp;id=".$row['id']."&amp;rid=".$entry."&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\" onclick=\"return delete_post('".$LANG['AdminJSPicDelete']."')\"><img src=\"img/image_delete.gif\" alt=\"".$LANG['AdminPicDelete']."\" title=\"".$LANG['AdminPicDelete']."\"></a>";
  $delpic2 = " | <a href=\"$this->SELF?action=del&amp;tbl=".$fromtable."pics&amp;id=".$row['id']."&amp;rid=".$entry."&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\" onclick=\"return delete_post('".$LANG['AdminJSPicDelete']."')\" style=\"color:#D00;\"><img src=\"img/image_delete.gif\" alt=\"".$LANG['AdminPicDelete']."\" title=\"".$LANG['AdminPicDelete']."\"> ".$LANG['AdminPicDelete']."</a>";
}
if ($this->VARS['smilies'] == 1)
{
  echo $this->emotion($comment) . '<div id="adminLinks_' . $row['id'] . '" class="adminlinks">';
}
else
{
  echo $comment . '<div id="adminLinks_' . $row['id'] . '" class="adminlinks">';
}
if ($row['accepted'] == 0)
{
  echo '<a href="'.$this->SELF.'?action=accept&amp;tbl='.$tbl.'&amp;id='.$row['id'].'&amp;rid='.$entry.'&amp;gbsession='.$this->gbsession.'&amp;uid='.$this->uid . $unacc.'" style="color:#090;"><img src="img/flag_green.gif" alt="'.$LANG['AdminAccept'].'" title="'.$LANG['AdminAccept'].'"> '.$LANG['AdminAccept'].'</a>';
}
else
{
  echo '<a href="'.$this->SELF.'?action=unaccept&amp;tbl='.$tbl.'&amp;id='.$row['id'].'&amp;rid='.$entry.'&amp;gbsession='.$this->gbsession.'&amp;uid='.$this->uid . $unacc.'" style="color:#A70;"><img src="img/flag_red.gif" alt="'.$LANG['AdminUnaccept'].'" title="'.$LANG['AdminUnaccept'].'"> '.$LANG['AdminUnaccept'].'</a>';
}
echo " | <a href=\"$this->SELF?action=edit&amp;rid=$entry&amp;tbl=$tbl&amp;id=$row[id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/admin_edit.gif\" alt=\"".$LANG['AdminEdit']."\" title=\"".$LANG['AdminEdit']."\"> ".$LANG['AdminEdit']."</a>";
if (($this->VARS['disablecomments'] != 1) && ($tbl == 'gb'))
{
  echo " | <a href=\"comment.php?gb_id=$row[id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/comment.gif\" alt=\"".$LANG['AltCom']."\" title=\"".$LANG['AltCom']."\"> ".$LANG['AltCom']."</a>";
}
echo "$delpic2 | <a href=\"$this->SELF?action=del&amp;tbl=$tbl&amp;id=$row[id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\" onclick=\"return delete_post('".$LANG['AdminJSDelete']."')\" style=\"color:#D00;\"><img src=\"img/bin.gif\" alt=\"".$LANG['AdminDelete']."\" title=\"".$LANG['AdminDelete']."\"> ".$LANG['AdminDelete']."</a></div></div>\n";
if ($tbl == 'gb')
{
  $this->db->query("select * from ".LAZ_TABLE_PREFIX."_com where id='$row[id]' order by com_id asc");
  while ($com = $this->db->fetch_array($this->db->result))
  {
  $hostname = (preg_match('/^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$/', $com['host'])) ? "IP" : "Host";
  $their_comip = (preg_match('/^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$/', $com['host'])) ? $com['host'] : '';
  $their_comip = ($com['ip'] != '') ? $com['ip'] : $their_comip;
  $resolve_comip = ($their_comip != '') ? " (<a href=\"http://whois.domaintools.com/".$their_comip."\" target=\"_blank\" title=\"Resolve\">".$their_comip."</a>)" : '';
  $combandelete = ($their_ip != '') ? "<br>\n<a href=\"".$this->SELF."?action=banip&amp;ip=".$their_comip."&amp;rid=".$entry."&amp;tbl=com&amp;gbsession=".$this->gbsession."&amp;uid=".$this->uid.$unacc."\" onclick=\"return delete_post('".$LANG['AdminJSBanIP']." ".$their_comip."?')\">".$LANG['AdminBanIP']."</a> | <a href=\"".$this->SELF."?action=bandel&amp;ip=".$their_comip."&amp;tbl=com&amp;id=".$com['com_id']."&amp;rid=".$entry."&amp;gbsession=".$this->gbsession."&amp;uid=".$this->uid.$unacc."\" onclick=\"return delete_post('".$LANG['AdminJSBanDel']." ".$their_comip."')\">".$LANG['AdminBanIPDel']."</a> | <a href=\"".$this->SELF."?action=banunaccept&amp;ip=".$their_ip."&amp;tbl=com&amp;id=".$com['com_id']."&amp;rid=".$entry."&amp;gbsession=".$this->gbsession."&amp;uid=".$this->uid.$unacc."\">".$LANG['AdminBanIPUnaccept']."</a>" : '';
  $com["comments"] = nl2br($com["comments"]);
  $comEmail = (!empty($com['email'])) ? ' (<a href="mailto:' . $com['email'] . '">' . $com['email'] . '</a>)' : '';
  $combgcolor = ($com['comaccepted'] == 0) ? '#FFC0CB' : $bgcolor;
  echo "<table width=\"90%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" align=\"center\" style=\"clear:both;margin:3px auto 0 auto;background:#DFDFDF;\">\n";
  // echo "<tr bgcolor=\"$combgcolor\"><td colspan=\"3\"><hr size=\"1\"></td></tr>\n";
  echo "<tr bgcolor=\"$combgcolor\"><td valign=\"top\" colspan=\"3\"><b><font size=\"1\" face=\"Arial, Helvetica, sans-serif\">".date("D, F j, Y H:i",$com['timestamp'] += $this->VARS['offset']*3600)." $hostname: $com[host] $resolve_comip $combandelete</font></b></td>";
  echo "<tr bgcolor=\"$combgcolor\"><td valign=\"top\"><div style=\"font-size:12px;margin:0;\" onmouseover=\"showLinks('adminLinks_".$com['com_id']."');\" onmouseout=\"hideLinks('adminLinks_".$com['com_id']."');\">$com[name]$comEmail:<br />\n";
  echo $this->emotion($com['comments']) . '<div id="adminLinks_'.$com['com_id']."\" class=\"adminlinks\">\n";
  if ($com['comaccepted'] == 0)
  {
   echo "<a href=\"$this->SELF?action=accept&amp;tbl=com&amp;rid=$entry&amp;id=$com[com_id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"style=\"color:#090;\"><img src=\"img/flag_green.gif\" alt=\"".$LANG['AdminAccept']."\" title=\"".$LANG['AdminAccept']."\"> ".$LANG['AdminAccept']."</a>";
  }
  else
  {
   echo "<a href=\"$this->SELF?action=unaccept&amp;tbl=com&amp;rid=$entry&amp;id=$com[com_id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\" style=\"color:#A70;\"><img src=\"img/flag_red.gif\" alt=\"".$LANG['AdminUnaccept']."\" title=\"".$LANG['AdminUnaccept']."\"> ".$LANG['AdminUnaccept']."</a>";
  }
  echo " | <a href=\"$this->SELF?action=edit&amp;rid=$entry&amp;tbl=com&amp;id=$com[com_id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/admin_edit.gif\" alt=\"".$LANG['AdminEdit']."\" title=\"".$LANG['AdminEdit']."\"> ".$LANG['AdminEdit']."</a> | <a href=\"$this->SELF?action=del&amp;tbl=com&amp;id=$com[com_id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\" onclick=\"return delete_post('".$LANG['AdminJSDelete']."')\" style=\"color:#D00;\"><img src=\"img/bin.gif\" alt=\"".$LANG['AdminDelete']."\" title=\"".$LANG['AdminDelete']."\" style=\"color:#D00;\"> ".$LANG['AdminDelete']."</a>";
  echo "</div></div></td>";
  echo "<td align=\"right\" width=\"5\"><a href=\"$this->SELF?action=edit&amp;rid=$entry&amp;tbl=com&amp;id=$com[com_id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/admin_edit.gif\" alt=\"".$LANG['AdminEdit']."\" title=\"".$LANG['AdminEdit']."\"></a><br><a href=\"$this->SELF?action=del&amp;tbl=com&amp;id=$com[com_id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\" onclick=\"return delete_post('".$LANG['AdminJSDelete']."')\"><img src=\"img/bin.gif\" alt=\"".$LANG['AdminDelete']."\" title=\"".$LANG['AdminDelete']."\"></a>";
   if ($com['comaccepted'] == 0)
   {
    echo "<br><a href=\"$this->SELF?action=accept&amp;tbl=com&amp;rid=$entry&amp;id=$com[com_id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/flag_green.gif\" alt=\"".$LANG['AdminAccept']."\" title=\"".$LANG['AdminAccept']."\"></a><br>";
   }
   else
   {
    echo "<br><a href=\"$this->SELF?action=unaccept&amp;tbl=com&amp;rid=$entry&amp;id=$com[com_id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/flag_red.gif\" alt=\"".$LANG['AdminUnaccept']."\" title=\"".$LANG['AdminUnaccept']."\"></a><br>";
   }
   echo "</td>
   <td width=\"5\"><input type=\"checkbox\" name=\"comarray[$com[com_id]]\"></td>
   </tr></table>";
  }
}
echo "  </td>
  <td width=\"10%\" style=\"line-height: 18px;\"><font size=\"1\"><b><a href=\"$this->SELF?action=edit&amp;rid=$entry&amp;tbl=$tbl&amp;id=$row[id]&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/admin_edit.gif\" alt=\"".$LANG['AdminEdit']."\" title=\"".$LANG['AdminEdit']."\"></a><br>
  <a href=\"$this->SELF?action=del&amp;tbl=$tbl&amp;id=$row[id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\" onclick=\"return delete_post('".$LANG['AdminJSDelete']."')\"><img src=\"img/bin.gif\" alt=\"".$LANG['AdminDelete']."\" title=\"".$LANG['AdminDelete']."\"></a>$delpic";

if ($row['accepted'] == 0)
{
  echo "<br>\n<a href=\"$this->SELF?action=accept&amp;tbl=$tbl&amp;id=$row[id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/flag_green.gif\" alt=\"".$LANG['AdminAccept']."\" title=\"".$LANG['AdminAccept']."\"></a>";
}
else
{
  echo "<br>\n<a href=\"$this->SELF?action=unaccept&amp;tbl=$tbl&amp;id=$row[id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/flag_red.gif\" alt=\"".$LANG['AdminUnaccept']."\" title=\"".$LANG['AdminUnaccept']."\"></a>";
}
if (($this->VARS['disablecomments'] != 1) && ($tbl == 'gb'))
{
  echo "<br><a href=\"comment.php?gb_id=$row[id]&amp;rid=$entry&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\"><img src=\"img/comment.gif\" alt=\"".$LANG['AdminAccept']."\" title=\"".$LANG['AltCom']."\"></a><br>";
}
echo "</b></font></td>
<td><input type=\"checkbox\" name=\"entryarray[$row[id]]\"></td>
</tr>\n";
$id--;

}

?>
<tr>
<td colspan="4" align="right" bgcolor="#DDDDDD"><input type="submit" value="Delete Selected" name="multidelete" onClick="doMultiple('<?php echo $LANG['AdminJSMultiDelete']; ?>');if(flag==1) return false;">
 &nbsp; <input type="submit" value="Accept Selected" name="multiaccept" onClick="doMultiple('<?php echo $LANG['AdminJSMultiAccept']; ?>');if(flag==1) return false;">
  &nbsp; <input type="submit" value="Unaccept Selected" name="multiunaccept" onClick="doMultiple('<?php echo $LANG['AdminJSMultiUnaccept']; ?>');if(flag==1) return false;"></td>
</tr>
  </table>
  </form>
<?php } ?>
  <form method="post" action="<?php echo $this->SELF; ?>" name="FormMain2" onsubmit="return CheckValue2()">
  <table cellspacing="0" cellpadding="2" align="center" width="100%">
  <tr>
  <td class="section" style="border-width: 0 0 1px 1px; border-style: solid; border-color: #BBD8E7;">
   <input type="text" name="record" size="12">
   <input type="submit" value="Jump to record">
   <input type="hidden" name="action" value="show">
   <input type="hidden" name="gbsession" value="<?php echo $this->gbsession; ?>">
   <input type="hidden" name="uid" value="<?php echo $this->uid; ?>">
   <input type="hidden" name="tbl" value="<?php echo $tbl; ?>">
  </td>
  <td align="right" class="section" style="border-width: 0 1px 1px 0; border-style: solid; border-color: #BBD8E7;">&nbsp;
<?php
echo "<a href=\"$this->SELF?action=show&amp;tbl=$tbl&amp;entry=0&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\">Goto Top</a>\n";
if ($prev_page >= 0) {
 echo "  &nbsp;&nbsp;<a href=\"$this->SELF?action=show&amp;tbl=$tbl&amp;entry=$prev_page&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\">Previous Page</a>\n";
}
if ($next_page < $total) {
 echo "  &nbsp;&nbsp;<a href=\"$this->SELF?action=show&amp;tbl=$tbl&amp;entry=$next_page&amp;gbsession=$this->gbsession&amp;uid=$this->uid$unacc\">Next Page</a>\n";
}

?> </td>
  </tr>
  </table>
  </form>
</div>
<script type="text/javascript">
hideClass('adminlinks');
</script>