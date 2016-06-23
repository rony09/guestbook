<?php
include $this->db->include_path.'/lang/english.php';
if (($this->VARS['lang'] != 'english') && file_exists($this->db->include_path.'/lang/'.$this->VARS['lang'].'.php'))
{
  include $this->db->include_path.'/lang/'.$this->VARS['lang'].'.php';
}
$section = (!empty($_GET['section'])) ? $_GET['section'] : 'stats';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Guestbook - Info</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script>
<script type="text/javascript">
function reset_stats() {
  if (confirm('Reset block count to 0?')) {
  return true;
  }
  else {
    alert ('<?php echo $LANG['AdminJSNoAction']; ?>');
  return false;
  }
}
</script>
<style type="text/css">
<!--
.contentholder {  padding: 0; margin: 0;  border-width: 0 1px 1px 1px; border-style: solid; border-color: #BBD8E7; background-color: #FFF; }
.optionsTitle { color: #0A3E75; background-color: #EAF3FA; font-weight: bold; font-size: 20px; height: 26px;  margin: 0; padding: 0; text-align: center; border-width: 1px 0; border-style: solid; border-color: #BBD8E7; }
.adminSection { color: #000; background-color: #FFF; font-size: 12px; margin: 0; padding: 0 0 0 10px; }
.adminSection p { padding: 5px; margin: 0; }
.number { font-style: italic; color: #0A3E75; }
.percent { font-size: smaller; }
-->
</style>
</head>
<body>
<div class="contenttable">
<div class="contentholder">
<?php 
$include_path = dirname(__FILE__);
include($include_path.'/menu.php');
//Get current time
$mtime = microtime();
$mtime = explode(" ",$mtime);
$tstart = $mtime[1] + $mtime[0];
?>
<div class="optionsTitle">Guestbook Statistics</div>
<div class="adminSection">
<?php
$queryCount = 0;
$timeNow = time();
$last24hrs = $timeNow - 86400;
$last7days = $timeNow - 604800;
$last30days = $timeNow - 2592000;

$this->db->query("SELECT COUNT(CASE WHEN accepted='1' THEN '1' ELSE NULL END) AS accepted,
                         COUNT(CASE WHEN accepted='0' THEN '0' ELSE NULL END) AS unaccepted, 
                         COUNT(CASE WHEN gender='m' THEN 'm' ELSE NULL END) AS males, 
                         COUNT(CASE WHEN gender='f' THEN 'f' ELSE NULL END) AS females, 
                         COUNT(CASE WHEN gender='x' OR gender='' THEN 'x' ELSE NULL END) AS noGender,
                         COUNT(CASE WHEN email!='' THEN '' ELSE NULL END) as emails,
                         COUNT(CASE WHEN url!='' THEN '' ELSE NULL END) as homepages,
                         COUNT(CASE WHEN date>='$last24hrs' THEN 1 ELSE NULL END) as today,
                         COUNT(CASE WHEN date>='$last7days' THEN 1 ELSE NULL END) as week,
                         COUNT(CASE WHEN date>='$last30days' THEN 1 ELSE NULL END) as month,
                         COUNT(CASE WHEN browser LIKE '%Windows%' THEN 1 ELSE NULL END) as windows,
                         COUNT(CASE WHEN browser LIKE '%Macintosh%' THEN 1 ELSE NULL END) as mac,
                         COUNT(CASE WHEN browser LIKE '%Linux%' THEN 1 ELSE NULL END) as linux,
                         COUNT(CASE WHEN browser LIKE '%Firefox%' THEN 1 ELSE NULL END) as firefox,
                         COUNT(CASE WHEN browser LIKE '%MSIE%' THEN 1 ELSE NULL END) as msie,
                         COUNT(CASE WHEN browser LIKE '%Chrome%' THEN 1 ELSE NULL END) as chrome,
                         COUNT(CASE WHEN browser LIKE '%Opera%' THEN 1 ELSE NULL END) as opera
                         FROM " . LAZ_TABLE_PREFIX . "_data");
$foo = $this->db->fetch_array($this->db->result);
$queryCount++;
$acceptedPosts = $foo['accepted'];
$unacceptedPosts = $foo['unaccepted'];
$totalPosts = $acceptedPosts + $unacceptedPosts;
$males = $foo['males'];
$females = $foo['females'];
$noGender = $foo['noGender'];
$emails = $foo['emails'];
$homepages = $foo['homepages'];
$todaysPosts = $foo['today'];
$weeksPosts = $foo['week'];
$monthsPosts = $foo['month'];
$OSWindows = $foo['windows'];
$OSLinux = $foo['linux'];
$OSMac = $foo['mac'];
$iexplorer = $foo['msie'];
$firefox = $foo['firefox'];
$chrome = $foo['chrome'];
$opera = $foo['opera'];

// How many private entries

$this->db->query("SELECT COUNT(*) as total FROM " . LAZ_TABLE_PREFIX . "_private");
$foo = $this->db->fetch_array($this->db->result);
$queryCount++;
$totalPrivate = $foo['total']; 

// Now grab how many comments

$this->db->query("SELECT COUNT(CASE WHEN comaccepted='1' THEN '1' ELSE NULL END) AS acceptedcoms, 
                         COUNT(CASE WHEN comaccepted='0' THEN '0' ELSE NULL END) AS unacceptedcoms,
                         COUNT(case WHEN email!='' THEN '' ELSE NULL END) as comemails,
                         COUNT(CASE WHEN timestamp>='$last24hrs' THEN 1 ELSE NULL END) as today,
                         COUNT(CASE WHEN timestamp>='$last7days' THEN 1 ELSE NULL END) as week,
                         COUNT(CASE WHEN timestamp>='$last30days' THEN 1 ELSE NULL END) as month
                         FROM " . LAZ_TABLE_PREFIX . "_com");
$foo = $this->db->fetch_array($this->db->result);
$queryCount++;
$acceptedComs = $foo['acceptedcoms'];
$unacceptedComs = $foo['unacceptedcoms'];
$comemails = $foo['comemails'];
$totalComs = $acceptedComs + $unacceptedComs;
$todaysComPosts = $foo['today'];
$weeksComPosts = $foo['week'];
$monthsComPosts = $foo['month'];

// How many pictures uploaded

$this->db->query("SELECT COUNT(*) as total FROM " . LAZ_TABLE_PREFIX . "_pics");
$foo = $this->db->fetch_array($this->db->result);
$queryCount++;
$totalPics = $foo['total'];

// And finally get block count if enabled
if($this->VARS['count_blocks'] == 1)
{
  $this->db->query("SELECT block_count FROM " . LAZ_TABLE_PREFIX . "_config");
  $foo = $this->db->fetch_array($this->db->result);
  $queryCount++;
  $blockCount = $foo['block_count'];
}
else
{
  $blockCount = '<em>FUNCTION DISABLED</em>';
}

$baseFigure = ($totalPosts > 0) ? 100 / $totalPosts : 0;
$avgComs    = ($totalPosts > 0) ? round($totalComs / $totalPosts, 3) : 0;
$perComs    = ($totalComs  > 0) ? 100 / $totalComs : 0;
echo '<p><strong>Your guestbook has:</strong><br/>
<strong class="number">' . $totalPosts . '</strong> public entries (<strong class="number">' . $acceptedPosts . '</strong> accepted entries and <strong class="number">' . $unacceptedPosts . '</strong> unaccepted entries).<br />
<strong class="number">' . $totalPrivate . '</strong> private entries.<br />
<strong class="number">' . $totalComs . '</strong> comments (<strong class="number">' . $acceptedComs . '</strong> accepted comments and <strong class="number">' . $unacceptedComs . '</strong> unaccepted comments).</p>
<p><strong>Average number of comments per public entry:</strong> <strong class="number">' . $avgComs . '</strong></p>
<p><strong>Entries and comments made in the last:</strong>:<br />
24 hours: <strong class="number">' . $todaysPosts . '</strong> entries and <strong class="number">' . $todaysComPosts . '</strong> comments.<br />
7 Days: &nbsp; <strong class="number">' . $weeksPosts . '</strong> entries and <strong class="number">' . $weeksComPosts . '</strong> comments.<br />
30 Days: <strong class="number">' . $monthsPosts . '</strong> entries and <strong class="number">' . $monthsComPosts . '</strong> comments.</p>
<p><strong>Other Stats</strong><br/>
Entries with an email address: <strong class="number">' . $emails . '</strong> <em class="percent">(' . round(($baseFigure * $emails),2) . '%)</em><br />
Comments with an email address: <strong class="number">' . $comemails . '</strong> <em class="percent">(' . round(($perComs * $comemails),2) . '%)</em><br />
Entries with a homepage: <strong class="number">' . $homepages . '</strong> <em class="percent">(' . round(($baseFigure * $homepages),2) . '%)</em><br />
Entries with an image attached: <strong class="number">' . $totalPics . '</strong> <em class="percent">(' . round(($baseFigure * $totalPics),2) . '%)</em></p>
<h3>Breakdown of entries by...</h3>
<p><strong>Gender:</strong><br />
Males: <strong class="number">' . $males . '</strong> <em class="percent">(' . round(($baseFigure * $males),2) . '%)</em><br />
Females: <strong class="number">' . $females . '</strong> <em class="percent">(' . round(($baseFigure * $females),2) . '%)</em><br />
Not Specified: <strong class="number">' . $noGender . '</strong> <em class="percent">(' . round(($baseFigure * $noGender),2) . '%)</em></p>
<p><strong>Operating System (rough):</strong><br />
Windows: <strong class="number">' . $OSWindows . '</strong> <em class="percent">(' . round(($baseFigure * $OSWindows),2) . '%)</em><br />
Macintosh (Apple): <strong class="number">' . $OSMac . '</strong> <em class="percent">(' . round(($baseFigure * $OSMac),2) . '%)</em><br />
Linux: <strong class="number">' . $OSLinux . '</strong> <em class="percent">(' . round(($baseFigure * $OSLinux),2) . '%)</em><br />
Other: <strong class="number">' . ($totalPosts - ($OSWindows + $OSMac + $OSLinux)) . '</strong> <em class="percent">(' . round(($baseFigure * ($totalPosts - ($OSWindows + $OSMac + $OSLinux))),2) . '%)</em></p>
<p><strong>Web browser (rough):</strong><br />
Internet Explorer: <strong class="number">' . $iexplorer . '</strong> <em class="percent">(' . round(($baseFigure * $iexplorer),2) . '%)</em><br />
Firefox: <strong class="number">' . $firefox . '</strong> <em class="percent">(' . round(($baseFigure * $firefox),2) . '%)</em><br />
Chrome: <strong class="number">' . $chrome . '</strong> <em class="percent">(' . round(($baseFigure * $chrome),2) . '%)</em><br />
Opera: <strong class="number">' . $opera . '</strong> <em class="percent">(' . round(($baseFigure * $opera),2) . '%)</em><br />
Other: <strong class="number">' . ($totalPosts - ($iexplorer + $firefox + $chrome + $opera)) . '</strong> <em class="percent">(' . round(($baseFigure * ($totalPosts - ($iexplorer + $firefox + $chrome + $opera))),2) . '%)</em></p>
<h3 style="margin-bottom:2px; padding-bottom:0;">ENTRIES AND COMMENTS BLOCKED</h3>
<p style="font-size: smaller; font-style: italic;">All available anti spam techniques are counted even if you do not have them enabled.</p>
';
if($this->VARS['count_blocks'] == 1)
{
  if((strpos($blockCount, 'a:11:') !== 0) || isset($_GET['resetBlock']) || empty($blockCount))
  {
    $blockCount = 'a:11:{i:-1;i:' . time() . ';i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;}';
    $this->db->query('UPDATE ' . LAZ_TABLE_PREFIX . '_config SET block_count="' . $blockCount . '"');
    $queryCount++;
  }
  
  $blockTypes = array(
   -1 => 'count started',
    0 => 'Total number blocked',
    1 => 'Filled in the honeypot',
    2 => 'You have banned their IP',
    3 => 'Did not fill in the anti bot test',
    4 => 'Failed the anti bot test',
    5 => 'Sent no timehash',
    6 => 'Failed the header checks',
    7 => 'Posted a word you have blocked',
    8 => 'Posted more URLs than you allow',
    9 => 'Stop Forum Spam thinks they are a spammer'
  );
  
  $stats = @unserialize($blockCount);
  $totalBlocks = "<tr>\n<td><hr /></td>\n<td>&nbsp;</td>\n</tr>
  <tr>\n<td><strong>" . $stats[0] . '</strong></td><td> ' . $blockTypes[0] . "</td>\n</tr>\n";
  $countSince = "<tr>\n<td colspan=\"2\" style=\"font-size:small;font-style:italic;\"><br />" . $blockTypes[-1] . ' ' . date("jS F Y \a\\t G:i:s", $stats[-1]) . "</td>\n</tr>\n";
  unset($stats[0]);
  unset($stats[-1]);
  arsort($stats);
  echo '<table cellpadding="1" cellspacing="0" class="error" style="background:transparent;width: auto;text-align: left;">';
  //for($i = 1; $i <= 9; $i++)
  foreach($stats as $key => $value)
  {
    echo "<tr>\n<td style=\"text-align: right;padding-right:10px;\"><strong>" . $value . '</strong></td><td style=\"padding-right:5px;\"> ' . $blockTypes[$key] . "</td>\n</tr>\n";
  }
  echo $totalBlocks . $countSince;
  echo '</table><br />';

  
  echo '<p><span style="margin: 0 0 20px 10px;">(<a href="' . $this->SELF . '?action=info&amp;gbsession=' . $this->gbsession .'&amp;uid=' . $this->uid . '&amp;resetBlock=true" style="color:#000;" onclick="return reset_stats();">click here to reset block count to 0</a>)</span></p>
  ';
}


//Get current time as we did at start
$mtime = microtime();
$mtime = explode(' ',$mtime);
echo '<p style="font-size:smaller;font-style:italic;margin-top:10px;">Stats were generated in ' . round((($mtime[1] + $mtime[0]) - $tstart), 3) . ' seconds using ' . $queryCount . ' database queries.</p>'; 
?>
</div>
</div>
</div>
</div>
