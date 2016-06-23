<script type="text/javascript">
<!--
  showComs = '$LANG[BookMess12]';
  hideComs = '$LANG[BookMess13]';
// -->
</script>
<script type="text/javascript" src="$GB_PG[base_url]/lazjs.php?jspage=body"></script>
<script type="text/javascript" src="$GB_PG[base_url]/enlargeit.php"></script>
<script type="text/javascript" src="$GB_PG[base_url]/motionpackjs.php"></script>
<script type="text/javascript">
hs.graphicsDir = '$GB_PG[base_url]/img/hs/';
hs.outlineType = 'rounded-white';
</script>
<div class="lazTop" id="lazTop">
  <div style="padding: 3px;">
    <div style="text-align:right;float:right;">
      $TOPLINK
    </div>  
    <div>
      $VARS[book_name]
    </div>
  </div>
  <div style="clear: left; padding: 3px;">
    $LANG[BookMess6]
  </div>
  <div style="padding: 3px 3px 1px 3px">
    <div style="float:left;">
      <span><img src="$GB_PG[base_url]/img/point3.gif" width="9" height="9" alt="">$LANG[NavTotal]
         <span class="lazTopNum">$TPL[GB_TOTAL]</span> &nbsp; $LANG[NavRecords] <span class="lazTopNum">$VARS[entries_per_page]</span></span>
    </div>
    <div style="text-align:right;">
      $TPL[GB_NAVIGATION]
    </div>
  </div>
  <table border="0" cellspacing="1" cellpadding="5" align="center" width="100%" bgcolor="$VARS[tb_bg_color]" id="lazTable">
    $SEARCH 
    <tr style="background:$VARS[tb_hdr_color]; font-size:small; font-family:$VARS[font_face]; color:$VARS[tb_text]; font-weight: bold;">
      <td width="32%">$LANG[FormName]</td>
      <td width="68%">$LANG[BookMess7]</td>
    </tr>
<!--Start Guestbook Entries -->
$TPL[GB_ENTRIES]
<!--End Guestbook Entries -->
 </table>
 <script type="text/javascript">
 lazNode = document.getElementById('lazTable');
 var comDivs = getElementsByClass('comDiv',lazNode,'div');
 for(i=0; i < comDivs.length; i++){
   comDivs[i].style.display = 'none';
   comDivs[i].style.overflow = 'hidden';
 }

 var comHides = getElementsByClass('comHide',lazNode,'a');
 for(i=0; i < comHides.length; i++){
   comHides[i].style.display = '';
 }
</script>
  <div style="padding:5px;clear:both;">
    <div style="float:left;">
       <span class="font2"><a href="$GB_PG[base_url]/rss.php?entry=$entry"><img src="$GB_PG[base_url]/img/rss.png" border="0" width="80" height="15" alt="$LANG[BookMess14]" title="$LANG[BookMess14]"></a></span>
    </div>
    <div style="text-align: right;">
      <span class="font2">$TPL[GB_NAVIGATION]</span>
    </div>
  </div>
</div>