<?php

namespace LdapWrapper;

/**
 * Class LdapWrapper
 * @package LdapWrapper
 */
class LdapWrapper
{
	/**
	 * Ldap port
	 * 
	 * @var int
	 */
	private $_port;
	
	/**
	 * Ldap protocol version to be used
	 * 
	 * @var int
	 */
	private $_protocolVersion;
	
	/**
	 * bind link
	 * 
	 * @var resource
	 */
	private $_link;

	/**
	 * Connect Ldap server using (or not) binding user
	 * 
	 * @param string $hostname
	 * @param int $port
	 * @param array $bindParams
	 * @param int $protocolVersion
	 */
	public function __construct($hostname, $port = 389, $bindParams = [], $protocolVersion = 3)
	{
		$this->setPort($port);
		$this->setProtocolVersion($protocolVersion);
		$this->connect($hostname, $bindParams);
	}

	/**
	 * Port setter
	 * 
	 * @param int $port
	 */
	private function setPort($port)
	{
		$this->_port = $port;
	}

	/**
	 * Protocol version setter
	 * 
	 * @param int $version
	 */
	private function setProtocolVersion($version)
	{
		$this->_protocolVersion = $version;
	}

	/**
	 * Connect Ldap
	 * 
	 * @param array $bindParams
	 */
	private function connect($hostname, array $bindParams)
	{
		$params = $bindParams + [
			'bindDn' => null,
			'bindPassword' => null
		];
		$this->_link = ldap_connect($hostname, $this->_port);
		ldap_set_option($this->_link, LDAP_OPT_PROTOCOL_VERSION, $this->_protocolVersion);
		ldap_bind($this->_link, $params['bindDn'], $params['bindPassword']);
	}

	/**
	 * Ldap search method
	 * 
	 * @param string $baseDn
	 * @param string $filter
	 * @param array $options
	 * @return array
	 */
	public function search($baseDn, $filter, array $options = [])
	{
		extract($options + [
			'attributes' => [],
			'attrsonly' => null,
			'sizelimit' => null,
			'timelimit' => null,
			'deref' => null,
		]);
		$query = ldap_search($this->_link, $baseDn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref);
		return ldap_get_entries($this->_link, $query);
	}
}