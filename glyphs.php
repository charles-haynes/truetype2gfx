<?php
// Set the enviroment variable for GD
putenv('GDFONTPATH=' . realpath('./fonts'));

// Set the content-type
header('Content-Type: image/png');
header('Cache-Control: no-cache, must-revalidate');

// Create the image
$width = 320;
$height = 240;
$im = imagecreatetruecolor($width, $height);

$cwidth=(int)($width/32); // 32 glyps per line
$cheight=(int)($height/8); // 8 lines of glyphs (256/32)
$cim = imagecreatetruecolor($cwidth,$cheight);

$dpi = 141;
if (isset($_GET["dpi"])) $dpi = $_GET["dpi"];

$size = (7 * $dpi) / 96;

$font = 'FreeSans.ttf';
if (isset($_GET["font"])) $font = $_GET["font"];

// Create some colors
$white = imagecolorallocate($im, 240, 240, 240);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, $width-1, $height-1, $white);

// get font metrics
$box = imagettfbbox($size, 0, $font, chr(110)); // n
$min_x = min( array($box[0], $box[2], $box[4], $box[6]) );
$max_x = max( array($box[0], $box[2], $box[4], $box[6]) );
$min_y = min( array($box[1], $box[3], $box[5], $box[7]) );
$max_y = max( array($box[1], $box[3], $box[5], $box[7]) );

for ($c = 0; $c < 256; $c++) {
  $box = imagettfbbox($size, 0, $font, chr($c));
  $min_x = min( array($min_x, $box[0], $box[2], $box[4], $box[6]) );
  $max_x = max( array($max_x, $box[0], $box[2], $box[4], $box[6]) );
  $min_y = min( array($min_y, $box[1], $box[3], $box[5], $box[7]) );
  $max_y = max( array($max_y, $box[1], $box[3], $box[5], $box[7]) );
}
error_log(sprintf("min_x %d, max_x %d, min_y %d, max_y %d", $min_x, $max_x, $min_y, $max_y));

for ($c = 0; $c < 256; $c++) {
  // get bounding box for glyph
  $box = imagettfbbox($size, 0, $font, chr($c));

  // Create the glyph
  imagefilledrectangle($cim, 0, 0, $cwidth-1, $cheight-1, $white);
  // center the glyph
  $cx = ($cwidth - abs($box[4]-$box[0]) - $box[0])/2;
  imagettftext($cim, $size, 0, $cx, $cheight-$max_y, $black, $font, chr($c));

  // Place it
  $x = ($c%32)*$cwidth;
  $y = ((int)($c/32))*$cheight;
  imagecopy($im, $cim, $x, $y, 0, 0, $cwidth, $cheight);
}

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>
