<?php

/**
 * Identifiers
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Identifiers extends BaseIdentifiers
{
	
	public static function getURLService()
	{
		return array(
			"orcid"=>"https://orcid.org/",
			"grid"=>"https://www.grid.ac/institutes/",
			"wikidata"=>"https://www.wikidata.org/wiki/"
		);
	}
}