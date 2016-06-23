<?php
ob_start();
/////////////////////////////////////////////
//
// CAPTCHA generator v2.04
// Last modified: Wed, 17 February 2016 20:06:23 GMT
//
/////////////////////////////////////////////

/////////////////////////////////////////////
//
// Error Codes
//
// 1 = No hash received
// 2 = Font directory does not exist or is not readable
// 3 = No font files loaded (freetype)
// 4 = No font files loaded (not freetype)
// 5 = Failed header test
//
/////////////////////////////////////////////

// Lets grab the database username to use as a key
//LAZ_INCLUDE_PATH = dirname(__FILE__);
define('LAZ_INCLUDE_PATH', dirname(__FILE__));
require_once LAZ_INCLUDE_PATH.'/admin/version.php';
require_once LAZ_INCLUDE_PATH.'/admin/config.inc.php';
require_once LAZ_INCLUDE_PATH.'/lib/' . $DB_CLASS;
require_once LAZ_INCLUDE_PATH.'/lib/vars.class.php';
require_once LAZ_INCLUDE_PATH.'/lib/template.class.php';

$number_of_chars = 5;  // DO NOT TOUCH THIS!!!!

define('LAZ_TABLE_PREFIX', $table_prefix);

$db = new guestbook_vars(LAZ_INCLUDE_PATH);
$db->getVars();

$usecolor = ($db->VARS['captcha_grey']) ? 0 : 1;

$image_height = (!empty($db->VARS['captcha_height']) && is_int(intval($db->VARS['captcha_height']))) ? $db->VARS['captcha_height'] : 100;
$image_width = (!empty($db->VARS['captcha_width']) && is_int(intval($db->VARS['captcha_width']))) ? $db->VARS['captcha_width'] : 350;

$thekey = (!empty($GB_DB['user'])) ? $GB_DB['user'] : 'Lazarus';

// create an image with width 120px, height 20px
$image = imagecreatetruecolor($image_width, $image_height);

// Assign a background colour
$background = imagecolorallocate($image, 255,255,255);

// Make background transparent if desired
if ($db->VARS['captcha_trans'] == 1)
{
  imagecolortransparent($image, $background);
}

// Fill it in with the background colour
imagefill($image, 0, 0, $background);

// If we want greyscale make it all the same random grey
$grey = mt_rand(120, 140);

// Have we got a timehash to work with?

$hash = (!empty($_GET['hash'])) ? $_GET['hash'] : '';

function display_error($error_number)
{
  global $image;
  $red = imagecolorallocate($image, 255, 0, 0);
  imagestring($image, 5, 5, 8, 'ERROR! ('.$error_number.')', $red);
  if($error_number == 1)
  {
    imagestring($image, 5, 5, 35, 'Is JavaScript enabled?', $red);
  }
  // Do our headers
  header('Cache-Control: no-cache');
  header('Pragma: no-cache');
  header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
  if (function_exists('imagepng'))
  {
    header('Content-type: image/png');
    // Dump the image
    imagepng($image);
    echo trim(ob_get_clean());
  }
  else
  {
    header('Content-type: image/jpeg');
    // Dump the image
    imagejpeg($image, '', 100);
    echo trim(ob_get_clean());
  }
  // Tidy up
  imagedestroy($image); 
  exit();  
}

// OK lets run some tests
/*if($db->VARS['check_headers'] == 1)
{
  if(($failedHeader = $db->check_headers(3)) != 0)
  {
    display_error('5.'.$failedHeader);
  }
}*/

if (!empty($hash) && is_numeric($hash))
{

  // generate some random stuff for the text
  $realcode = strtoupper(md5(time()));

  // Better make sure they have freetype installed
  $freetype = (function_exists('imagettftext')) ? true : false;
  
  $fontDir = LAZ_INCLUDE_PATH.'/fonts/';
  
  if (is_dir($fontDir) && is_readable($fontDir))
  {
    // Set our fonts
    if ($freetype)
    {
      $handle = opendir($fontDir);
      $font_count = 0;
      while (($file = readdir($handle)) !== false)
      {
        if (preg_match("/\.ttf/i", $file) && is_readable($fontDir.$file)) 
        {
          $font[] = ($fontDir.'/'.$file);
          $font_count++;
        }
      }
      closedir($handle);
      if ($font_count == 0)
      {
        display_error(3);
      }        
    }
    else
    {
      $handle = opendir($fontDir);
      $font_count = 0;
      while ($file = readdir($handle))
      {
        if (preg_match("/\.gdf/i", $file) && is_readable($fontDir.$file))
        {
          $font[] = imageloadfont($fontDir.'/'.$file);
          $font_count++;
        }
      }
      closedir($handle);
      if ($font_count == 0)
      {
        display_error(4);
      }          
    }
  }
  else
  {
    display_error(2);   
  }

  // Do we want lines?
  if ($db->VARS['captcha_grid'])
  {
    // Generate the lines then
    for ($i = 0; $i < 3; $i++)
    {
      $red = ($usecolor) ? mt_rand(40, 140) : $grey;
      $green = ($usecolor) ? mt_rand(40, 140) : $grey;
      $blue = ($usecolor) ? mt_rand(40, 140) : $grey;
      $y1 = mt_rand(3,$image_height-7);
      $y2 = mt_rand(3,$image_height-7);
      $x1 = mt_rand(0, $image_width/10) + $i * $image_width/3;
      $x2 = $x1 + mt_rand(0, $image_width/10) + $image_width/3;
      if ($x2 > 197)
      {
        $x2 = 197;
      }
      
      // Add some anti-aliasing above the line.
      if ($freetype)
      {
        $line_color = imagecolorallocate($image, round($red * 1.5), round($green * 1.5), round($blue * 1.5));
        if (rand(0,1))
        {
          imageline($image, $x1, $y1, $x2, $y2, $line_color);
        }
        imageline($image, $x1, $y1 + 1, $x2, $y2 + 1, $line_color);
      }
      
      for ($j = 2; $j < $image_width / 60 + 1; $j++) // thickness 3, 5, 7 pixels.
      {
        $line_color = imagecolorallocate($image, $red, $green, $blue);
        imageline($image, $x1, $y1 + $j, $x2, $y2 + $j, $line_color);
      }
      
      // Add some anti-aliasing below the line.
      if($freetype)
      {
        $line_color = imagecolorallocate($image, round($red * 1.5), round($green * 1.5), round($blue * 1.5));
        imageline($image, $x1, $y1 + $j, $x2, $y2 + $j, $line_color);
        if (rand(0,1))
        {
          imageline($image, $x1, $y1 + $j + 1, $x2, $y2 + $j + 1, $line_color);
        }
      }
    }
    // Make the lines wavy
    $wave = rand(3,5); // wave strength
    $wave_width = rand(8,15); // wave width
    for ($i = 0; $i < 200; $i += 2)
    {
      imagecopy($image, $image, $i - 2, sin($i / $wave_width) * $wave, $i, 0, 2, 40);
    } 
  }

  // Here we add our code to the image
  // First create our code
  $realcode = '';
  $realcode = md5($thekey) . md5($hash);
  $realcode = strtoupper(md5($realcode));
  // Just an array for turning numbers into letters
  $captchanum = array(0 => 'V', 1 => 'H', 2 => 'K', 3 => 'M', 4 => 'P', 5 => 'S', 6 => 'T', 7 => 'W', 8 => 'X', 9 => 'Z');
  $xpos = mt_rand(5, 20); // set a random horizontal starting position
  $char_height = (min(2/3 * $image_width / ($number_of_chars + 1) , $image_height / 2)) - 2;
  $cur_x = - $char_height/3;
  for ($i = 0;$i <= 30;$i += 7)
  {
    $red = ($usecolor) ? mt_rand(40, 140) : $grey;
    $green = ($usecolor) ? mt_rand(40, 140) : $grey;
    $blue = ($usecolor) ? mt_rand(40, 140) : $grey;
    $thecolor = imagecolorallocate($image, $red, $green, $blue);
    $realcode[$i] = (is_numeric($realcode[$i])) ? $captchanum[$realcode[$i]] : $realcode[$i];
    if ($freetype)
    {
      $cur_x += $image_width / ($number_of_chars + 1);
      $font_height = $char_height * (1 + rand(0,3) / 10 - 0.1 );
      imagettftext($image, $font_height, rand(-200, 200) / 10, $cur_x, mt_rand(($font_height + ($font_height / 3) ), $image_height - 3), $thecolor, $font[array_rand($font)], $realcode[$i]);
    }
    else
    {
      $fontface = mt_rand(0,(count($font)-1));
      $vertpos = mt_rand(0, ($image_height - imagefontheight($font[$fontface]) - 5));
      $cur_x += $image_width / ($number_of_chars + 1);
      imagechar($image, $font[$fontface], $cur_x,  $vertpos, $realcode[$i], $thecolor);
    }
  
  }
  
  // Here we add the waves to the text and make lines extra wavy
  $wave = rand(3,5); // wave strength
  $wave_width = rand(8,15); // wave width
  for ($i = 0; $i < 200; $i += 2)
  {
    imagecopy($image, $image, $i - 2, sin($i / $wave_width) * $wave, $i, 0, 2, 40);
  }
  
  // Do we want the noise? I don't think I will make this an option
  if ($db->VARS['captcha_noise'])
  {
    for ($i = 1; $i <=  $image_height - 1; $i++)
    {
      for($j = 1; $j <= 30; $j++)
      {
        $red = ($usecolor) ? mt_rand(40, 140) : $grey;
        $green = ($usecolor) ? mt_rand(40, 140) : $grey;
        $blue = ($usecolor) ? mt_rand(40, 140) : $grey;
        $thecolor = imagecolorallocate($image, $red, $green, $blue);
        imagesetpixel($image, mt_rand(1, $image_width - 1), $i, $thecolor);
      }
    }
  }
}
else // We've not got a hash so report an error
{
  display_error(1);
} 

// Do our headers
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");

if(function_exists('imagegif'))
{
  header('Content-type: image/gif');
  imagegif($image);
}
elseif(function_exists('imagepng'))
{
  header('Content-type: image/png');
  imagepng($image);
}
else
{
  header('Content-type: image/jpeg');
  imagejpeg($image);
}

echo trim(ob_get_clean());

// Tidy up
imagedestroy($image);
?>