<?php
/**
 * A class for basic DNS queries.
 * 
 * SPDX-License-Identifier: MIT
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright 2023 Malte Bublitz
 * @author Malte Bublitz <malte70@rt3x.de>
 */


/**
 * DNS Query util
 *
 * @property string $hostname         Hostname (or IPv4) to resolve
 * @property string $hostname_query   The hostname we actually resolve. For PTR lookups, this is a .in-addr.arpa address.
 * @property string $type             DNS record type
 * @property int    $type_query       The DNS_* constant related to $type
 * @property array  $dns_record_types An associative array mapping human readable record names (like"PTR")
 *                                    to the DNS_* constants we really need.
 */
class DNSQuery {
	protected $hostname;
	protected $hostname_query;
	protected $type;
	protected $type_query;
	protected $dns_record_types;
	
	public function __construct($hostname, $type, $dns_record_types) {
		$this->setDNSRecordTypes($dns_record_types);
		$this->setHostname($hostname);
		$this->setType($type);
	}
	
	public function setHostname($hostname) {
		if (strlen($hostname) > 0) {
			$this->hostname = $hostname;
			$this->hostname_query = $hostname;
			return true;
		}
		return true;
	}
	
	public function setType($type) {
		if (is_string($type)) {
			$type       = strtoupper($type);
			
			if (is_array($this->dns_record_types) && !array_key_exists($type, $this->dns_record_types)) {
				return false;
			}
			
			$this->type = $type;
			return true;
		}
		return false;
	}
	
	public function setDNSRecordTypes($dns_record_types) {
		if (is_array($dns_record_types) && count($dns_record_types) > 0) {
			$this->dns_record_types = $dns_record_types;
			return true;
		}
		return false;
	}
	
	/**
	 * Get the value of the $hostname property. 
	 *
	 * If $this->hostname is empty, it's set to a default fallback value.
	 *
	 * @return string
	 */
	public function getHostname() {
		if (empty($this->hostname)) {
		   $this->hostname = $_SERVER["REMOTE_ADDR"];
		}
		return $this->hostname;
	}
	
	public function getHostnameQuery() {
		return $this->hostname_query;
	}
	
	public function getType() {
		if (!is_string($this->type) || empty($this->type)) {
			$this->type = "PTR";
		}
		print $this->type;
		return $this->type;
	}
	
	public function getTypeQuery() {
		//if (!is_int($this->type_query) && is_int($this->type)) {
		if (!is_int($this->type_query)) {
			$this->type_query = $this->dns_record_types[$this->type];
		}
		return $this->type_query;
	}

	/**
	 * Return the list of DNS record types matching a human readable
	 * name to the DNS_* constant.
	 * 
	 * Returns a fallback array containing only the PTR record, so
	 * setting up $dns_record_types before is not needed.
	 *
	 * @return array
	 */
	public function getDNSRecordTypes() {
		$dns_record_types = $this->dns_record_types;
		if (!is_array($this->dns_record_types)) {
			$this->setDNSRecordTypes(
				Array(
					"A" => DNS_A,
					"PTR" => DNS_PTR
				)
			);
		}
		return $this->dns_record_types;
	}
	
	/**
	 * Prepare $hostname and $hostname_query for PTR queries.
	 * 
	 * $hostname should contain an IP address, and $hostname_query
	 * the ".in-addr.arpa" or "ip6.arpa" domain.
	 */
	private function prepareHostnameForPTR() {
		$domain_ipv4 = ".in-addr.arpa";
		$domain_ipv6 = ".ip6.arpa";
		
		if ($this->getType() != "PTR") {
			return false;
		}
		
		$hostname = $this->getHostname();
		
		if (filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPv4)) {
			// IPv4 given
			$hostname_query = implode(".", array_reverse(explode(".", $this->getHostname()))) . $domain_ipv4;
			
		} elseif(filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPv6)) {
			// IPv6 given
			// @see https://itecnote.com/tecnote/php-convert-ipv6-to-nibble-format-for-ptr-records/
			$addr           = inet_pton($hostname);
			$unpack         = unpack('H*hex', $addr);
			$hex            = $unpack['hex'];
			$hostname_query = implode('.', array_reverse(str_split($hex))) . $domain_ipv6;
			
		} elseif(substr($hostname, -1*strlen($domain_ipv4)) == $domain_ipv4) {
			// .in-addr.arpa domain given
			$hostname_query = $hostname;
			$hostname = implode(
				".",
				array_reverse(
					explode(
						".",
						substr($hostname, 0, strlen($domain_ipv4))
					)
				)
			);
			
		} elseif(substr($hostname, -1*strlen($domain_ipv6)) == $domain_ipv6) {
			/*
			 * .ip6.arpa domain given
			 * TODO: implement this!
			 * 
			 * @see https://github.com/dsp/v6tools/blob/master/src/v6tools/IPv6Address.php
			 * @see https://github.com/mlocati/ip-lib
			 */
			throw new Exception("Converting an ip6.arpa domain to IPv6 address is not implemented yet!");
			//$hostname_query = $hostname;
			//$ip6 = array_reverse(explode(".", substr($hostname_query, 0, -1*strlen($domain_ipv6))));
			
		}
	}

	/**
	 * Do the DNS query and return the result.
	 *
	 * @return array
	 * */
	public function resolve() {
		return dns_get_record(
			$this->getHostnameQuery(),
			$this->getTypeQuery()
		);
	}
}


