<?php
/**
 * DNS tools configuration
 * 
 * @url https://app.malte70.de/dns-tools/
 */



/**
 * Record-Typen:
 *
 * Mapping Formular-Feld => Konstante für dns_get_record()
 */
$DNS_RECORD_TYPES = Array(
	"A"     => DNS_A,
	"AAAA"  => DNS_AAAA,
	"CNAME" => DNS_CNAME,
	//"HINFO" => DNS_HINFO,
	"CAA"   => DNS_CAA,
	"MX"    => DNS_MX,
	"NS"    => DNS_NS,
	"PTR"   => DNS_PTR,
	//"SOA"   => DNS_SOA,
	"TXT"   => DNS_TXT,
	"SRV"   => DNS_SRV
);



/**
 * List of DNS aervers
 * 
 * TODO: NOT IMPLEMENTED YET!
 */
$DNS_SERVERS = Array(
	"185.95.218.43" => "Digitale Gesellschaft (CH)",
	"8.8.8.8"       => "Google Public DNS",
	"1.1.1.1"       => "Cloudflare DNS",
	"94.247.43.254" => "OpenNIC",
	"84.200.69.80"  => "DNSWatch",
);



/**
 * Site branding
 */
$Branding = Array(
	"Lang"        => "de",
	"SiteTitle"   => "DNS-Abfrage-Tools",
	"Generator"   => "malte70/dns-tools",
	"Author"      => "Malte Bublitz",
	"Favicon"     => Array(
		"Type" => "image/png",
		"Size" => "512x512",
		"URL"  => "https://xyz.malte70.de/img/icons_tango/addressbook-512.png",
	),
	"Stylesheet"  => "style.css",
	"HeaderLink"  => "/",
	"HeaderTitle" => "DNS-Abfrage",
	//"Favicon" => "",
	//"Favicon" => "",
);

