<?php

// XML file types
$xml_exts = array(
	'atr' => 'Actor',
	'gtf' => 'GameType',
	'gun' => 'Gun',
	'itm' => 'HandHeldItem',
	'kil' => 'KitRestriction',
	'kit' => 'Kit',
	'mis' => 'Mission',
	'prj' => 'Projectile',
	'toe' => '',
	'xml' => ''
);

// Check file references to make sure there are no broken links
function CheckFileReferences($file) {
		$data = GetXMLFile($file);
//var_dump($data);

//atr
//FolderName
//ModelFace>icg_hsp_snp_02.rsb</ModelFace>
//BlinkFaceName>icg_hsp_snp_02_blink.rsb</BlinkFaceName>
//ActionFaceName/>
//ShellFaceName/>
//KitPath>hero\avraham_arnan</KitPath>
//ModelName>avraham_arnan.chr</ModelName>
//LOD2>avraham_arnan_a.chr</LOD2>
//LOD3>avraham_arnan_b.chr</LOD3>

//kit
//KitTexture shell/art/kit
//(Firearm|ThrownItem)->ItemFileName

//gun
//UnderbarrelWeaponName
//ModelFileName

}

// Sections to check
/*
actor
equip
kits
mission
*/

// Translations
//./shell/strings.txt

$files = array();
//rglob("./*");
foreach ($xml_exts as $ext=>$name) {
	$files[$ext] = rglob("*.{$ext}");
	foreach ($files[$ext] as $file) {
		CheckFileReferences($file);
	}
}
//var_dump($files);

// Get XML file as object
function GetXMLFile($file) {
	$xml = simplexml_load_file($file);
	return($xml);
/*
	foreach ($mypix->entry as $pixinfo) {
		$title=$pixinfo->title;
		$link=$pixinfo->link['href'];
		$image=str_replace("_b.jpg","_s.jpg",$pixinfo->link[1]['href']);
		echo "<a href="",$link,""><img src="",$image,"" alt="",$title,"" /></a>n";
	}
*/
}

// rglob - recursively locate all files in a directory according to a pattern
function rglob($pattern, $getfiles=1,$getdirs=0,$flags=0) {
	$dirname = dirname($pattern);
	$basename = basename($pattern);
	$glob = glob($pattern, $flags);
	$files = array();
	$dirlist = array();
	foreach ($glob as $path) {
//var_dump($path,$files,$dirs);
		if (is_file($path) && (!$getfiles)) {
			continue;
		}
		if (is_dir($path)) {
			$dirlist[] = $path;
			if (!$getdirs) {
				continue;
			}
		}
		$files[] = $path;
	}
//var_dump($files);
	foreach (glob("{$dirname}/*", GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
		$dirfiles = rglob($dir.'/'.$basename, $getfiles,$getdirs,$flags);
		$files = array_merge($files, $dirfiles);
	}
	return $files;
}
