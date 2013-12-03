#!/usr/bin/php5
<?php

/* ------------------------------------------------------
	bigls 		: https://github.com/sufehmi/bigls

        version		: 1.0.2

        license		: GPL v2
			  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			  http://vlsm.org/etc/gpl-unofficial.id.html

			This program is distributed in the hope that it will be useful,
			but WITHOUT ANY WARRANTY; without even the implied warranty of
			MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
			GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/

// ------------------- VARIABLES --------------------------------

$version 	= "bigls 1.0.2

Copyright (C) 2013 Harry Sufehmi.
License GPLv2: GNU GPL version 2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>.
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Written by Harry Sufehmi.
"; 

$helptext 	= "Usage: bigls [OPTION] 

List huge number (millions) of files in a directory (default : in the current directory).

  -d, --directory=PATH 	specify a different directory to list its content
  -l			long format / more human-readable
  -n			do not list directories
  
  --dateformat=STRING   specify date format, as defined in : http://php.net/date

  -h, --help     	display this help and exit
  -v, --version  	output version information and exit


Exit status:
 0  if OK,
 1  if there's an error

Report bigls bugs to <https://github.com/sufehmi/bigls/issues>
bigls home page: <https://github.com/sufehmi/bigls>
";

$nodir			= false;
$longform		= false;
$dateformat		= 'Ymd';
$maxsizedigits		= 15; 		// max file size digits to be shown

$outputinterval		= 1000;  	// show only after 1000 files each time = faster performance
$dir 			= ".";		// default directory to ls (list)


// ------------------- START CODE -------------------------------

// get parameters & process
$opts = getopt("d:hlnv", array('help','version', 'dateformat:')); 
foreach (array_keys($opts) as $opt) switch ($opt) {

	// -d : specify which directory to list
	case 'd' : 
		$dir = $opts['d'];
		break;

	// -n : do not list directories
	case 'n' : 
		$nodir = true;
		break;

	// -l : long format
	case 'l' : 
		$longform 	= true;
		$dateformat	= 'Y-m-d';
		break;


	// --dateformat : specify date format, as defined in : http://php.net/date
	case 'dateformat' : 
		$dateformat = $opts['dateformat'];
		break;


	case 'h' : 
		echo $helptext;
		exit(0);

	case 'help' : 
		echo $helptext;
		exit(0);

	case 'v' : 
		echo $version;
		exit(0);

	case 'version' : 
		echo $version;
		exit(0);

}; // foreach (array_keys($opts) as $opt) switch ($opt) {



// start going through list of files ===============================================
$ctr = 0;
if ($handle = opendir($dir)) {
	
	$line	= '';
	while (false !== ($entry = readdir($handle))) {
		
		if ($entry != "." && $entry != "..") {

                	$stat   = stat($dir."/".$entry);

			if ($longform) {
				$date = date($dateformat, $stat['mtime']) . "::";
				// fixme: test if this is faster : http://www.paulferrett.com/2012/php-sprintf-pad-leading-zeros/
				$size = str_pad($stat['size'], $maxsizedigits, " ", STR_PAD_LEFT) . "::";
			} else {
				$date = date($dateformat, $stat['mtime']) . "-";
				$size = '';
			};

			$filename = $entry . "\n";
	
			// store a whole new line/entry
			if ($nodir) {
				if (!is_dir($entry)) {
					$ctr++;
					$line .= $date;
					$line .= $size;
					$line .= $filename;
				};
			} else {
				$ctr++;
				$line .= $date;
				$line .= $size;
				$line .= $filename;
			};

		}; // if ($entry != "." && $entry != "..") 

		// when enough entries collected in $line, print it
		if ($ctr >= $outputinterval) {
			echo $line;
			$ctr  = 0;
			$line = '';
		};

	}; // while (false !== ($entry = readdir($handle))) {
	closedir($handle);

	// print unprinted list of files
	if ($ctr < $outputinterval) {
		echo $line;
	};
};

// normal exit
exit(0);


/* -------------------------------

CHANGELOG 

01.00.00 / 01 Nov 2013 : First working version

01.00.02 / 03 Dec 2013 : First public version
			# Parses options from command line correctly
			# Can process other directories other than .
			# Several output format
			# Performance optimization : output per 1000 files

---------------------------------- */


/* -------------------------------

TO-DO :

1. Do all "//fixme"

2. Implement the following options :

  --maxsizedigits            max file size digits to be shown. default = 15
  --outputinterval=COUNT     only print	every COUNT number of files. default = 1000

  -B, --ignore-backups       do not list implied entries ending with ~


  -i, --inode                print the index number of each file
  -I, --ignore=PATTERN       do not list implied entries matching shell PATTERN

  -m                         fill width with a comma separated list of entries

  -n, --numeric-uid-gid      like -l, but list numeric user and group IDs

  -o                         like -l, but do not list group information

  -p, --indicator-style=slash
                             append / indicator to directories


  -Q, --quote-name           enclose entry names in double quotes
      --quoting-style=WORD   use quoting style WORD for entry names:
                               literal, locale, shell, shell-always, c, escape

  -R, --recursive            list subdirectories recursively

  -s, --size                 print the allocated size of each file, in blocks

---------------------------------- */

?>

