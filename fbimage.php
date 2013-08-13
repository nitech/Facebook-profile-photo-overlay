<?php
// Add an overlay to your facebook profile photo
// By Simon Pedersen at www.good-morning.no

// Input: fbid - facebook id, returns a jpeg file being the input user's profile photo manipulated with an overlay
// called this way: 
// fbimage.php?fbid=100000515495823

// File exists in cache? http worker process needs write access to this folder
$src = $_REQUEST['fbid'];
$path = "cache/".$src.".jpg";

// only create if not already exists in cache
if (!file_exists($path)) create($src, $path);

// override line 13. Always create for testing purposes
// create($src, $path);

// output as jpeg
header('Content-Type: image/jpg');
readfile($path);

// Create image 
function create($src, $path){

    // base image is just a transparent png in the same size as the input image
	$base_image = imagecreatefrompng("images/fbimage-template.png");

    // Get the facebook profile image in 200x200 pixels
	$photo = imagecreatefromjpeg("http://graph.facebook.com/".$src."/picture?width=200&height=200");
	
    // read overlay  
	$overlay = imagecreatefrompng("images/fbimage-overlay.png");

    // keep transparency of base image
	imagesavealpha($base_image, true);
	imagealphablending($base_image, true);

    // place photo onto base (reading all of the photo and pasting unto all of the base)
	imagecopyresampled($base_image, $photo, 0, 0, 0, 0, 200, 200, 200, 200);
	
    // place overlay on top of base and photo
	imagecopy($base_image, $overlay, 0, 0, 0, 0, 200, 200);

    // Save as jpeg
    imagejpeg($base_image, $path);
}

// cropping function not in use in this example
function crop($resource, $width, $height)
{

    // resource dimensions
    $size = array(
        0 => imagesx($resource),
        1 => imagesy($resource),
    );
    // sides
    $longer  = (int)($size[0]/$width > $size[1]/$height);
    $shorter = (int)(!$longer);
    // ugly hack to avoid condition for imagecopyresampled()
    $src = array(
        $longer  => 0,
        $shorter => ($size[$shorter]-$size[$longer])/2,
    );
    // new image resource
    $new = imagecreatetruecolor($width, $height);
    // do the magic
    imagecopyresampled($new, $resource,
        0,  0,
        $src[0], $src[1],
        $width, $height,
        $size[$longer], $size[$longer]
    );

    return $new;
}
?>