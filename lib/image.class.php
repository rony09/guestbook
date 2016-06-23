<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 20:03:44 GMT
 * ----------------------------------------------
 */

class gb_image {

  var $imagick = '';  /*
            * Windows: c:/imagemagick/convert.exe
            */
  var $max_width;
  var $max_height;
  var $min_filesize = 10240;
  var $destdir = '.';
  var $prefix = '';
  var $supported_types = '';

  function __CONSTRUCT() 
  {
    $this->max_width = 300;
    $this->max_height = 85;
    $this->get_supported_types();
  }

  function set_border_size($max_width,$max_height) 
  {
    if (($max_width > 0) && ($max_height > 0)) 
    {
      $this->max_width = $max_width;
      $this->max_height = $max_height;
      return true;
    } 
    else 
    {
      return false;
    }
  }

  function set_prefix($strg) 
  {
    $bad_chars = array('\\','/','*','?','"','<','>','|');
    for ($i=0; $i < sizeof($bad_chars); $i++) 
    {
      if (strstr($strg, $bad_chars[$i])) 
      {
        return false;
      }
    }
    $this->prefix = $strg;
    return true;
  }

  function set_destdir($img_dir) 
  {
    if (!is_dir($img_dir)) 
    {
      return false;
    }
    $this->destdir = $img_dir;
    return true;
  }

  function get_supported_types() 
  {
    if (extension_loaded('gd')) 
    {
      if (function_exists('imagegif')) 
      {
        $this->supported_types['1'] = 'GIF';
      }
      if (function_exists('imagejpeg')) 
      {
        $this->supported_types['2'] = 'JPEG';
      }
      if (function_exists('imagepng')) 
      {
        $this->supported_types['3'] = 'PNG';
      }
      if (function_exists('imagewbmp')) 
      {
        $this->supported_types['4'] = 'BMP';
      }
      return (is_array($this->supported_types) && count($this->supported_types)>0) ? $this->supported_types : false;
    } 
    else 
    {
      return false;
    }
  }

  function is_imagick() 
  {
    $is_safe_mode = get_cfg_var('safe_mode');
    if ($is_safe_mode) 
    {
      return false;
    }
    if (preg_match('/convert/i',$this->imagick)) 
    {
      return true;
    } 
    elseif ($this->imagick != '') 
    {
      if (!preg_match('/WIN/i',PHP_OS)) 
      {
        $retval = exec("whereis convert");
        if (!empty($retval)) 
        {
          $this->imagick = 'convert';
          return true;
        }
      }
      $this->imagick = 'none';
      return false;
    } 
    else 
    {
      return false;
    }
  }
  
  function create_thumbnail($source,$destination='') 
  {
    $size = @getimagesize($source);
    $new_size = $this->get_img_size_format($size[0],$size[1]);
    $img_filesize = (file_exists($source)) ? filesize($source) : false;
    $destination = trim($destination);
    if (strstr($destination, '/') || strstr($destination, '\\')) 
    {
      $new_file = $destination;
    } 
    elseif ($destination=='') 
    {
      $new_file = $this->destdir.'/'.$this->prefix.basename($source);
    } 
    else 
    {
      $new_file = $this->destdir.'/'.$this->prefix.$destination;
    }
    if (!$img_filesize || $img_filesize <= $this->min_filesize) 
    {
      return false;
    }
    if ($this->is_imagick()) 
    {
      if (is_array($size) && count($size)>0) 
      {
        exec($this->imagick." -quality 90 -antialias -scale $new_size[0]x$new_size[1] $source $new_file");
      }
    } 
    else 
    {
      $type = $size[2];
      if (isset($this->supported_types[$type])) 
      {
       switch ($type) 
       {
        case 1 :
          $im = imagecreatefromgif($source);
          if (function_exists('imagecreatetruecolor')) 
          {
            $new_im = imagecreatetruecolor($new_size[0],$new_size[1]);
            imagecolortransparent($new_im, imagecolorallocate($new_im, 0, 0, 0));
            imagecopyresampled($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          } 
          else 
          {
             $new_im = imagecreate($new_size[0],$new_size[1]);
             imagecopyresized($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          }
          imagegif($new_im,$new_file);
          break;
        case 2 :
          $im = imagecreatefromjpeg($source);
          if (function_exists('imagecreatetruecolor')) 
          {
            $new_im = imagecreatetruecolor($new_size[0],$new_size[1]);
            imagecopyresampled($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          } 
          else 
          {
             $new_im = imagecreate($new_size[0],$new_size[1]);
             imagecopyresized($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          }
          imagejpeg($new_im,$new_file,90);
          break;
        case 3 :
          $im = imagecreatefrompng($source);
          if (function_exists('imagecreatetruecolor')) 
          {
            $new_im = imagecreatetruecolor($new_size[0],$new_size[1]);
            imagecolortransparent($new_im, imagecolorallocate($new_im, 0, 0, 0));
            imagecopyresampled($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          } 
          else 
          {
             $new_im = imagecreate($new_size[0],$new_size[1]);
             imagecopyresized($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          }
          imagepng($new_im,$new_file);
          break;
        case 6 :
          $im = imagecreatefromwbmp($source);
          if (function_exists('imagecreatetruecolor')) 
          {
            $new_im = imagecreatetruecolor($new_size[0],$new_size[1]);
            imagecopyresampled($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          } 
          else 
          {
             $new_im = imagecreate($new_size[0],$new_size[1]);
             imagecopyresized($new_im,$im,0,0,0,0,$new_size[0],$new_size[1],$size[0],$size[1]);
          }
          imagewbmp($new_im,$new_file);
          break;           
       }
      }
    }
    if (file_exists($new_file))
    {
      chmod($new_file, 0755);
      return true;
    }
    else
    {
      return false;
    }
  }

  function set_min_filesize($file_size) 
  {
    if ($file_size>4096) 
    {
      $this->min_filesize = $file_size;
      return true;
    } 
    else 
    {
      return false;
    }
  }

  function get_img_size_format($img_width,$img_height) 
  {
    if ($img_width > $this->max_width) 
    {
      $tag_height = ($this->max_width / $img_width) * $img_height;
      $tag_width = $this->max_width;
      if ($tag_height > $this->max_height) 
      {
        $tag_width = ($this->max_height / $tag_height) * $tag_width;
        $tag_height = $this->max_height;
      }
    } 
    elseif ($img_height > $this->max_height) 
    {
      $tag_width = ($this->max_height / $img_height) * $img_width;
      $tag_height = $this->max_height;
      if ($tag_width > $this->max_width) 
      {
        $tag_height = ($this->max_width / $tag_width) * $tag_height;
        $tag_width = $this->max_width;
      }
    } 
    else 
    {
      $tag_width = $img_width;
      $tag_height = $img_height;
    }
    $tag_width = round($tag_width);
    $tag_height = round($tag_height);
    return array(
      $tag_width,
      $tag_height,
      'width="'.$tag_width.'" height="'.$tag_height.'"'
    );
  }
}
?>