#!/usr/local/bin/php -q
<?php
/*

SH3 Extractor

A preliminary extractor for the resource file format used in Kingsoft's
Xiake Yingxiong Zhuan XP.

Version:   1.0
Author:    Derrick Sobodash <derrick@sobodash.com>
Copyright: (c) 2017 Derrick Sobodash
Web site:  https://github.com/sobodash/sh3extract/
License:   BSD License <http://opensource.org/licenses/bsd-license.php>

*/


error_reporting (E_WARNING | E_PARSE);
$version = "1.0";
echo ("\SH3 Extractor v$version (cli)\nCopyright (c) 2017 Derrick Sobodash\n");
set_time_limit(6000000);

// Check the PHP version of the user
if(phpversion() < "4.3.2")
  die(print "ERROR: PHP 4.3.2 or newer is required\n");

$idx = fopen("Sh3DiskIdx.dat", "rb");
$img = fopen("Sh3DiskImg.dat", "rb");

$csv = fopen("table.csv", "w");

while(!feof($idx)) {
	$skip_header = 0;
	$filename = fread($idx, 0x141);
	$filename = trim(substr($filename, 0, strpos($filename, "\0")));
	$filename = iconv("BIG5", "UTF-8", $filename);
	$path = explode("/", $filename);
	array_pop($path);
	$path = implode("/", $path);
	@list($shit, $start) = unpack("V", fread($idx, 4));
	@list($shit, $length) = unpack("V", fread($idx, 4));
	print "Dumping $filename...\n";
	@mkdir("dump/".$path, 0755, true);
	$fo = fopen("dump/".$filename, "w");
	fseek($img, $start);
	$dump = fread($img, $length);
	if(substr($dump, 0, 3) =="SWF")
		fputs($fo, substr($dump, 0x11, strlen($dump)-0x11-5));
	else
		fputs($fo, $dump);
	fclose($fo);
	fputcsv($csv, array($filename, "0x".dechex($start), "0x".dechex($length)));
}

fclose($csv);
fclose($idx);
fclose($img);

?>
