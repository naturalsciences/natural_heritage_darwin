<?php

/**
 * ldapAuth class
 */
class ldapAuth
{
  private $ds;
  private $base_user;

  /**
   * Constructor
   */
  public function __construct()
  {

    // Retrieve configuration
    $ldap_host = sfConfig::get('app_ldap_host', 'localhost');
    $ldap_port =sfConfig::get('app_ldap_port', 389);
    $ldap_user = sfConfig::get('app_ldap_user');
    $ldap_pass =  sfConfig::get('app_ldap_pass');
    $this->id_attr = sfConfig::get('app_ldap_attr_id','uid');
    $ldap_version = sfConfig::get('app_ldap_version', 3);
    $this->base_user = sfConfig::get('app_ldap_baseuser');
	
	
    if (is_array($ldap_host)) {
      foreach ($ldap_host as $host) {
        if (!$this->ds) $this->ds = ldap_connect($ldap_host[0], $ldap_port);
      }
    } else {
      $this->ds = ldap_connect($ldap_host, $ldap_port);
    }
    
    if (!$this->ds)
    {
      throw new ldapException("Unable to connect to LDAP server");
    }

    ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);
    $binded = ldap_bind($this->ds, $ldap_user, $ldap_pass);
    
    if (!$binded) {
      throw new ldapException("Unable to connect to LDAP server : authentication error");
    }
	
  }

  public function __destruct()
  {
    $this->close();
  }

  /**
   * Close connexion
   */
  public function close()
  {
    ldap_close($this->ds);
  }

  /**
   * Authenticate an user
   * @param $login user uid
   * @param $password user password
   * @return boolean
   */
  public function authenticate($login, $password)
  {
     
    // Search the user
    $sr = ldap_search($this->ds, $this->base_user, $this->id_attr."=$login");

    if (ldap_count_entries($this->ds, $sr) <= 0)
	{
        
     	 return false;
	}
    $entry = ldap_first_entry($this->ds, $sr);
    $dn = ldap_get_dn($this->ds, $entry);
    ldap_free_result($sr);

    if (is_string($dn) && !empty($dn)) {
      if (!ldap_bind($this->ds, $dn, $password)) {
	     
        return false;
      } else {
	  
        return true;
      }
    }
	
    return false;
  }

  /**
   * Update password for an user
   * @param $login user uid
   * @param $oldpass previous password
   * @param $newpass new password
   * @param $nocheck true to bypass previous password checking
   * @return boolean
   */
  public function updatePassword($login, $oldpass, $newpass) {
    $sr = ldap_search($this->ds, $this->base_user,  $this->id_attr."=$login");
    if (ldap_count_entries($this->ds, $sr) <= 0)
      return false;
    $entry = ldap_first_entry($this->ds, $sr);
    $dn = ldap_get_dn($this->ds, $entry);
    ldap_free_result($sr);
    if (is_string($dn) && !empty($dn)) {
      if (!@ldap_bind($this->ds, $dn, $oldpass)) {
        return false;
      } else {
        // Authentication OK
        if (ldap_mod_replace($this->ds, $dn,
            array('userPassword'=>"{MD5}".base64_encode(pack("H*",md5($newpass)))))) {
          return true;
        } else {
          return false;
        }
      }
    }
  }

  /**
   * Get attributes for a specified uid
   * @param $uid user uid
   * @param $values an array with the list of attributes to get
   * @return an array with the values, or false if an error occured
   */
  public function getAttributes($uid, $values)
  {
    if (!is_array($values)) {
      $values = array($values);
    }
    
    // Recherche de l'utilisateur
    $sr = ldap_search($this->ds, $this->base_user, $this->id_attr."=$uid");
    if (ldap_count_entries($this->ds, $sr) <= 0)
      return false;
    $entry = ldap_first_entry($this->ds, $sr);
    
    $attrib = ldap_get_attributes($this->ds, $entry);
    if ($attrib !== false) {
      $tab = array();
      foreach ($values as $val) {
        if (!isset($attrib[$val][0])) return false;
        $tab[$val] = $attrib[$val][0];
      }
      return $tab;
    } else return false;
  }

  /**
   * Check if there's a matching field=value pair into the whole
   * directory
   * @param $field name of the field
   * @param $value value
   * @return boolean
   */
  public function check($field, $value)
  {
    $sr = ldap_search($this->ds, $this->base_user, "$field=$value");
    if (ldap_count_entries($this->ds, $sr) <= 0)
      return false;
    else
      return true;
  }
}
