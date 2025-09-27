<?php
return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'cache' => [
        'graphql' => [
            'id_salt' => 'hXUg3nx4KPtQUKE9KHisdUJqn7n2RRFR'
        ],
        'frontend' => [
            'default' => [
                'id_prefix' => '221_'
            ],
            'page_cache' => [
                'id_prefix' => '221_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'config' => [
        'async' => 0
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'crypt' => [
        'key' => 'base641WpCLJXtf9PDXIf74yRYkaoPoDcL/d31roAV6AGLl3U='
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => '127.0.0.1:3306',
                'dbname' => 'magento2-ce',
                'username' => 'root',
                'password' => 'root',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'production',
    'session' => [
        'save' => 'files',
        'save_path' => '/tmp/magento_sessions'
    ],
    'lock' => [
        'provider' => 'db'
    ],
    'directories' => [
        'document_root_is_pub' => true
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'graphql_query_resolver_result' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1
    ],
    'downloadable_domains' => [
        'magento2-ce.local'
    ],
    'search' => [
        'engine' => 'elasticsearch8',
        'elasticsearch8_server_hostname' => 'elasticsearch',
        'elasticsearch8_server_port' => '9200',
        'elasticsearch8_index_prefix' => 'magento2',
        'elasticsearch8_timeout' => 15
    ],
    'install_options' => [
        'search-engine' => 'elasticsearch8',
        'elasticsearch-host' => 'elasticsearch',
        'elasticsearch-port' => '9200',
        'elasticsearch-index-prefix' => 'magento2',
        'elasticsearch-timeout' => '15'
    ],
    'install' => [
        'date' => 'Fri, 19 Sep 2025 19:42:22 +0000'
    ],
    'system' => [
        'default' => [
            'payment' => [
                'payflowpro' => [
                    'partner' => null,
                    'user' => null,
                    'pwd' => null,
                    'sandbox_flag' => '0',
                    'proxy_host' => null,
                    'proxy_port' => null,
                    'debug' => '0'
                ],
                'payflow_link' => [
                    'pwd' => null,
                    'sandbox_flag' => '0',
                    'use_proxy' => '0',
                    'proxy_host' => null,
                    'proxy_port' => null,
                    'debug' => '0',
                    'url_method' => 'GET'
                ],
                'payflow_express' => [
                    'debug' => '0'
                ],
                'paypal_express_bml' => [
                    'publisher_id' => null
                ],
                'paypal_express' => [
                    'debug' => '0',
                    'merchant_id' => null
                ],
                'hosted_pro' => [
                    'debug' => null
                ],
                'paypal_billing_agreement' => [
                    'debug' => '0'
                ],
                'braintree' => [
                    'merchant_id' => null,
                    'public_key' => null,
                    'private_key' => null,
                    'merchant_account_id' => null
                ],
                'braintree_paypal' => [
                    'merchant_name_override' => null
                ],
                'checkmo' => [
                    'mailing_address' => null
                ],
                'payflow_advanced' => [
                    'user' => null,
                    'pwd' => null,
                    'sandbox_flag' => '0',
                    'proxy_host' => null,
                    'proxy_port' => null,
                    'debug' => '0',
                    'url_method' => 'GET'
                ]
            ],
            'payment_all_paypal' => [
                'paypal_payflowpro' => [
                    'settings_paypal_payflow' => [
                        'heading_cc' => null,
                        'settings_paypal_payflow_advanced' => [
                            'paypal_payflow_settlement_report' => [
                                'heading_sftp' => null
                            ]
                        ]
                    ]
                ],
                'payflow_link' => [
                    'settings_payflow_link' => [
                        'settings_payflow_link_advanced' => [
                            'payflow_link_settlement_report' => [
                                'heading_sftp' => null
                            ]
                        ]
                    ]
                ],
                'payments_pro_hosted_solution' => [
                    'pphs_settings' => [
                        'pphs_settings_advanced' => [
                            'pphs_settlement_report' => [
                                'heading_sftp' => null
                            ]
                        ]
                    ]
                ],
                'express_checkout' => [
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null
                            ]
                        ]
                    ]
                ]
            ],
            'paypal' => [
                'fetch_reports' => [
                    'ftp_login' => null,
                    'ftp_password' => null,
                    'ftp_sandbox' => '0',
                    'ftp_ip' => null,
                    'ftp_path' => null
                ],
                'general' => [
                    'business_account' => null,
                    'merchant_country' => 'TH'
                ],
                'wpp' => [
                    'api_username' => null,
                    'api_password' => null,
                    'api_signature' => null,
                    'api_cert' => null,
                    'sandbox_flag' => '0',
                    'proxy_host' => null,
                    'proxy_port' => null
                ]
            ],
            'admin' => [
                'url' => [
                    'custom' => null,
                    'custom_path' => null
                ]
            ],
            'web' => [
                'unsecure' => [
                    'base_url' => 'http://magento2-ce.local/',
                    'base_link_url' => '{{unsecure_base_url}}',
                    'base_static_url' => null,
                    'base_media_url' => null
                ],
                'secure' => [
                    'base_url' => 'http://magento2-ce.local/',
                    'base_link_url' => '{{secure_base_url}}',
                    'base_static_url' => null,
                    'base_media_url' => null
                ],
                'default' => [
                    'front' => 'cms'
                ],
                'cookie' => [
                    'cookie_path' => null,
                    'cookie_domain' => null
                ]
            ],
            'catalog' => [
                'productalert_cron' => [
                    'error_email' => null
                ],
                'product_video' => [
                    'youtube_api_key' => null
                ],
                'search' => [
                    'elasticsearch8_server_hostname' => 'localhost',
                    'opensearch_server_hostname' => 'localhost',
                    'elasticsearch8_server_port' => '9200',
                    'opensearch_server_port' => '9200',
                    'elasticsearch8_index_prefix' => 'magento2',
                    'opensearch_index_prefix' => 'magento2',
                    'elasticsearch8_enable_auth' => '0',
                    'opensearch_enable_auth' => '0',
                    'elasticsearch8_username' => null,
                    'opensearch_username' => null,
                    'elasticsearch8_password' => null,
                    'opensearch_password' => null,
                    'elasticsearch8_server_timeout' => '15',
                    'opensearch_server_timeout' => '15'
                ]
            ],
            'cataloginventory' => [
                'source_selection_distance_based_google' => [
                    'api_key' => null
                ]
            ],
            'currency' => [
                'import' => [
                    'error_email' => null
                ]
            ],
            'sitemap' => [
                'generate' => [
                    'error_email' => null
                ]
            ],
            'trans_email' => [
                'ident_general' => [
                    'name' => 'Owner',
                    'email' => 'owner@example.com'
                ],
                'ident_sales' => [
                    'name' => 'Sales',
                    'email' => 'sales@example.com'
                ],
                'ident_support' => [
                    'name' => 'CustomerSupport',
                    'email' => 'support@example.com'
                ],
                'ident_custom1' => [
                    'name' => 'Custom 1',
                    'email' => 'custom1@example.com'
                ],
                'ident_custom2' => [
                    'name' => 'Custom 2',
                    'email' => 'custom2@example.com'
                ]
            ],
            'contact' => [
                'email' => [
                    'recipient_email' => 'hello@example.com'
                ]
            ],
            'sales_email' => [
                'order' => [
                    'copy_to' => null
                ],
                'order_comment' => [
                    'copy_to' => null
                ],
                'invoice' => [
                    'copy_to' => null
                ],
                'invoice_comment' => [
                    'copy_to' => null
                ],
                'shipment' => [
                    'copy_to' => null
                ],
                'shipment_comment' => [
                    'copy_to' => null
                ],
                'creditmemo' => [
                    'copy_to' => null
                ],
                'creditmemo_comment' => [
                    'copy_to' => null
                ]
            ],
            'checkout' => [
                'payment_failed' => [
                    'copy_to' => null
                ]
            ],
            'carriers' => [
                'ups' => [
                    'is_account_live' => '0',
                    'access_license_number' => null,
                    'gateway_xml_url' => 'https://onlinetools.ups.com/ups.app/xml/Rate',
                    'gateway_rest_url' => 'https://onlinetools.ups.com/api/rating/',
                    'password' => null,
                    'username' => null,
                    'gateway_url' => 'https://www.ups.com/using/services/rave/qcostcgi.cgi',
                    'shipper_number' => null,
                    'tracking_url' => 'https://onlinetools.ups.com/ups.app/xml/Track',
                    'tracking_rest_url' => 'https://onlinetools.ups.com/api/track/',
                    'debug' => '0'
                ],
                'usps' => [
                    'gateway_url' => 'https://production.shippingapis.com/ShippingAPI.dll',
                    'gateway_secure_url' => 'https://secure.shippingapis.com/ShippingAPI.dll',
                    'userid' => null,
                    'password' => null
                ],
                'fedex' => [
                    'account' => null,
                    'api_key' => null,
                    'secret_key' => null,
                    'sandbox_mode' => '0',
                    'production_webservices_url' => 'https://apis.fedex.com/',
                    'sandbox_webservices_url' => 'https://apis-sandbox.fedex.com/',
                    'smartpost_hubid' => null
                ],
                'dhl' => [
                    'id' => null,
                    'password' => null,
                    'account' => null,
                    'debug' => '0',
                    'gateway_url' => 'https://xmlpi-ea.dhl.com/XMLShippingServlet'
                ]
            ],
            'google' => [
                'analytics' => [
                    'account' => null
                ],
                'gtag' => [
                    'analytics4' => [
                        'measurement_id' => null
                    ],
                    'adwords' => [
                        'conversion_id' => null
                    ]
                ]
            ],
            'twofactorauth' => [
                'duo' => [
                    'client_id' => null,
                    'client_secret' => null,
                    'api_hostname' => null,
                    'integration_key' => null,
                    'secret_key' => null
                ],
                'authy' => [
                    'api_key' => null
                ]
            ],
            'recaptcha_backend' => [
                'type_recaptcha' => [
                    'public_key' => null,
                    'private_key' => null
                ],
                'type_invisible' => [
                    'public_key' => null,
                    'private_key' => null
                ],
                'type_recaptcha_v3' => [
                    'public_key' => null,
                    'private_key' => null
                ]
            ],
            'recaptcha_frontend' => [
                'type_recaptcha' => [
                    'public_key' => null,
                    'private_key' => null
                ],
                'type_invisible' => [
                    'public_key' => null,
                    'private_key' => null
                ],
                'type_recaptcha_v3' => [
                    'public_key' => null,
                    'private_key' => null
                ]
            ],
            'system' => [
                'smtp' => [
                    'host' => 'localhost',
                    'port' => '25'
                ],
                'full_page_cache' => [
                    'varnish' => [
                        'access_list' => null,
                        'backend_host' => null,
                        'backend_port' => null
                    ]
                ],
                'release_notification' => [
                    'content_url' => null,
                    'use_https' => '1'
                ]
            ],
            'adobe_ims' => [
                'integration' => [
                    'api_key' => null,
                    'private_key' => null
                ]
            ],
            'dev' => [
                'restrict' => [
                    'allow_ips' => null
                ],
                'js' => [
                    'session_storage_key' => 'collected_errors'
                ]
            ],
            'newrelicreporting' => [
                'general' => [
                    'api_url' => 'https://api.newrelic.com/v2/applications/%s/deployments.json',
                    'insights_api_url' => 'https://insights-collector.newrelic.com/v1/accounts/%s/events',
                    'account_id' => null,
                    'app_id' => null,
                    'api' => null,
                    'insights_insert_key' => null
                ]
            ],
            'analytics' => [
                'general' => [
                    'token' => null
                ],
                'url' => [
                    'signup' => 'https://advancedreporting.rjmetrics.com/signup',
                    'update' => 'https://advancedreporting.rjmetrics.com/update',
                    'bi_essentials' => 'https://dashboard.rjmetrics.com/v2/magento/signup',
                    'otp' => 'https://advancedreporting.rjmetrics.com/otp',
                    'report' => 'https://advancedreporting.rjmetrics.com/report',
                    'notify_data_changed' => 'https://advancedreporting.rjmetrics.com/report'
                ]
            ],
            'crontab' => [
                'default' => [
                    'jobs' => [
                        'analytics_subscribe' => [
                            'schedule' => [
                                'cron_expr' => '0 * * * *'
                            ]
                        ],
                        'analytics_collect_data' => [
                            'schedule' => [
                                'cron_expr' => '00 02 * * *'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
