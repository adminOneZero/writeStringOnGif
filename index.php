<?php
if (!isset($_GET["name"]) ) {
  die('sorry try again');
  exit();
}

// lib ot fix slicing words
require_once 'core/ar/arabicSupport.php';
// support arabic
$Arabic = new I18N_Arabic('Glyphs');
$text = $Arabic->utf8Glyphs($_GET["name"]);

// gif lib extractor
require 'core/GifFrameExtractor.php';

use GifFrameExtractor\GifFrameExtractor;
use movemegif\domain\FileImageCanvas;
use movemegif\GifBuilder;


// just error dump
error_reporting(E_ALL);
ini_set('display_errors', 1);


// may be need this to control memory usege
ini_set('memory_limit','1000M');

// config
$font_path = './fonts/ae_AlHor.ttf'; // font path
$y = 40;
$x = 20;
$gifFilePath = 'gif/loader11.gif'; // gif file



// get random string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}



if (GifFrameExtractor::isAnimatedGif($gifFilePath)) { // check this is an animated GIF
    $gfe = new GifFrameExtractor();
    $gfe->extract($gifFilePath);
    // add all images name
    $all_files = array();
    // Do something with extracted frames ...
    $count = 1;
    foreach ($gfe->getFrames() as $frame) {
      // get random string
      $random = generateRandomString();
      // vreate random name
      $name = $count.'__'.$random.'.png';
      // add Image name to array
      array_push( $all_files , $name);
      // The frame resource image var
      $img = $frame['image'];
      // save image
      imagepng($img,'tmp/tmp1'.$name);
      // Clear Image From Memory
      imagedestroy($img);
      $count++;
  }
}


// write in images
foreach ($all_files as $name) {
// Load Image From Source to memory as binary
$our_image = imagecreatefrompng('tmp/tmp1'.$name);


// Allocate A Color For The Text Enter RGB Value
$white_color = imagecolorallocate($our_image, 255, 255, 255);

// write Text On Image
imagettftext($our_image, 30,0,$x,$y, $white_color, $font_path, $text);

// save image
imagepng($our_image,'tmp/tmp2/'.$name);

// Clear Memory
imagedestroy($our_image);

// delete image
unlink('tmp/tmp1'.$name);
}




// include movemegif's namespace
require_once 'core/autoloader.php';


$builder = new GifBuilder();
$builder->setRepeat();

foreach ($all_files as $name) {

  $builder->addFrame()
      ->setCanvas(new FileImageCanvas('tmp/tmp2/'.$name))
      ->setDuration(4);
        unlink('tmp/tmp2/'.$name);
}



// Send Image to Browser
$builder->output('animate.gif');
