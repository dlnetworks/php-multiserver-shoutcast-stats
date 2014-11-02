<?php

$station_name = "Radio Station";

$refresh = "60";  // 0 to disable
$timeout = "5"; // timeout

$ip[1] = "127.0.0.1"; 
$port[1] = "80";
$sid[1] = "1";

$ip[2] = "127.0.0.1"; 
$port[2] = "8000";
$sid[2] = "1";

$ip[3] = "127.0.0.1";
$port[3] = "8030";
$sid[3] = "1";

$ip[4] = "127.0.0.1";
$port[4] = "8050";
$sid[4] = "1";


/* ----- End config ----- */

$servers = count($ip);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php
if ($refresh != "0") 
	{
	print "<meta http-equiv=\"refresh\" content=\"$refresh\">\n";
	}
print "<title>$station_name Stats</title>\n";
?>
<style type="text/css"> <!-- body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#FFF;margin:5px;background-color:#000}div{padding:4px;margin-bottom:5px;width:420px}div div{border:0;margin:5px 5px 0}h1{font-size:22px;color:#FFF;margin:2px}h2{font-size:14px;color:#FFF;margin:2px}p{margin:5px}a{color:#FFF;text-decoration:none}a:hover{color:#FFF}div.line{height:3px;font-size:1px;margin-top:0}.red{color:#C00;font-weight:700}.small{font-size:10px}--> </style></head>
<body><center>
<?php
$i = "1";
while($i<=$servers)
	{
	$fp = @fsockopen($ip[$i],$port[$i],$errno,$errstr,$timeout);
	if (!$fp) 
		{ 
		$listeners[$i] = "0";
		$msg[$i] = "<span class=\"red\">ERROR [Connection refused / Server down]</span>";
		$error[$i] = "1";
		} 
	else
		{ 
		fputs($fp, "GET /7.html?sid=$sid[$i] HTTP/1.0\r\nUser-Agent: Mozilla (The King Kong of Lawn Care)\r\n\r\n");
		while (!feof($fp)) 
			{
			$info = fgets($fp);
			}
		$info = str_replace('<HTML><meta http-equiv="Pragma" content="no-cache"></head><body>', "", $info);
		$info = str_replace('</body></html>', "", $info);
		$stats = explode(',', $info);
		if (empty($stats[1]) )
			{
			$listeners[$i] = "0";
			$msg[$i] = "<span class=\"red\">ERROR [There is no source connected]</span>";
			$error[$i] = "1";
			}
		else
			{
			if ($stats[1] == "1")
				{
				$song[$i] = $stats[6];
				$listeners[$i] = $stats[4];
				$max[$i] =  $stats[3];
				$bitrate[$i] = $stats[5];
				$peak[$i] = $stats[2];
				if ($stats[0] == $max[$i]) 
					{ 
					$msg[$i] .= "<span class=\"red\">";
					}
				$msg[$i] .= "<b>Listeners:</b> $listeners[$i] | <b>Peak:</b> $peak[$i] | <b>Max:</b> $max[$i] | <b>Bitrate:</b> $bitrate[$i] Kbps<br><b>Song:</b> $song[$i]";
				if ($stats[0] == $max[$i]) 
					{ 
					$msg[$i] .= "</span>";
					}
				$msg[$i] .= "\n<p>";
				}
			else
				{
				$listeners[$i] = "0";
				$msg[$i] = "<span class=\"red\">ERROR [Cannot get info from server]</span>";
				$error[$i] = "1";
				}
			}
		}
	$i++;
	}
$total_listeners = array_sum($listeners);
print "<div id=\"blu\">\n<div style=\"text-align: center;\">\n<h1>$total_listeners Listeners</h1>\n</div>\n</div>\n<div>\n<div>\n</div>\n</div>\n<div>\n";
$i = "1";
while($i<=$servers)
	{
  	  print "<div>\n";
if ($max[$i] > 0) 
	{
	$percentage = round(($listeners[$i] / $max[$i] * 100));
	$timesby = (300 / $max[$i]);
	$barlength = round(($listeners[$i] * "$timesby"));
	}
if ($error[$i] != "1") 
	{ ?>
    <table width="420"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="25%" align="center"><b><a href="http://<?php print $ip[$i] . ":" . $port[$i]; ?>" target="_blank"><?php print $ip[$i] . ":" . $port[$i]; ?></a></b>&nbsp;&nbsp;</td>
        <td width="75%" colspan="3" ><img src="<?php if ($percentage == "100") { print "red-"; } ?>bar.gif" width="<?php print $barlength ?>" height="12" alt="The server is at <?php print $percentage; ?>% capacity"></td>
      </tr>
      <tr>
        <td width="25%">&nbsp;</td>
        <td width="25%">0%</td>
        <td width="25%" align="center">50%</td>
        <td width="25%" align="right">100%</td>
      </tr>
    </table>
<?php
	}
else
	{ ?>
    <table width="420"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="25%" align="center"><b><a href="http://<?php print $ip[$i] . ":" . $port[$i]; ?>" target="_blank"><?php print $ip[$i] . ":" . $port[$i]; ?></a></b>&nbsp;&nbsp;</td>
        <td width="75%" colspan="3" bgcolor="#ffffff">&nbsp;</td>
      </tr>
      <tr>
        <td width="25%">&nbsp;</td>
        <td width="25%">0%</td>
        <td width="25%" align="center">50%</td>
        <td width="25%" align="right">100%</td>
      </tr>
    </table>
<?php
	}
print "    <p>$msg[$i]<br></p></div>\n  <div class=\"line\"> </div>\n";
	$i++;
	}
print "</div>\n";
$time_difference = "0"; // BST: 1 GMT: 0
$time_difference = ($time_difference * 60 * 60);
$time = date("h:ia", time() + $time_difference);
$date = date("jS F, Y", time() + 0);
print "<div>\n<div>\n<p><center><b>$date<br>$time</b></p></center>\n</div>\n</div>\n"; ?>
</center>
</body>
</html>
