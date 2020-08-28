<?php
  /**********************************************************************************************************
   **   File: PiHQINDI.php                                                                                 **
   **   Author: Rob Musquetier (rob.musquetier@itcap.nl) for Raspberry Pi version 3 Camera ASCOM driver    **
   **   Forked by: Gord Tulloch (gord.tulloch@gmail.com)                                                   **
   **   Thanks to Rob for allowing use of his code in this project                                         **
   **********************************************************************************************************/
   
    $type 		= "jpg";	// Default type to create jpg files
	$debug 		= false;	// Default debug is switched off
	$doit  		= true;		// Default doit is switched on

	$debug		= isset($_REQUEST['debug']) ? validate_boolean($_REQUEST['debug']) : $debug;		// Dump Unix commands on command line interface (for debugging purposes only)
	$doit		= isset($_REQUEST['doit']) ? validate_boolean($_REQUEST['doit'])     : $doit;    	// Flag to prevent Unix cammand to be executed (for debugging purposes only)

	$type		= isset($_REQUEST['type'])			? validate_value($_REQUEST['type'], "jpg,png,gif,bmp") : $type;    	// Desired file type

	$exposure	= isset($_REQUEST['exposure']) ? validate_value(strtolower($_REQUEST['exposure']), "auto,night,nightpreview,off,verylong,fireworks") : "off"; 	// Default off
	$analog_gain = isset($_REQUEST['analog_gain'])	? validate_number($_REQUEST['analog_gain']) 														: 1;			// Default 1
	$flicker	= isset($_REQUEST['flicker'])		? validate_value($_REQUEST['flicker'], "off,auto,50hz,60hz")										: "off";		// Default flicker mode off
	$awb		= isset($_REQUEST['awb'])			? validate_value(strtolower($_REQUEST['awb']), "off,auto,sun,cloud,shade,tungsten,fluorescent,incandescent,flash,horizon,greyworld") : "auto";	// Default auto wide balance
	$hflip		= isset($_REQUEST['hflip'])			? validate_boolean($_REQUEST['hflip'])																: false;		// Default horizontab flip off
	$vflip		= isset($_REQUEST['vflip'])			? validate_boolean($_REQUEST['vflip'])																: false;		// Default vertical flip off
	$roi_x		= isset($_REQUEST['roi_x'])			? validate_number($_REQUEST['roi_x'])																: -1;			// Default disabled
	$roi_y		= isset($_REQUEST['roi_y'])			? validate_number($_REQUEST['roi_y'])																: -1;      		// Default disabled
	$roi_w		= isset($_REQUEST['roi_w'])			? validate_number($_REQUEST['roi_w'])																: -1;			// Default disabled
	$roi_h		= isset($_REQUEST['roi_h'])			? validate_number($_REQUEST['roi_h'])																: -1;			// Default disabled
	$shutter	= isset($_REQUEST['shutter'])		? validate_number(round($_REQUEST['shutter']))														: 1000000;		// Default 1 seconts
	$drc		= isset($_REQUEST['drc'])			? validate_value(strtolower($_REQUEST['drc']), "off,low,med,high")									: "off";		// Default dynamic range control switched off
	$ag			= isset($_REQUEST['ag'])			? validate_number(round($_REQUEST['ag'] * 10) / 10)													: 1.0;			// Default analog gain on 1
	$dg			= isset($_REQUEST['dg'])			? validate_number(round($_REQUEST['dg'] * 10) / 10)													: 1.0;			// Default digital gain on 1
	$binning	= isset($_REQUEST['binning'])		? validate_value(round($_REQUEST['binning']), "1,2,3,4")											: 1;			// Default on maximum resolution (4056 xc 3040)
	$annotate	= isset($_REQUEST['annotate'])		? validate_number(round($_REQUEST['annotate']))														: 0;			// Default 0 (no text)
	$timeout	= isset($_REQUEST['timeout'])		? validate_number(round($_REQUEST['timeout']))														: 100;			// Default 0.1 sec timeout
	$verbose    = isset($_REQUEST['verbose'])		? validate_boolean($_REQUEST['verbose'])															: false;		// Default debug info default off
	$convert    = isset($_REQUEST['convert'])		? validate_boolean($_REQUEST['convert'])															: false;		// Convert to FITS default off

	// Remove previously created image and log files create longer than 10 minute ago
	echo exec("find *.bmp -mmin +1 -type f -exec rm {} \;");
	echo exec("find *.jpg -mmin +1 -type f -exec rm {} \;");
	echo exec("find *.gif -mmin +1 -type f -exec rm {} \;");
	echo exec("find *.png -mmin +1 -type f -exec rm {} \;");
	echo exec("find *.log -mmin +15 -type f -exec rm {} \;");
	echo exec("find *.*~ mmin +1 -type f -exec rm {} \;");

	// Sensor width in pixels
	$sensor_width = 4056;

	// Sensor height in pixels
	$sensor_height = 3040;

	// Set shutter speed
	// Limit shutter speed to minimal 0.000001 sec
	if ($shutter < 1)
		$shutter = 1;			// Lower limit to 0 seconds

	// Limit shutter speed to 239 secs
	if ($shutter > 239000000)
		$shutter = 239000000;	// Upper limit to 239 seconds

	if ($analog_gain < 1)
		$analog_gain = 1;

	if ($analog_gain > 16)
		$analog_gain = 16;

	// Create filename from selected ISO gain, exposure time and date/time stamp
	$filename = "image_".$analog_gain."_".$shutter."_".$binning."x".$binning."_".date("Ymd")."T".date("His");

	// Open log file
	$f = fopen ($filename.".log", "w+");

	// Open en write selected details from the URL to the log file
	$q = 0;
	$qo = -1;
	// Retrieve URL from character position 16 onwards
	$p = substr($_SERVER['REQUEST_URI'], 16);

	// Cycle through all give parameters (seperated by & character)
	while ($q = strpos($p, "&", $q + 1)) {

		// Write parameter and the passed value, replace equal character (=) with a colon (:)
		fwrite($f, str_replace("=", ": ", substr($p, $qo + 1, ($q - $qo - 1)))."\n");

		// Save position of the & found in the string
		$qo = $q;
	}
	// Call raspistill and ensures nopreview option is selected, flicker control is switched off and auto white balance is switched off
//	$command  = ".userland/build/bin/raspistill --nopreview --metering matrix --thumb none ";
	$command  = "/usr/bin/raspistill --nopreview --metering average --thumb none ";

	// Add file type (jpg, png or bmp) to the command string
	// Check for jpg value
	if ($type=="jpg")

		// Set jpg file in the raspistill command string
		$command  .= "--raw --quality 100 --output ".$filename.".jpg ";

	// Other file formats (bmp, gif and png)
	else
		// Add bmp, gif or png file type encoding tot he raspistill command string
		$command  .= "--encoding ".$type." --output ".$filename.".".$type." --burst ";

	// Add header labels to the command string
	$command .= "--exif 'INSTRUME=Raspberry Pi Camera V3' --exif 'DATE-LOC=".date("Y-m-d")."T".date("H:i:s")."' --exif 'IMAGETYP=LIGHT' --exif 'EXPOSURE=".($shutter / 1000000)."' --exif 'HFLIPPED=".$hflip."' --exif 'VFLIPPED=".$vflip."' --exif 'GAIN=".$iso."' --exif 'FILTER=RGB' ";

	// Set ISO value
	//$command .= "--ISO ".$iso." ";

	// Set exposure type
	$command .= "--exposure ".$exposure." ";
	//$command .= "--exposure off ";

	// Set flicker mode
	$command .= "--flicker ".$flicker." ";

	// Set auto white balance
	if ($awb!='off')
		$command .= "--awb ".$awb." ";
	else
		// Set manual white balance (R = R * 3.3, B = B * 1.53
		$command .= "--awb off --awbgains 2,1.53 ";

	// Horizontal flip
	if ($hflip)
		$command .= "--hflip ";

	// Vertical flip
	if ($vflip)
		$command .= "--vflip ";

	// Set region of interest
	if ($roi_x!=-1 and $roi_y!=-1 and $roi_w!=-1 and $roi_h!=-1) {
		// Detemine new width
		$w = round($roi_w / $sensor_width * 100) / 100;

		// Ensure width is between 0 and 1 (0 and 100% of the sensor width)
		if ($w < 0 or $w > 1)
			$w = 1;

		// Determine new height
		$h = round($roi_h / $sensor_height * 100) / 100;

		// Ensure height is between 0 and 1 (0 and 100% of the sensor height )
		if ($h < 0 or $h > 1)
			$h = 1;

		// Add region of interest to the command string
		$command .= "--roi ".$x.",".$y.",".$w.",".$h." ";
	}

	// Add shutter speed to raspistill command string

	// for higher then 5 seconds exposures use the extra -lss option to prevent long waiting times
/*
	if ($shutter > 5000000) {
		$command .= "-lss ".$shutter." ";
		$command .= "-ss ".round($shutter / 100)." ";
	}
	else
*/
		$command .= "--shutter ".$shutter." ";

	// Set dynamic range control
	$command .= "--drc ".$drc." ";

	// Set analog gain

	// Ensure analog gain is at least set to 1
	// Add analog gain setting to command string
	$command .= "--analoggain ".$analog_gain." ";

	// Set digital gain
/*
	// Ensure analog gain is at least set to 1
	if ($dg < 1.0)
		$dg = 1.0;				// Lower limit to 1

	// Ensure analog gain is at most set to 255
	if ($dg > 255.0)
		$dg = 255.0;			// Upper limit to 255

	// Add digital gain setting to command string
	$command .= "--digitalgain ".$dg." ";
*/
	// Set camera mode (resolution)

	// Ensure binning is at least set to 1
	if ($binning < 1)
		$binning = 1;				// Lower limit to 1

	// Ensure binning is at most set to 3
	if ($binning > 4)
		$binning = 4;				// Upper limit to 4

	// Translate the desired binning size to the right camera mode

	// Check if binning 1 is selected
	if ($binning==1)

		// Select binning 1 (4060 x 3056 pixels)
		$roi = "";

	// Check if binning 2 is selected
	elseif ($binning==2)

		// Select binning 2 (2028 x 1520 pixels)
		$roi = "--roi 0.25,0.25,0.5,0.5 ";

	// Check if binning 3 is selected
	elseif ($binning==3)

		// Select binning 3 (1352 x 1013 pixels)
		$roi = "--roi 0.333333,0.333333,0.333333,0.333333 ";

	// Binning 3 is selected
	else

		// Select binning 4 (1014 x 764 pixels)
		$roi = "--roi 0.375,0.375,0.25,0.25 ";

	// Add requered camera mode (for the desired binning factor) to the raspistill command string
	$command .= "--mode 3 ";
	$command .= $roi;

	// Set annotation text
	if ($annotate)
		$command .= "--annotate ".$annotate." -ae 32,0xff,0x808000 ";	// Default to size 32 white text on black background

	$timeout = round($shutter / 20000);

	// Ensure timeout is at least set to 0.1 sec
	if ($timeout < 100)
		$timeout = 100;				// Lower limit to 0.1 seconds (unforenately 0 secs causes raspistill to crash)

	// Ensure timeout is at most set to 15 sec
	if ($timeout > 15000)
		$timeout = 15000;			// Upper limit to 15 seconds

	// Add timeout value to the raspistill command prompt
	$command .= "--timeout ".$timeout." ";

	// Set debug info
	if ($verbose)
		$command .= "--verbose ";

	$command .= "--digitalgain 1 --stats --burst 2>&1 | tee raspilog.txt\n";

	// Show command on the command interface when debug parameter is true or not 0
	if ($debug)
		echo "			MESSAGE: executing command: ".$command."<br>\n";

// time userland/build/bin/raspistill --nopreview --metering matrix --thumb none --encoding png
// --output image_400_10000000_1x1_20200519T145218.png --ISO 400 --exposure night --flicker off
// --awb auto -lss 10000000 -ss 100000 --drc off --mode 3 --timeout 100 --burst -v
// 2>&1 | perl -pe 'use POSIX strftime; print strftime "[%Y-%m-%d %H:%M:%S] ", localtime'

	// Write raspistill command string to the log file
	fwrite($f, "\n".$command." n\n");

	// Close the log file
	fclose ($f);

	// Check if Unix command should be executed
	if ($doit)
		// Execute raspistill command
		echo exec($command);

	// Change permissions on the file
	$ecommand = "chmod 660 ".$filename.".".$type;

	// Show command on the command interface when debug parameter is true or not 0
	if ($debug)
		echo "			MESSAGE: executing command: ".$ecommand."<br>\n";

	// Check if Unix command should be executed
	if ($doit)
		// Execute change permission command
		echo exec($ecommand);

	// Check if image needs to vbe converted to fits
	if ($convert) {
		// Convert using imagemagick convert function
		$ecommand = "convert ".$filename.".".$type." ".$filename.".fits";

		// Show command on the command interface when debug parameter is true or not 0
		if ($debug)
			echo "			MESSAGE: executing command: ".$ecommand."<br>\n";

		// Check if Unix command should be executed
		if ($doit)
			// Excute convert to fits commandt
			echo exec($ecommand);

		// Change permissions on fits file
		$ecommand = "chmod 660 ".$filename.".fits";

		// Show command on the command interface when debug parameter is true or not 0
		if ($debug)
			echo "			MESSAGE: executing command: ".$ecommand."<br>\n";

		// Check if Unix command should be executed
		if ($doit)
			// Execute change permission command
			echo exec($ecommand);
	}

	// Show file name on the command interface when debug parameter is not false or 0
	if (!$debug)
		echo $filename.".".$type."<br/>\n".$command;

	// Otherwise show image on the screen
	else
		echo "<img src='".$filename.".".$type."' width='676' height='506' />\n".$command."<br/>\n";

	// Function to validate input variable $v (it should consist of y, n, Y, N , 0, 1, true or false)
	function validate_boolean($v) {

		// Chop of spaces at the start and end of the string
		$v = trim($v);

		// Check for permitted input values
		if (!$v or $v=="0" or $v=="1" or strtolower($v)=="true" or strtolower($v)=="false" or strtoupper($v)=="N" or strtoupper($v)=="Y")
			// Check if value false
			if (!$v or strtolower($v)=="false")
				// Return false value
				return false;
			else
				// Return true value
				return true;
		else
			// Return false value
			return false;
	}

	// Check if input variable $v consists of numbers or a point (.) or a minus (-) sign
	function validate_number($v) {

		// Set error flag
		$err = false;

		// Chop of spaces at the start and end of the string
		$v = trim($v);

		// Check if input variable is 0
		if ($v=="0")
			// Return the varaible value;
			return $v;

		// Cycle through all the entered characters
		for ($i = 0; $i < strlen($v); $i++)
			// Check for permitted input values
			if (!($v[$i]== "-" and $i==0) and ($v[$i] < "0" or $v[$i] > "9") and $v[$i]!=".")
				// Flag the input variable as not compliant
				$err = true;

		// Check if input variable was valid
		if ($err)
			// Return 0 value
			return 0;

		// Input variable was valid
		else
			// Return checked value
			return $v;
	}

	// Check if input variable $v consists of the possible values passed in the value parameter
	function validate_value($v, $options) {

		// Set error flag
		$err = false;

		// Chop of spaces at the start and end of the string
		$v = trim($v);

		// Determin position in possible value string to start searching
		$p = $q = -1;

		// Reset value string position counter
		$to = 0;

		// Cycle through possible options string (delimited by comma's), a maximum 50 options is allowed
		while ($p = strpos($options, ",", $p + 1) and $to < 50) {

			// Retrieve possible option and trim of trailing and leading spaces
			$t = trim(substr($options, $q + 1, $p - $q - 1));

			// Save the position where "," was found
			$q = $p;

			// Increase option couter
			$to++;

			// Check if input variable is one of the found allowed options
			if ($t==$v)
				// Return the input variable
				return $v;
		}

		// Get last possible allowed option
		$l = trim(substr($options, $q + 1));

		// Check if input variable is one of the found allowed options
		if ($l==$v)

			// Return the input variable
			return $v;

		// Option similar to the input varaible could not be found in the allowed options variable $options
		else

			// Illiega option fouund
			$err = true;

		// Cehck if an illigal variable was givven
		if ($v and $err)

			// Return false
			return false;

		// Legal option was given
		else

			// Return the allowed variable value
			return $v;
	}
