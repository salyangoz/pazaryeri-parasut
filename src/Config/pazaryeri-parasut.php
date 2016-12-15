<?php

return [
    "n11_app_key"                       =>  env('N11_APP_KEY'),
    "n11_app_secret"                    =>  env('N11_APP_SECRET'),
    'n11_base_url'                      =>  "https://api.n11.com/ws/", //Bunu env'ye bağlamadım çünkü kullanıcıdan kullanıcıya değişen bir değer değil. Değiştiğinde tüm kullanıcıların env güncellemesine gerek kalmaması için burada tanımladım.
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
	'hepsiburada_order_endpoint'		=>	'https://oms-external-sit.hepsiburada.com/',
	'hepsiburada_username'				=>	env('HEPSIBURADA_USERNAME'),
	'hepsiburada_password'				=>	env('HEPSIBURADA_PASSWORD'),
	'hepsiburada_merchant_id'			=>	env('HEPSIBURADA_MERCHANT_ID')
];