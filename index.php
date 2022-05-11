<?php

/**
 *
 * Copyright MITRE 2012
 *
 * OpenIDConnectClient for PHP5
 * Author: Michael Jett <mjett@mitre.org>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 */

//require __DIR__ . '/vendor/autoload.php';
//
//use Jumbojett\OpenIDConnectClient;
//
//$oidc = new OpenIDConnectClient('https://test-login.kyndryl.net/oidc/endpoint/default/.well-known/openid-configuration',
//                                'NTI1MjBlZjItOTRiMS00',
//                                'ZjUxYzg0YmItMjlhNS00');
//$oidc->providerConfigParam(array('token_endpoint'=>'https://test-login.kyndryl.net/oidc/endpoint/default/token'));
////$oidc->addScope('Sitman UAT');
//
//
//
////Add username and password
//$oidc->addAuthParam(array('username'=>'artur.jankowski@kyndryl.com'));
//$oidc->addAuthParam(array('password'=>'<Password>'));
//
////Perform the auth and return the token (to validate check if the access_token property is there and a valid JWT) :
////$token = $oidc->requestResourceOwnerToken(TRUE)->access_token;


session_start();

/* Authorization */
require_once 'config.php';

if (!isset ($_SESSION['app']['serialnumber'])) { ?>

        <form action='auth/authorize.php' method='post' name='frm'>
        <input type='hidden' id='fullUrl' name='url' value=''></form>
        <script>
            document.getElementById("fullUrl").value = window.location;
            document.frm.submit();
        </script>

    <?php
}else{
    echo "Hello {$_SESSION['app']['firstName']} ";
	echo "{$_SESSION['app']['lastName']} :)<br>";
	echo "{$_SESSION['app']['emailAddress']} :)<br>";
	session_destroy();
}





?>

<html>
<head>
    <title>New Example OpenID Connect Client Use</title>
    <style>
        body {
            font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
        }
    </style>
</head>
<body>

    

</body>
</html>

