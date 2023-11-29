<?php
/**
 * PHP-basierter DNs-Client
 * 
 * SPDX-License-Identifier: BSD-2-Clause
 * 
 * - https://www.php.net/manual/en/function.dns-get-record.php
 * - https://pear.php.net/package/Net_DNS2
 * - 
 */


require_once("config.inc.php");
require_once("dnsquery.class.php");

ini_set("display_errors", true);
error_reporting(E_ALL);



// $hostname  = "";
// $type      = "";
$hostname  = $_SERVER["REMOTE_ADDR"];
$type      = "PTR";

// $dnsserver = "";


/**
 * HTTP POST => DNS-Abfrage durchführen
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$hostname  = @$_POST["hostname"];
	$type      = @$_POST["type"];
	//$dnsserver = $_POST["dnsserver"];
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
	$hostname  = "_telnet._tcp.rt3x.de";
	$type      = "SRV";
}

//if (empty($hostname) || !array_key_exists($type, $DNS_RECORD_TYPES) || !array_key_exists($dnsserver, $DNS_SERVERS)) {
if (empty($hostname) || !array_key_exists($type, $DNS_RECORD_TYPES)) {
	header("Content-Type: text/plain; charset=UTF-8");
	//print_r($_POST);
	die("\nWrong parameters");
}

$query = new DNSQuery($hostname, $type, $DNS_RECORD_TYPES);
$result = $query->resolve();

if (true) {
} else {
	$result = NULL;
	
}

//header("Content-Type: text/plain; charset=UTF-8");
//print_r($result);

?><!DOCTYPE html>
<html lang="<?=$Branding["Lang"]?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title><?=$Branding["SiteTitle"]?></title>
		
<?php if (!empty($Branding["Generator"])): ?>
		<meta name="generator" content="<?=$Branding["Generator"]?>">
<?php endif; ?>
<?php if (!empty($Branding["Author"])): ?>
		<meta name="author" content="<?=$Branding["Author"]?>">
<?php endif; ?>
		<link rel="stylesheet" href="<?=$Branding["Stylesheet"]?>">
<?php if (is_array($Branding["Favicon"])): ?>
		<link rel="icon" type="<?=$Branding["Favicon"]["Type"]?>" sizes="<?=$Branding["Favicon"]["Size"]?>" href="<?=$Branding["Favicon"]["URL"]?>">
<?php endif; ?>
	</head>
	<body id="top">
		<header>
			<h1>
				<a href="<?=$Branding["HeaderLink"]?>"><?=$Branding["HeaderTitle"]?></a>
			</h1>
		</header>
		
		<main>
			<section id="form">
				<form action="./" method="POST">
					<div class="input-group">
						<label for="hostname">Hostname</label>
						<input type="text" name="hostname"<?=(empty($hostname) ? "" : ' value="'.$hostname.'"')?>>
					</div>
					<div class="input-group">
						<label for="type">Record-Typ</label>
						<select name="type">
<?php foreach ($DNS_RECORD_TYPES as $k => $v): ?>
							<option value="<?=$k?>"<?=($k==$type ? " selected" : "")?>><?=$k?></option>
<?php endforeach; ?>
						</select>
					</div>
					<!--
					<div class="input-group">
						<label for="dnsserver">DNS-Server</label>
						<select name="dnsserver">
<?php foreach ($DNS_SERVERS as $k => $v): ?>
							<option value="<?=$k?>"><?=$k?> (<?=$v?>)</option>
<?php endforeach; ?>
						</select>
					</div>
					-->
					<div class="input-group">
						<button type="submit">Abschicken</button>
					</div>
			</section>
			
<?php if (is_array($result)): ?>
			<section id="result">
				<h2>Ergebnis der Abfrage</h2>
<?php if (count($result) < 1): ?>
				<code>
					NXDOMAIN
				</code>
<?php else: ?>
<!--
Return value of dns_get_record():

<?php print_r($result); ?>

-->
				<code>
<?php
foreach ($result as $r) {
	print "\t\t\t\t\t<pre>";
	print $r["host"] . ".\t ";
	
	for ($i = (7-strlen($r["ttl"])); $i<0; $i++) {
		print " ";
	}
	print $r["ttl"] . " \t" . $r["class"] . " \t";
	
	for ($i = (5-strlen($r["type"])); $i<0; $i++) {
		print " ";
	}
	print $r["type"] . " \t";
	
	error_reporting(E_ERROR);
	
	switch ($r["type"]) {
		case "A":
			print $r["ip"];
			break;
			
		case "AAAA":
			print $r["ipv6"];
			break;
			
		case "CAA":
			print $r["flags"] . " " . $r["tag"] . " " . $r["value"];
			break;
			
		case "NS":
		case "PTR":
		case "CNAME":
			print $r["target"];
			break;
			
		case "MX":
			print $r["pri"] . " " . $r["target"];
			break;
			
		case "TXT":
			print $r["txt"];
			break;
			
		case "SRV":
			print $r["prio"]." ".$r["weight"]." ".$r["port"]." ".$r["target"];
			break;
			
		case "HINFO":
			print "cpu:" . $r["cpu"] . ", os:" .  $r["os"];
			break;
			
	}
	print "</pre><br>";
}
?>
				</code>
<?php endif; ?>
			</section>
<?php endif; ?>
		</main>
		
		<footer>
			<p>
				&copy; 2023 <a href="https://malte70.de" rel="me nofollow">malte70</a>
			</p>
		</footer>
	</body>
</html>
