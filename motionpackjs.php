<?php ob_start('ob_gzhandler'); ?>
var timerlen = 5;
var slideAniLen = 300;

var timerID = new Array();
var startTime = new Array();
var obj = new Array();
var endHeight = new Array();
var moving = new Array();
var dir = new Array();
var noComs = new Array();

function slidedown(objname) {
  if (moving[objname]) return; // It's currently sliding

  theElement = (noComs[objname]) ? 'com' + objname + '_open' : objname;
  if (document.getElementById(theElement).style.display != 'none') return; // cannot slide down something that is already visible

  moving[objname] = true;
  dir[objname] = 'down';
  startslide(objname);
}

function slideup(objname) {
  if (moving[objname]) return; // It's currently sliding

  theElement = (noComs[objname]) ? 'com' + objname + '_open' : objname;
  if (document.getElementById(theElement).style.display == 'none') return; // cannot slide up something that is already hidden

  moving[objname] = true;
  dir[objname] = 'up';
  startslide(objname);
}

function startslide(objname) {
  theElement = (noComs[objname]) ? 'com' + objname + '_open' : objname;
  obj[objname] = document.getElementById(theElement);

  startTime[objname] = (new Date()).getTime();

  if (dir[objname] == 'down') {
    obj[objname].style.height = '1px';
  }

  obj[objname].style.display = 'block';
  endHeight[objname] = parseInt(obj[objname].scrollHeight);
  if(endHeight[objname] > 150) {
    endHeight[objname] = 150;
    obj[objname].style.overflow = 'auto';
  }

  timerID[objname] = setInterval('slidetick(\'' + objname + '\');', timerlen);
}

function slidetick(objname) {
  var elapsed = (new Date()).getTime() - startTime[objname];

  if (elapsed > slideAniLen) endSlide(objname)
  else {
    var d = Math.round(elapsed / slideAniLen * endHeight[objname]);
    if (dir[objname] == 'up') d = endHeight[objname] - d;

    obj[objname].style.height = d + 'px';
  }

  return;
}

function endSlide(objname) {
  clearInterval(timerID[objname]);

  if (dir[objname] == 'up') obj[objname].style.display = 'none';

  obj[objname].style.height = endHeight[objname] + 'px';

  delete(moving[objname]);
  delete(timerID[objname]);
  delete(startTime[objname]);
  delete(endHeight[objname]);
  delete(obj[objname]);
  delete(dir[objname]);
  if(noComs[objname])
  {
    if (document.getElementById('com' + objname + '_open').style.display == 'none') {
      document.getElementById('comtext_' + objname).innerHTML = showComs + ' (' + noComs[objname] + ')';
    } else {
      document.getElementById('comtext_' + objname).innerHTML = hideComs + ' (' + noComs[objname] + ')';
    }
  }

  return;
}

function toggleSlide(objname, coms) {
  theElement = (coms) ? 'com' + objname + '_open' : objname;
  noComs[objname] = coms;
  if (document.getElementById(theElement).style.display == 'none') {
    // div is hidden, so let's slide down
    slidedown(objname);
  } else {
    // div is not hidden, so slide up
    slideup(objname);
  }
}
<?php ob_end_flush(); ?>