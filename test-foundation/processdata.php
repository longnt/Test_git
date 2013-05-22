<?php
error_reporting(E_ALL ^ E_NOTICE);
global $dir_file_upload ;
$dir_file_upload = 'files-upload/';
global $imgExts;
$imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif", "JPG", "JPEG", "PNG");
$results['status']= 'false';
$results['log']= 'No Result';
$results['url'] = null;
$results['name'] = '';
if(isset($_POST) && ($_POST['action'] == 'search')){
	if(isset($_POST['keyword'])){
		try{
			$info = save_image($_POST['keyword']);
			if($info){
				$results['status']= 'true';
				$results['log']= 'Results data';
				$results['name'] = $info['filename'];
				$results['url'] = $info['url'];
			}
		} catch (Exception $e){}
	}
	$_POST = null;
	die(get_json($results));
} else if(isset($_POST) && ($_POST['action'] == 'show_images')){
	show_images();
} else if(isset($_POST) && ($_POST['action'] == 'delete_image')){
	$file_input = $_POST['src'];
	$file_info = pathinfo($file_input);
	if(isset($file_info['basename'])){
        unlink($dir_file_upload.$file_info['basename']);
		$results['status']= 'true';
		$results['log']= 'Delete image to success.';
	}
	die(get_json($results));
}

function save_image($inPath = null){ //Download images from remote server
	$file_name = set_file_name($inPath);
	if(isset($file_name['url'])){
		try{
			$handle = @fopen($inPath, "r");
			if($handle){
				$in=    fopen($inPath, "rb");
				$out=   fopen($file_name['url'], "wb");
				while ($chunk = fread($in,8192))
				{
					fwrite($out, $chunk, 8192);
				}
				fclose($in);
				fclose($out);
				$file_name['url'] = str_replace('processdata.php','',getWeb_Root()).$file_name['url'];
				return $file_name; 
			} else {
				return null;
			}
		} catch (Exception $e){return null;}
		
	} else {
		return null;
	}
}

function set_file_name($url = null){
	global $imgExts, $dir_file_upload ;
	$file_info = pathinfo($url);
	if(isset($file_info)){
		$urlExt = pathinfo($url, PATHINFO_EXTENSION);
		$ext_file = '';
		for($i=0; $i<= strlen($urlExt); $i++){
			$ext_file .= $urlExt[$i-1];
			if($urlExt[$i]== '#' || $urlExt[$i]== '?'){
				break;
			}
		}
		if($ext_file != null){
			$urlExt = $ext_file;
		}
		if (in_array($urlExt, $imgExts)) {
			$file_info['basename'] = str_replace(array(' ','%20'),'-',$file_info['filename'].'.'.$urlExt);
			$file_info['name'] = $file_info['basename'];
			 $check_file =$dir_file_upload.$file_info['basename'];
			$file_info['url'] = $check_file;
			if(file_exists($file_info['url'])){
				$status = false;
				$i = 1;
				while ($status == false){
					$check_file = $dir_file_upload.$i.'-'.$file_info['basename'];
					if(!file_exists($check_file)){
						$file_info['name'] = $i.'-'.$file_info['basename'];
						$status = true;
					} else {
						$i++;
					}
				}
				$file_info['url'] = $check_file;
				return $file_info;
			} else {
				return $file_info;
			}
		} else {
			return null;
		}
	}
	return null;
}
/* function:  generates thumbnail */
function make_thumb($src,$dest,$desired_width) {
	/* read the source image */
	$source_image = imagecreatefromjpeg($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$desired_height = floor($height*($desired_width/$width));
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width,$desired_height);
	/* copy source image at a resized size */
	imagecopyresized($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
	/* create the physical thumbnail image to its destination */
	imagejpeg($virtual_image,$dest);
}

/* function:  returns files from dir */
function get_files($images_dir,$exts = array('jpg')) {
	global $imgExts;
	$exts = $imgExts;
	$files = array();
	if($handle = opendir($images_dir)) {
		while(false !== ($file = readdir($handle))) {
			$extension = strtolower(get_file_extension($file));
			if($extension && in_array($extension,$exts)) {
				$files[] = $file;
			}
		}
		closedir($handle);
	}
	return $files;
}

/* function:  returns a file's extension */
function get_file_extension($file_name) {
	return substr(strrchr($file_name,'.'),1);
}
function show_images(){
	global $dir_file_upload;
	$results['status']= 'false';
	$results['log']= 'No Results';
	$results['data'] = '';
	$images_dir = $dir_file_upload;
	$image_files = get_files($images_dir);
	if(count($image_files)) {
		foreach($image_files as $index=>$file) {
			$str_link = str_replace('processdata.php','',getWeb_Root()).$dir_file_upload.$file;
			$file_info = pathinfo($str_link);
			$results['data'][] = $file_info;
		}
		$results['log'] = 'Photo Library';
		$results['status']= 'true';
	}
	die(get_json($results));
}

function utf162utf8($utf16){
	// Check for mb extension otherwise do by hand.
	if( function_exists('mb_convert_encoding') ) {
		return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
	}

	$bytes = (ord($utf16{0}) << 8) | ord($utf16{1});

	switch (true) {
		case ((0x7F & $bytes) == $bytes):
			// this case should never be reached, because we are in ASCII range
			// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
			return chr(0x7F & $bytes);

		case (0x07FF & $bytes) == $bytes:
			// return a 2-byte UTF-8 character
			// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
			return chr(0xC0 | (($bytes >> 6) & 0x1F))
				 . chr(0x80 | ($bytes & 0x3F));

		case (0xFFFF & $bytes) == $bytes:
			// return a 3-byte UTF-8 character
			// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
			return chr(0xE0 | (($bytes >> 12) & 0x0F))
				 . chr(0x80 | (($bytes >> 6) & 0x3F))
				 . chr(0x80 | ($bytes & 0x3F));
	}

	// ignoring UTF-32 for now, sorry
	return '';
}

/**
* Function name :	decodeUnicodeString
* Description : decode Unicode String
*/
function decodeUnicodeString($chrs)
{
	$delim       = substr($chrs, 0, 1);
	$utf8        = '';
	$strlen_chrs = strlen($chrs);

	for($i = 0; $i < $strlen_chrs; $i++) {

		$substr_chrs_c_2 = substr($chrs, $i, 2);
		$ord_chrs_c = ord($chrs[$i]);

		switch (true) {
			case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $i, 6)):
				// single, escaped unicode character
				$utf16 = chr(hexdec(substr($chrs, ($i + 2), 2)))
					   . chr(hexdec(substr($chrs, ($i + 4), 2)));
				$utf8 .= utf162utf8($utf16);
				$i += 5;
				break;
			case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
				$utf8 .= $chrs{$i};
				break;
			case ($ord_chrs_c & 0xE0) == 0xC0:
				// characters U-00000080 - U-000007FF, mask 110XXXXX
				//see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 2);
				++$i;
				break;
			case ($ord_chrs_c & 0xF0) == 0xE0:
				// characters U-00000800 - U-0000FFFF, mask 1110XXXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 3);
				$i += 2;
				break;
			case ($ord_chrs_c & 0xF8) == 0xF0:
				// characters U-00010000 - U-001FFFFF, mask 11110XXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 4);
				$i += 3;
				break;
			case ($ord_chrs_c & 0xFC) == 0xF8:
				// characters U-00200000 - U-03FFFFFF, mask 111110XX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 5);
				$i += 4;
				break;
			case ($ord_chrs_c & 0xFE) == 0xFC:
				// characters U-04000000 - U-7FFFFFFF, mask 1111110X
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 6);
				$i += 5;
				break;
		}
	}

	return str_replace("\/","/",$utf8); 
}
/**
* Function name :	get_json
* Output		:	data type json
*/
function get_json($data = null){
	$json = json_encode($data); 
	return decodeUnicodeString($json);
}

/* Set status order */
function curPageURL() {
	$pageURL = 'http';
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function getDomain_website($url = null){
	$url = curPageURL();
	if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === FALSE)
	{
	return false;
	}
	/*** get the url parts ***/
	$parts = parse_url($url);
	/*** return the host domain ***/
	return $parts['scheme'].'://'.$parts['host'];
}

function getWeb_Root($url = null){
	$url = curPageURL();
	if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === FALSE)
	{
	return false;
	}
	/*** get the url parts ***/
	$parts = parse_url($url);
	/*** return the host domain ***/
	return $parts['scheme'].'://'.$parts['host'].$parts['path'];
}