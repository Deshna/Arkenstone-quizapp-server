<?php
function ldap_auth($ldap_id, $ldap_password){
                        $ds = ldap_connect("ldap.iitb.ac.in") or die("Connect");
                        if($ldap_id=='') die("Id");
                        if($ldap_password=='') die("Pass");
                                $sr = ldap_search($ds,"dc=iitb,dc=ac,dc=in","(uid=$ldap_id)");
                                $info = ldap_get_entries($ds, $sr);
                                $roll = $info[0]["employeenumber"][0];
                                $ldap_id = $info[0]['dn'];
                                if(@ldap_bind($ds,$ldap_id,$ldap_password)){
                                        return json_encode($info["0"]);
                                }
                                else
                                {
                                        return "Auth";
                                }

}
echo ldap_auth($_GET['user'],$_GET['pass']);
