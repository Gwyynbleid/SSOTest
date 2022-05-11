<?php
//CIC applications main configuration file

//configs is deprecated
$configs = [
    "openidconnect" => [
        "authorize_url" => "https://test-login.kyndryl.net/oidc/endpoint/default/authorize", //Given url ending with /authorize
        "token_url" => "https://test-login.kyndryl.net/oidc/endpoint/default/token", //Given url ending with /token
        "introspect_url" => "https://test-login.kyndryl.net/oidc/endpoint/default/introspect", //Given url ending with /introspect
        "client_id" => "NTI1MjBlZjItOTRiMS00", //Given Client ID
        "client_secret" => "ZjUxYzg0YmItMjlhNS00", //Given Client Secret
        "redirect_url" => "https://9.128.10.60/SSOTest/auth", //Your approved redirect url
        "use_proxy" => false, //set if proxy is required to establish connection with SSO provider
        "proxy_server" => ""
    ]
];


