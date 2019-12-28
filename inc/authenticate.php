<?php
function authenticate($user, $password) {

    global $CONFIG;
    
    if(empty($user) || empty($password)) return false;

	$ldap = ldap_connect($CONFIG['ldap_uri']);
	ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
	ldap_set_option($ldap,LDAP_OPT_REFERRALS,0);
	// verify bind user
	$bind = @ldap_bind($ldap, $CONFIG['ldap_binddn'], $CONFIG['ldap_bindpw']) or exit("Unable to bind to LDAP server");
    if (!$bind) { return false; }
    // valid
    $filter = "(mail=".$user.")";
    $attr = array("dn");
    $result = ldap_search($ldap, $CONFIG['ldap_basedn'], $filter, $attr) or exit("Unable to search LDAP server");
    $entries = ldap_get_entries($ldap, $result);
    if ($entries['count']!=1) {
        ldap_unbind($ldap);
        return false;
    }

    // check authorization
    if (!in_array($user, $CONFIG['authorized_users'])) {
        ldap_unbind($ldap);
        return false;
    }

    $dn = $entries[0]['dn'];
    
	// authenticate user
	if(!@ldap_bind($ldap, $dn, $password)) {
        ldap_unbind($ldap);
        return false;
    }
    ldap_unbind($ldap);
    // establish session variables
    $_SESSION['user'] = $user;
    return true;
}
?>
