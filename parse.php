<?php
//FB meta img
//whitelist some URLS
//JPG ??
//Ad urls - http://ad.doubleclick.net
//repeated URLs
//Image related to website desc 
// 	- Relevant keywords in the href or alt attributes
//	- Location of the <img> tag on page, the closer to relevant content the better, but may not always work for complicated layouts
//http://stackoverflow.com/questions/2987195/how-does-facebook-know-what-image-to-parse-out-of-an-article
//<meta name="thumbnail" content="whatever.jpg" />
//<link rel="image_src" href="thumbnail_image" />

/*
 * Title
 	- meta tag - title - done
	- FB meta - done
 * Description
	- Meta tag - desc - done
	- FB meta - done
	- Use SEO tags - itemprop - description
	 
 * Full URL
 * Thumbnail Images
 	- FB meta img 

*
*<meta property="og:title" content="Your site or page name here" />
<meta property="og:type" content="website" />
<meta property="og:url" content="http://your-web-address-here.com" />
<meta property="og:image" content="your-thumbnail-image-url-here" />
<meta property="og:site_name" content="Your website name here" />
<meta property="og:description" content="Description of the site or page here." />
*
*/

function getImages($content, $addedImgs) {
	$allimages = array();
// 	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"] alt="([^<]*)".*>|<img.+alt="([^<]*)" src=[\'"]([^\'"]+)[\'"].*>/U',$content, $allimages);
// 	preg_match_all('/<img.+(src="([^<]*)")>/Ui',$content, $allimages);
	preg_match_all('/<img[^>]+>/i',$content, $allimages); 
// 	$output = preg_match_all('/<img[^>]*src=[\"|\'](.*)[\"|\']>/U',$content, $allimages);
	//(name="([^<]*)"|property="([^<]*)")
// 	echo "<pre>";
// 	print_r($allimages);
	//http://stackoverflow.com/questions/138313/how-to-extract-img-src-title-and-alt-from-html-using-php
	$relatedImages = array(); /*$addedImgs = array();
	if(isset($siteData['site_image'])) {
		if(isset($siteData['site_image']["og"])){
			foreach($siteData['site_image']["og"] as $addedImg){
				array_push($addedImgs, $addedImg);
			}
		}
		if(isset($siteData['site_image']["link"])){
			foreach($siteData['site_image']["link"] as $addedImg){
				array_push($addedImgs, $addedImg);
			}
		}
	}*/
	foreach($allimages[0] as $ind=>$img){
// 		echo $img.'<br/>\n';
		$imgArr = parseImg($img);
// 		print_r($imgArr);
		if(isset($imgArr["src"]) && !in_array($imgArr["src"], $addedImgs)){//isset($imgArr["alt"]) && $imgArr["alt"] != ''){//echo htmlentities($imgArr["src"]).'<br/>';
			array_push($relatedImages, $imgArr["src"]);
			array_push($addedImgs, $imgArr["src"]);
		}
		/*preg_match_all('/(alt|title|src)=("[^"]*")/i',$img, $attrs);
// 		print_r($attrs);
		foreach($attrs[1] as $x=>$attr){
			$trimVal = trim($attrs[2][$x]); $trimSrc = "";
			if($attr == 'alt' && $trimVal != '""'){
				echo $trimVal.'<br/>';
// 				echo $trimVal.' '.$allimages[5][$ind] .'<br/>';
				if($trimSrc == ""){
					foreach($attrs[1] as $y=>$srcattr){
						$trimSrcVal = trim($attrs[2][$y]);
						if($srcattr == 'src' && $trimSrcVal != '""'){
							$trimSrc = $trimSrcVal;
						}
					}
					if($trimSrc != "")
						array_push($relatedImages, $trimSrc);
				}else{
					if($trimSrc != "")
						array_push($relatedImages, $trimSrc);
				}
				$trimSrc = "";
			}elseif($attr == 'src' && $trimVal != '""'){
				$trimSrc = $trimVal;
			}else{
				$trimSrc = "";
			}
		}*/
	}
// 	print_r($relatedImages);
// 	echo "</pre>";
	return $relatedImages;
}
function isValidURL($url)
{
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}
function getImageUrl($imgUrl){
	global $url ;
	global $reqUrlParams ;
	$imgUrl = str_replace('"', "", $imgUrl);
	$imgurl = $imgUrl;
	$urlParams = parse_url($imgUrl);
	if(!isset($urlParams["host"]) || $urlParams["host"] == ""){
		if(isset($reqUrlParams["host"]))
			$imgurl = $reqUrlParams["host"].'/'.$imgUrl;
	}
	if(!isset($urlParams["scheme"]) || $urlParams["scheme"] == ""){
		$imgurl = $reqUrlParams["scheme"].'://'.$imgurl;
	}
	return (isValidURL($imgurl) ? $imgurl : "");
}
function parseImg($imgHtml){
// 	$imgHtml='<IMG BORDER=2 HEIGHT=133 WIDTH=172 SRC="picts/FREAKME.jpg" alt="Ribbit">';
	preg_match_all('/(alt|width|height|src)=("[^"]*")/i',$imgHtml, $values);
// 	print_r($values);
// 	global $url ;
// 	echo ($url).'<br/>';
// 	$imgHtml = str_replace(">","/>",$imgHtml);
// 	$imgHtml = str_replace("//>","/>",$imgHtml);
// 	$parser = xml_parser_create();
// 	echo $imgHtml.'\n';
// 	xml_parse_into_struct($parser, $imgHtml, $values);
// 	print_r($values);
	$arr = array();
	$blacklistImgs = array("ad.doubleclick.net");
	$imgSrc = "";
	$imgAlt = "";
	$height = 0;
	$width = 0;
	foreach ($values[1] as $key => $val) {//print_r($val);
// 		echo $key;
		if($val == "SRC" || $val == 'src'){
			$imgSrc = getImageURL($values[2][$key]);
		}
		if($val == "ALT" || $val == 'alt'){
			$imgAlt = $values[2][$key];
		}
		if($val == "HEIGHT" || $val == 'height'){
			$height = $values[2][$key];
		}
		if($val == "WDITH" || $val == 'width'){
			$width = $values[2][$key];
		}
	}
// 	echo $imgSrc.' '.$imgAlt.' '.$height.' '.$width;
		if ($imgSrc != "") {//echo "hi";
			$urlParams = parse_url($imgSrc);
			if(!in_array($urlParams["host"], $blacklistImgs)){
// 				print_r($val['attributes']);
				/*$width = 0; $height = 0;
				if(isset($val['attributes']['WIDTH'])){
					$width = $val['attributes']['WIDTH'];
				}
				if(isset($val['attributes']['HEIGHT'])){
					$height = $val['attributes']['HEIGHT'];
				}*/
				if($width < 1 && $height < 1){
					list($width, $height, $type, $attr) = @getimagesize($imgSrc);
				}
				if($type == 17){//If its fav ico with extn .ico
					return $arr;
				}
				$hwRatio = 0;
				if($width > 0 && $height > 0)
					$hwRatio = $width/$height;
// 				echo $width.' '.$height.' '.$hwRatio. ' '.$imgSrc .'<br/>';
				if($hwRatio > 0.8 && $hwRatio < 2){
					$minImg = 32;
					$isSmallSquare = ($hwRatio == 1? ($width > $minImg) : ($width > $minImg && $height > $minImg));
					if($isSmallSquare){//To remove Background images used for UI, if square
						$arr['src'] = $imgSrc;
						if(isset($imgAlt))
							$arr['alt'] = $imgAlt;
						if(isset($width))
							$arr['width'] = $width;
						if(isset($height))
							$arr['height'] = $height;
					}
				}
			}
		}
// 	}
// 	print_r($arr);
	return $arr;
}
function getMicroData($content, $sitedata){
	$microData = array();
	
	if(isset($sitedata['description'])){
		if(isset($sitedata['description']["meta"]) && !empty($sitedata['description']["meta"]) ){
			return $sitedata;
		}
	}
	
	//Filtering content with HTML5 microdata itemprop = description to get the descrition
	preg_match_all("/itemprop=\"description\">(.*?)<\//ims", $content, $allData);
// 	echo "<pre>";
	$desc = "";
	foreach($allData[1] as $ind => $data){
		//Removing HTML tags from content
		$data = preg_replace("/<.*?>/", "", $data);
		$desc = trim($data);
	}
	if(!empty($desc)){
		if(isset($sitedata['description']))
			array_push($sitedata['description'] , array("meta" => $desc));
		else 
			$sitedata['description'] = array("meta" => $desc);
	}
	return $sitedata;
// 	print_r($sitedata);
// 	echo "</pre>";
}
function getTitleMeta($content){
	$allimages = array();
// 	preg_match_all("<meta\\s*(?:(?:\\b(\\w|-)+\\b\\s*(?:=\\s*(?:[\"\"[^\"\"]*\"\"|'[^']*'|[^\"\"'<> ]|[''[^'']*''|\"[^\"]*\"|[^''\"<> ]]]+)\\s*)?)*)/?\\s*>", $content, $allimages);
	preg_match_all('/<meta (name="([^<]*)"|property="([^<]*)") (content="([^<]*)"|value="([^<]*)")([^<]*)>/i',$content, $allimages);
// 	echo "<pre>";
// 	print_r($allimages);
	
	$allTags = array("video" => array());
	foreach($allimages[0] as $ind=>$img){
		if($allimages[2][$ind] == 'description'){
			if(isset($allTags['description']))
				$allTags['description']["meta"] = $allimages[5][$ind];
			else{
				$allTags['description'] = array("meta" => $allimages[5][$ind]);
			}
		}elseif($allimages[2][$ind] == 'keywords'){
			$allTags['keywords'] = $allimages[5][$ind];
		}elseif($allimages[3][$ind] == 'og:title'){
			$allTags['title'] = array("og" => $allimages[5][$ind]);
		}elseif($allimages[3][$ind] == 'og:site_name'){
			$allTags['site_name'] = $allimages[5][$ind];
		}elseif($allimages[3][$ind] == 'og:url'){
			$allTags['site_url'] = $allimages[5][$ind];
		}elseif($allimages[3][$ind] == 'og:description'){
			if(isset($allTags['description']))
				$allTags['description']["og"] = $allimages[5][$ind];
			else{
				$allTags['description'] = array("og" => $allimages[5][$ind]);
			}
		}elseif($allimages[3][$ind] == 'og:image'){
			$allTags['site_image'] =array("og" => array($allimages[5][$ind]));
		}elseif($allimages[3][$ind] == 'og:type'){
			$allTags['site_type'] =array("og" => array($allimages[5][$ind]));
		}elseif($allimages[3][$ind] == 'og:video'){
			$allTags['video']["url"] = array("og" => $allimages[5][$ind]);
		}elseif($allimages[3][$ind] == 'og:video:width'){
			$allTags['video']["width"] =array("og" => $allimages[5][$ind]);
		}elseif($allimages[3][$ind] == 'og:video:height'){
			$allTags['video']["height"] = array("og" => $allimages[5][$ind]);
		}elseif($allimages[2][$ind] == 'twitter:player'){
			$playerUrl = $allimages[6][$ind] == "" ? $allimages[5][$ind] : $allimages[6][$ind];
			if(!isset($allTags['video']["url"])){
				$allTags['video']["url"] = array("tw" => $playerUrl);
			}else 
				$allTags['video']["url"]["tw"] = $playerUrl;
		}elseif($allimages[3][$ind] == 'twitter:player:width'){
			$playerWidth = $allimages[5][$ind];
			if(!isset($allTags['video']["width"])){
				$allTags['video']["width"] = array("tw" => $playerWidth);
			}else $allTags['video']["width"]["tw"] = $playerWidth;
		}elseif($allimages[3][$ind] == 'twitter:player:height'){
			$playerHeight = $allimages[5][$ind];
			if(!isset($allTags['video']["height"])){
				$allTags['video']["height"] = array("tw" => $playerHeight);
			}else $allTags['video']["height"]["tw"] = $playerHeight;
		}
	}
	
	//Get title
	preg_match_all('/<title>([^<]*)<\/title>/i',$content, $allimages);
	foreach($allimages[0] as $ind=>$title){
		if(isset($allTags['title']))
			$allTags['title']["meta"] = $allimages[1][$ind];
		else $allTags['title'] = array("meta" => $allimages[1][$ind]);
	}
	//End
	
//	Get site image
// 	$content = '<link  href="http://s1.licdn.com/scds/common/u/img/icon/icon_in_people_80x80.jpg" rel="image_src"/>';
	preg_match_all('/<link.+"image_src".+href="([^"]+)"|<link.+href="([^"]+)"[^>]+"image_src"/U',$content, $allimages);
	$siteimage = array(); $addedImgs = array();
	if(isset($allTags['site_image'])) {
		if(isset($allTags['site_image']["og"])){
			foreach($allTags['site_image']["og"] as $addedImg){
				array_push($addedImgs, $addedImg);
			}
		}
	}
	foreach($allimages as $imgs){
		foreach($imgs as $img){
			if($img != '' && isValidURL($img) && !in_array($img, $addedImgs)){
				array_push($siteimage , $img);
				array_push($addedImgs, $img);
			}
		}
	}
	if(!empty($siteimage)){
		$allTags['site_image']["link"] = $siteimage;
		array_push($addedImgs, $siteimage);
	}
	//If there is open graph image meta tag or Link meta tag for site image, scrape the site to get all images
// 	if(empty($addedImgs)){
// 		$relatedImages = getImages($content, $allTags, $addedImgs);
// 		$allTags["site_image"]["meta"] = $relatedImages;
// 	}
// 	echo "</pre>";
	return array("allTags" => $allTags, "addedImages" => $addedImgs);
	
}
/*function getUrls($post){
	$urls = array();
	
	$text = str_replace("<br/>", " <br/>", $post);
		
	$urlPattern = "/\b(?:https?|ftp):\/\/[a-zA-Z0-9-+&@#\/%?=~_|!:,.;]*[a-zA-Z0-9-+&@#\/%=~_|]/";
	// www. sans http:// or https://
	$pseudoUrlPattern = "/(^|[^\/])(www\.[\S]+(\b|$))/";
	
	preg_match_all($urlPattern, $text, $urlMatches);
	preg_match_all($pseudoUrlPattern, $text, $pseudoUrlMatches);
	
// 	echo "<pre>";
// 	print_r($urlMatches);
	if(count($urlMatches) > 0){
	foreach($urlMatches[0] as $url){
		array_push($urls, $url);
	}
	}
	if(count($pseudoUrlMatches) > 0){
// 		print_r($pseudoUrlMatches);
		foreach($pseudoUrlMatches[0] as $url){
			array_push($urls, $url);
		}
	}
	return $urls;
}*/
try{
//http://www.ebay.com/itm/Sterling-Silver-Souvenir-Spoon-New-Orleans-Louisiana-/290758424329?pt=Antiques_Silver&hash=item43b28d7709#ht_853wt_1397
//http://stackoverflow.com/questions/6294069/get-contents-of-url-like-facebook-wall-post
//http://www.espncricinfo.com/
//http://allaboutfrogs.org/
//http://www.nytimes.com/2012/08/18/us/politics/ryan-has-ear-of-washingtons-conservative-establishment.html?_r=1&hp
//www.santaclaraca.gov
//http://sutter.ca.campusgrid.net/home 
$url = $_REQUEST['url'];//'http://stackoverflow.com/questions/6294069/get-contents-of-url-like-facebook-wall-post';//'http://www.espncricinfo.com/';//'http://www.linkedin.com/';//$_REQUEST['url'];
// $urls = getUrls($post);

$dataArr = array();
ob_start();
// header('Content-type: application/json');
echo "<script>";
if($url == null || $url == ""){
	echo 'parent.handleURLResponse('.json_encode(array("isSuccess" => false, "errMsg" => "URL_NOT_AVAILABLE")).')';
	return;
	
}
// foreach ($urls as $url){
// $url = $urls[0];
if((strpos($url, "http://") === false) && (strpos($url, "https://") === false)){
	$url = "http://".$url;
}
$reqUrlParams = parse_url($url);
if(!isset($reqUrlParams['host'])){
	$reqUrlParams['host'] = "";
}
if(!isset($reqUrlParams['scheme'])){
	$url = "http://".trim($url);
	$reqUrlParams['scheme'] = "http://";
}
$content = @file_get_contents($url);
if($content == "" || $content == null){
	echo 'parent.handleURLResponse('.json_encode(array("isSuccess" => false, "errMsg" => "CONTENT_NOT_AVAILABLE")).')';
	return;
}
// $metatags = get_meta_tags($url);

$sitedata1 = getTitleMeta($content);
$sitedata = $sitedata1["allTags"];
$addedImgs = $sitedata1["addedImages"];
$sitedata = getMicroData($content, $sitedata);
if(!isset($sitedata['site_url']) || $sitedata['site_url']){
	$sitedata['site_url'] = $url;
}
array_push($dataArr, $sitedata);
// }
// echo "<pre>";
// print_r($metatags);
// print_r($sitedata);
echo 'parent.handleURLResponse('.json_encode(array("isSuccess" => true, "url" => $url, "data" => $dataArr)).')';
// print_r($relatedImages);
// echo count($images[1]).'<br/>';
// foreach($images[1] as $img){
// 	echo $img.'<br/>';
// }
echo "</script>";
ob_flush();
flush();
// sleep(5);
echo "<script>";
$relatedImages = getImages($content, $addedImgs);
echo 'parent.buildExtraImages('.json_encode(array("isSuccess" => true, "url" => $url, "data" => $relatedImages)).')';		
echo "</script>";
ob_flush();
flush();
}catch(Exception $e){
	echo $e->getMessage();
}