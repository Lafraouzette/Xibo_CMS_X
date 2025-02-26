<?php


// If you need to add custom configuration settings to the CMS settings.php file,
// this is the place to do it.

// For example, if you want to configure SAML authentication, you can add the
// required configuration here

/*
$authentication = new \Xibo\Middleware\SAMLAuthentication();
$samlSettings = [
    'workflow' => [
        // Enable/Disable Just-In-Time provisioning
        'jit' => true,
        // Attribute to identify the user
        'field_to_identify' => 'UserName',   // Alternatives: UserID, UserName or email
        // Default libraryQuota assigned to the created user by JIT
        'libraryQuota' => 1000,
        // Home Page
        'homePage' => 'icondashboard.view',
        // Enable/Disable Single Logout
        'slo' => true,
        // Attribute mapping between XIBO-CMS and the IdP
        'mapping' => [
            'UserID' => '',
            'usertypeid' => '',
            'UserName' => 'uid',
            'email' => 'mail',
        ],
        // Initial User Group
        'group' => 'Users',
        // Group Assignments
        'matchGroups' => [
            'enabled' => false,
            'attribute' =>  null,
            'extractionRegEx' => null,
        ],
    ],
    // Settings for the PHP-SAML toolkit.
    // See documentation: https://github.com/onelogin/php-saml#settings
    'strict' => false,
    'debug' => true,
    'idp' => [
        'entityId' => 'https://idp.example.com/simplesaml/saml2/idp/metadata.php',
        'singleSignOnService' => [
            'url' => 'http://idp.example.com/simplesaml/saml2/idp/SSOService.php',
        ],
        'singleLogoutService' => [
            'url' => 'http://idp.example.com/simplesaml/saml2/idp/SingleLogoutService.php',
        ],
        'x509cert' => '',
    ],
    'sp' => [
        'entityId' => 'http://xibo-cms.example.com/saml/metadata',
        'assertionConsumerService' => [
            'url' => 'http://xibo-cms.example.com/saml/acs',
        ],
        'singleLogoutService' => [
            'url' => 'http://xibo-cms.example.com/saml/sls',
        ],
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:emailAddress',
        'x509cert' => '',
        'privateKey' > '',
    ,
    'security' => [
        'nameIdEncrypted' => false,
        'authnRequestsSigned' => false,
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,
        'signMetadata' => false,
        'wantMessagesSigned' => false,
        'wantAssertionsSigned' => false,
        'wantAssertionsEncrypted' => false,
        'wantNameIdEncrypted' => false,
    ],
];
*/
