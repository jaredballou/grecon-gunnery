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

$strings = GetStringTable();
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



function GetFileList() {
	$testpaths = array('.','../Origmiss','../../Data');
	foreach ($testpaths as $tp) {
		$filepath = realpath($tp);
	}
//{$fieldpath}{$ptr}");
}

// Check file references to make sure there are no broken links
function CheckFileReferences($file) {
	$data = GetXMLFile($file);
//var_dump($data);
	$fields = array(
//		'KitPath' => 'kits',
		'ItemFileName' => 'equip',
		'KitTexture' => 'shell/art/kit',
		'Firearm|ItemFileName' => 'equip',
		'ThrownItem|ItemFileName' => 'equip',
		'UnderbarrelWeaponName' => 'equip',
		'ModelFileName' => 'model',
	);
/*
	if (isset($data['FolderName'])) {
		$fields['ModelName'] = "character/{$data['FolderName']}";
	}
*/
	foreach ($fields as $xmlpath => $fieldpath) {
		$bits = explode("|",$xmlpath);
		foreach ($bits as $bit) {
			if (isset($data[$bit])) {
				$ptr = &$data[$bit];
			} else {
				continue 2;
			}
		}
		if ($ptr) {
			$testpaths = array('.','../Origmiss','../../Data');
			foreach ($testpaths as $tp) {
				$filepath = "{$tp}/{$fieldpath}/{$ptr}";
				$regex = "";
				for ($i=0;$i<strlen($filepath);$i++) {
					if (ctype_alnum($filepath[$i])) {
						$regex.="[".strtolower($filepath[$i]).strtoupper($filepath[$i])."]";
					} else {
						$regex.=$filepath[$i];
					}
				}
				$filepath = glob($regex);
//var_dump($regex,$filepath);
				if (!count($filepath)) {
					continue;
				}
				return;
/*
				if (fileExistsCI($filepath)) {
//var_dump($filepath);
					return;
				}
*/
			}
			echo "WARNING: Cannot find {$file}:<{$xmlpath}>:{$fieldpath}/{$ptr}!\n";
		}
	}
//exit;
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

}


// Get XML file as object
function GetXMLFile($file) {
	$xml = (array) simplexml_load_file($file);
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

// Load string table translation
function GetStringTable($file="shell/strings.txt") {
	$strings = array();
	$data = preg_split('/\n|\r\n?/', file_get_contents($file));
	foreach ($data as $line) {
		preg_match_all('/^[ \t]*"([^"]*)"[ \t]*"([^"]*)"[ \t]*$/',$line,$matches);
		if ($matches[2]) {
			$strings[$matches[1][0]] = $matches[2][0];
		}
	}
	return $strings;
}






/**
 * Single level, Case Insensitive File Exists.
 *
 * Only searches one level deep. Based on
 * https://gist.github.com/hubgit/456028
 *
 * @param string $file The file path to search for.
 *
 * @return string The path if found, FALSE otherwise.
 */
function fileExistsSingle($file)
{
    if (file_exists($file) === TRUE) {
        return $file;
    }

    $lowerfile = strtolower($file);

    foreach (glob(dirname($file).'/*') as $file) {
        if (strtolower($file) === $lowerfile) {
            return $file;
        }
    }

    return FALSE;

}//end fileExistsSingle()


/**
 * Case Insensitive File Search.
 *
 * @param string $filePath The full path of the file to search for.
 *
 * @return string File path if valid, FALSE otherwise.
 */
function fileExistsCI($filePath)
{
    if (file_exists($filePath) === TRUE) {
        return $filePath;
    }

    // Split directory up into parts.
    $dirs = explode('/', $filePath);
    $len  = count($dirs);
    $dir  = '/';
    foreach ($dirs as $i => $part) {
        $dirpath = fileExistsSingle($dir.$part);
        if ($dirpath === FALSE) {
            return FALSE;
        }

        $dir  = $dirpath;
        $dir .= (($i > 0) && ($i < $len - 1)) ? '/' : '';
    }

    return $dir;

}//end fileExistsCI()
