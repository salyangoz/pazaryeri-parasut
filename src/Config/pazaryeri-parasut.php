<?php

// Todo: İsimlen yeniden düzenlenecek

return [
    "n11_app_key"                       =>  env('N11_APP_KEY'),
    "n11_app_secret"                    =>  env('N11_APP_SECRET'),
    'n11_base_url'                      =>  "https://api.n11.com/ws/",
    'parasut_client_id'                 =>  env('PARASUT_CLIENT_ID'),
    'parasut_client_secret'             =>  env('PARASUT_CLIENT_SECRET'),
    'parasut_username'                  =>  env('PARASUT_USERNAME'),
    'parasut_password'                  =>  env('PARASUT_PASSWORD'),
    'parasut_company_id'                =>  env('PARASUT_COMPANY_ID'),
    'parasut_category_id'               =>  env('PARASUT_CATEGORY_ID'),
    'parasut_account_id'                =>  env('PARASUT_ACCOUNT_ID'),
    'gittigidiyor_api_key'              =>  env('GITTIGIDIYOR_API_KEY'),
    'gittigidiyor_secret_key'           =>  env('GITTIGIDIYOR_SECRET_KEY'),
    'gittigidiyor_username'             =>  env('GITTIGIDIYOR_USERNAME'),
    'gittigidiyor_password'             =>  env('GITTIGIDIYOR_PASSWORD'),
    'gittigidiyor_auth_user'            =>  env('GITTIGIDIYOR_AUTH_USER'),
    'gittigidiyor_auth_password'        =>  env('GITTIGIDIYOR_AUTH_PASSWORD'),
    'gittigidiyor_lang'                 =>  env('GITTIGIDIYOR_LANG'),
    'gittigidiyor_developer_base_url'   =>  'https://dev.gittigidiyor.com:8443/',
    'gittigidiyor_product_base_url'     =>  'http://dev.gittigidiyor.com:8080/',
	'hepsiburada_order_endpoint'		=>	'https://oms-external.hepsiburada.com/',
	'hepsiburada_username'				=>	env('HEPSIBURADA_USERNAME'),
	'hepsiburada_password'				=>	env('HEPSIBURADA_PASSWORD'),
	'hepsiburada_merchant_id'			=>	env('HEPSIBURADA_MERCHANT_ID'),
    'einvoice_payment_type'             =>  "ODEMEARACISI",
    'einvoice_vat_withholding_code'     =>  "350",

    'ses' => [
        'key'           => env('SES_KEY'),
        'secret'        => env('SES_SECRET'),
        'region'        => env('SES_REGION'),
    ],

    'mail' => [
        'from_email'   => env('MAIL_FROM_EMAIL'),
        'from_name'    => env('MAIL_FROM_NAME'),
        'cc_email'     => env('MAIL_CC_EMAIL')
    ],

    'aws'   =>  [
        'invoice_bucket'        => env('AWS_INVOICE_BUCKET'),
        'invoice_bucket_url'    => env('AWS_INVOICE_BUCKET_URL')
    ],

    'marketplace'   =>  [
        'name'  =>  env('MARKETPLACE_NAME')
    ]
];