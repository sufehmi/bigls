#!/usr/bin/php
<?php

/* ------------------------------------------------------
Version		: 1.0.3

Part of bigls 	: https://github.com/sufehmi/bigls

Description 	: this script will move millions of files, with the specified timestamps, in as little time as possible

		example : ./move-old-snapshot.php -f 20130101 -t 20130601
		
		Will move all files dates between 1 January 2013 to 1 June 2013, 
		from $sourcedir to $targetdir

		This is actually doable with bigls + 1 line bash commands, but, 
		not as fast as this script.

		example : to move all files created before 1 September 2013 :

		cd /mnt/tmp/tmp1 ; bigls.php | while read OUT ; do cond=20130901 ; x=`echo $OUT | cut -b 1-8` ; f=`echo $OUT | cut -b 10-255` ; if [ $cond -ge $x ]; then mv  $f /mnt/tmp/tmp2/ >> /tmp/files-moved.log ; fi ; done


License		: GPL v2
                  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
                  http://vlsm.org/etc/gpl-unofficial.id.html

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/

$sourcedir    = "/mnt/tmp/tmp1";
$targetdir    = "/mnt/tmp/tmp2";


// proses parameter
$fromdate = 0;
$opts = getopt("f:t:");
foreach (array_keys($opts) as $opt) switch ($opt) {

	// from date
	case 'f' : 
		$fromdate = $opts['f'];
		break;

	// to date
	case 't' : 
		$todate = $opts['t'];
		break;
};

// jika tidak ada parameter = keluar saja
if ($fromdate == 0) { echo "WARNING !!! this is a dangerous script !! DO NOT execute without understanding how it works !!"; exit;};

// interval cetak laporan proses
$interval1	= 5000;
// interval cetak laporan file yang dipindahkan
$interval2	= 1000;

$ctr 		= 0;
$totalctr	= 0;
$hasilctr	= 0;
$line		= '';

if ($handle = opendir($sourcedir)) {
	
	while (false !== ($entry = readdir($handle))) {
		
		// skip the 2 special entries ("." and "..")
		if ($entry != "." && $entry != "..") {

			// dapatkan tanggal file
                	$stat   = stat($sourcedir."/".$entry);
			$tgl	= date('Ymd', $stat['mtime']);
 
			if (($tgl >= $fromdate) && ($tgl <= $todate)) {

				// create output string
				$line .= $tgl . "-";
				$line .= $entry . " --> $targetdir \n";

				// move the file
				$perintah = "mv $sourcedir/$entry $targetdir/";
				exec($perintah, $output);

				// counter procesing
				$hasilctr++;
				if ($hasilctr > $interval2) {
					echo $line;
					$line = '';
					$hasilctr = 0;
				}

			}; // if (($tgl >= $fromtgl) && ($tgl <= $totgl)) {
		
		}; // if ($entry != "." && $entry != "..") 
	
		$ctr++;
		if ($ctr > $interval1) {
			$totalctr	= $totalctr + $interval1;
			$ctr		= 0;
			echo "\n === Processed : $totalctr \n";
		};

	};
	closedir($handle);
};

?>

