<?php
include ("../admin/config.inc.php");
include ("../lib/mysql.class.php");

define('LAZ_TABLE_PREFIX', $table_prefix);

$smilies = new gbook_sql();
$smilies->connect();
$smilies->query("SELECT * FROM ".LAZ_TABLE_PREFIX.'_smilies');
$i=0;
while ($smilies->fetch_array($smilies->result)) {
    $bgcolor = ($i % 2) ? "#E1E1E1" : "#F7F7F7";
    echo "
    <tr>
      <td bgcolor=\"$bgcolor\"><b>".$smilies->record['s_code']." </b></td>
      <td bgcolor=\"$bgcolor\"><a href=\"javascript:emoticon('".$smilies->record['s_code']."')\"><img alt=\"".$smilies->record['s_code']."\" title=\"".$smilies->record['s_code']."\" border=\"0\" src=\"../img/smilies/".$smilies->record['s_filename']."\" width=\"".$smilies->record['width']."\" height=\"".$smilies->record['height']."\"></a></td>
      <td bgcolor=\"$bgcolor\">".htmlspecialchars($smilies->record['s_emotion'])."</td>
    </tr>\n";
    $i++;
}
?>