<?php

/**
 * ldapException is thrown when an error occurs into the upSimpleLdap
 * plugin
 */

class ldapException extends sfException
{
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
	}
}