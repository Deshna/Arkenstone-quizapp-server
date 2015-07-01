<?php

function ldap_auth($ldap_id, $ldap_password){ 

                        $adServer = "ldap://daiictw2k.da-iict.org";
        
                        $ldap = ldap_connect($adServer);
                        $ldaprdn = 'da-iict' . "\\" . $ldap_id;
                        $ldap_pass= $ldap_password;
                        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

                        $bind = ldap_bind($ldap,$ldaprdn,$ldap_pass);
                        
                        
                        
                        if($bind){
                                /*$sr = ldap_search($ldap,"dc=da-iict,dc=org","cn=$ldap_id");
                                echo $sr;
                                $info = ldap_get_entries($ldap, $sr);*/
                                //$ldap_id = $info[0]['dn'];

                                return $ldap_id;

                        }
                        else
                        {
                                echo "FAILURE";
                                return "Auth";
                        }
                        /*
                        $adServer = "ldap://daiictw2k.da-iict.org";                   
                        
                        $ldap = ldap_connect($adServer);

                        echo $ldap;

                        
                        $ldaprdn = 'cn=testad,ou=Users,dc=daiictw2k,dc=da-iict,dc=org';
                        //$ldaprdn = 'da-iict' . "\\" . "testad";
                        $pass = "testad";
                        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

                        $bind = ldap_bind($ldap);
                        var_dump(ldap_error($bind));
                        if($bind)
                                echo "SUCCESS";

                        else
                                echo "FAILURE";
                        


                        /*$ds = ldap_connect("ldap.daiict.org:389") or die("Connect");
                        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
                        echo $ds;
                        if (ldap_set_option($ds,LDAP_OPT_PROTOCOL_VERSION,3))
                        {
                                echo "Using LDAP v3\n";
                        }else{
                                echo "Failed to set version to protocol 3";
                        }

                        if($ldap_id=='') die("Id");
                        if($ldap_password=='') die("Pass");

                                $ldap_id='uid=johnny,cn=Johnny Doe,ou=People,dc=ldap,dc=daiict,dc=org';
                                $sr = ldap_search($ds,"dc=daiict,dc=org","(uid=$ldap_id)");
                                $ans = ldap_bind($ds,$ldap_id,$ldap_password);
                                var_dump(ldap_error($ds));
                                $number_returned = ldap_count_entries($ds,$sr);
                                
                                echo $number_returned;

                                $info = ldap_get_entries($ds, $sr);
                                
                                $roll = $info[0]["employeenumber"][0];
                                $ldap_id = $info[0]['dn'];
                                
                                $ldap_id='uid=johnny,ou=People,dc=daiict,dc=org';
                                
                                if(@ldap_bind($ds,$ldap_id,$ldap_password)){
                                        return json_encode($info["0"]);
                                }
                                else
                                {
                                        return "Auth";
                                }*/

}
echo ldap_auth($_GET['user'],$_GET['pass']);
