<?php
/**
 * SPDX-License-Identifier: MIT
 */


require_once("config.inc.php");
require_once("dnsquery.class.php");

ini_set("display_errors", true);
error_reporting(E_ALL);


$hostname  = "www.malte-bublitz.de";
$type      = "TXT";


$results = Array();
for ($i=0; $i<5; $i++) {
	$query = new DNSQuery($hostname, $type, $DNS_RECORD_TYPES);
	$query->setHostname(str_repeat("www.", $i+1) . $hostname);
	$results[] = $query->resolve()[0];
}


?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>DNS Easter Egg</title>
		
		<meta name="author" content="Malte Bublitt">
		<link rel="stylesheet" href="style.css">
		<link rel="icon" type="image/png" sizes="512x512" href="https://xyz.malte70.de/img/icons_tango/addressbook-512.png">
	</head>
	<body id="top">
		<header>
			<h1>
				<a href="./">DNS Easter Egg</a>
			</h1>
		</header>
		
		<main>
			<section id="easteregg">
				<h2>Easter egg hidden in TXT records of malte-bublitz.de</h2>
				<code>
<?php
foreach ($results as $r) {
	print "\t\t\t\t\t<h3>";
	print $r["host"];
	print "</h3>\n";
	print "\t\t\t\t\t<pre>";
	print "\n\t";
	
	print $r["txt"] . "</pre><br>";
	
}
?>
				</code>
			</section>
		</main>
		
		<footer>
			<p>
				&copy; <?=date("Y")?> <a href="https://malte70.de" rel="me nofollow">malte70</a>
			</p>
		</footer>
	</body>
</html>
