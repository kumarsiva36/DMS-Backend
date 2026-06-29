<?php return array (
  4 => 'concurrency',
  'app' => 
  array (
    'name' => 'Laravel',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost',
    'frontend_url' => 'http://192.168.1.24:3002/',
    'asset_url' => NULL,
    'timezone' => 'Asia/Kolkata',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
    'key' => 'base64:Vk8HxfK+mh/HdyFIS62jbWd++hIaBaXtoKKchePyVdM=',
    'previous_keys' => 
    array (
    ),
    'maintenance' => 
    array (
      'driver' => 'file',
    ),
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Barryvdh\\DomPDF\\ServiceProvider',
      23 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      24 => 'App\\Providers\\AppServiceProvider',
      25 => 'App\\Providers\\AuthServiceProvider',
      26 => 'App\\Providers\\EventServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
      28 => 'Laravel\\Passport\\PassportServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Concurrency' => 'Illuminate\\Support\\Facades\\Concurrency',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Context' => 'Illuminate\\Support\\Facades\\Context',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'Date' => 'Illuminate\\Support\\Facades\\Date',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Js' => 'Illuminate\\Support\\Js',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Number' => 'Illuminate\\Support\\Number',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Process' => 'Illuminate\\Support\\Facades\\Process',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'RateLimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schedule' => 'Illuminate\\Support\\Facades\\Schedule',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Uri' => 'Illuminate\\Support\\Uri',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Vite' => 'Illuminate\\Support\\Facades\\Vite',
      'PDF' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'Image' => 'Intervention\\Image\\Facades\\Image',
    ),
    'logo_url' => 'http://34.208.216.116:8081/userlogin',
    'app_url' => 'https://staging.catech.co.in:85/',
    'public_url' => 'http://192.168.1.100:8003/',
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'user-api' => 
      array (
        'driver' => 'passport',
        'provider' => 'users',
        'hash' => false,
      ),
      'staff' => 
      array (
        'driver' => 'session',
        'provider' => 'staff',
      ),
      'staff-api' => 
      array (
        'driver' => 'passport',
        'provider' => 'staff',
        'hash' => false,
      ),
      'sanctum' => 
      array (
        'driver' => 'sanctum',
        'provider' => NULL,
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
      'staff' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\Staff',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'backup' => 
  array (
    'backup' => 
    array (
      'name' => 'DMS-DB-Backup',
      'source' => 
      array (
        'files' => 
        array (
          'include' => 
          array (
            0 => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version',
          ),
          'exclude' => 
          array (
            0 => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\vendor',
            1 => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\node_modules',
          ),
          'follow_links' => false,
          'ignore_unreadable_directories' => false,
          'relative_path' => NULL,
        ),
        'databases' => 
        array (
          0 => 'mysql',
        ),
      ),
      'database_dump_compressor' => NULL,
      'database_dump_file_extension' => '',
      'destination' => 
      array (
        'filename_prefix' => '',
        'disks' => 
        array (
          0 => 's3',
          1 => 'local',
        ),
      ),
      'temporary_directory' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\app/backup-temp',
      'password' => NULL,
      'encryption' => 'default',
    ),
    'notifications' => 
    array (
      'notifications' => 
      array (
        'Spatie\\Backup\\Notifications\\Notifications\\BackupHasFailedNotification' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\UnhealthyBackupWasFoundNotification' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupHasFailedNotification' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\BackupWasSuccessfulNotification' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\HealthyBackupWasFoundNotification' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupWasSuccessfulNotification' => 
        array (
          0 => 'mail',
        ),
      ),
      'notifiable' => 'Spatie\\Backup\\Notifications\\Notifiable',
      'mail' => 
      array (
        'to' => 'saravanakumar.r@catech.co.in',
        'from' => 
        array (
          'address' => 'catech.dev2@gmail.com',
          'name' => 'DMS Support',
        ),
      ),
      'slack' => 
      array (
        'webhook_url' => '',
        'channel' => NULL,
        'username' => NULL,
        'icon' => NULL,
      ),
      'discord' => 
      array (
        'webhook_url' => '',
        'username' => '',
        'avatar_url' => '',
      ),
    ),
    'monitor_backups' => 
    array (
      0 => 
      array (
        'name' => 'Laravel',
        'disks' => 
        array (
          0 => 's3',
        ),
        'health_checks' => 
        array (
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumAgeInDays' => 1,
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumStorageInMegabytes' => 5000,
        ),
      ),
    ),
    'cleanup' => 
    array (
      'strategy' => 'Spatie\\Backup\\Tasks\\Cleanup\\Strategies\\DefaultStrategy',
      'default_strategy' => 
      array (
        'keep_all_backups_for_days' => 7,
        'keep_daily_backups_for_days' => 16,
        'keep_weekly_backups_for_weeks' => 8,
        'keep_monthly_backups_for_months' => 4,
        'keep_yearly_backups_for_years' => 2,
        'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'reverb' => 
      array (
        'driver' => 'reverb',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'host' => NULL,
          'port' => 443,
          'scheme' => 'https',
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
          'host' => 'api-mt1.pusher.com',
          'port' => 443,
          'scheme' => 'https',
          'encrypted' => true,
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
        'lock_connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => 'AKIAYAK3SG2IJ3WKOZXU',
        'secret' => '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',
        'region' => 'us-west-2',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
      'octane' => 
      array (
        'driver' => 'octane',
      ),
      'apc' => 
      array (
        'driver' => 'apc',
      ),
    ),
    'prefix' => 'laravel_cache_',
  ),
  'constant' => 
  array (
    'dashboard_modules' => 
    array (
      1 => 'Production Status',
      3 => 'Task Status',
      4 => 'Top 5 Delayed Task',
      2 => 'Top 5 Delayed Production',
      5 => 'Order Status',
    ),
    'dashboard_modules_mobile' => 
    array (
      4 => 'Top 5 Delayed Task',
      2 => 'Top 5 Delayed Production',
      7 => 'Notifications',
      6 => 'Ongoing List',
      5 => 'Order Status',
    ),
    'rolesAndPermissions' => 
    array (
      'Manager' => 
      array (
        0 => 1,
        1 => 2,
        2 => 3,
        3 => 4,
        4 => 5,
        5 => 6,
        6 => 7,
        7 => 8,
        8 => 9,
        9 => 10,
        10 => 11,
        11 => 12,
        12 => 13,
        13 => 14,
        14 => 15,
        15 => 16,
        16 => 17,
        17 => 18,
        18 => 19,
        19 => 20,
        20 => 21,
        21 => 22,
        22 => 23,
        23 => 24,
        24 => 25,
        25 => 26,
        26 => 27,
        27 => 28,
        28 => 29,
        29 => 30,
        30 => 31,
        31 => 32,
        32 => 33,
        33 => 34,
        34 => 35,
        35 => 36,
        36 => 37,
        37 => 38,
        38 => 39,
        39 => 40,
        40 => 41,
        41 => 42,
        42 => 43,
        43 => 44,
        44 => 45,
      ),
      'Merchandiser' => 
      array (
        0 => 1,
        1 => 2,
        2 => 3,
        3 => 4,
        4 => 5,
        5 => 6,
        6 => 7,
        7 => 8,
        8 => 9,
        9 => 10,
        10 => 11,
        11 => 12,
        12 => 13,
        13 => 14,
        14 => 15,
        15 => 16,
        16 => 17,
        17 => 18,
        18 => 19,
        19 => 20,
        20 => 21,
        21 => 22,
        22 => 23,
        23 => 24,
        24 => 25,
        25 => 26,
        26 => 27,
        27 => 28,
        28 => 29,
        29 => 30,
        30 => 31,
        31 => 32,
        32 => 33,
        33 => 34,
        34 => 35,
        35 => 36,
        36 => 37,
        37 => 38,
        38 => 40,
        39 => 41,
        40 => 42,
        41 => 43,
        42 => 44,
      ),
      'Supervisor' => 
      array (
        0 => 1,
        1 => 2,
        2 => 3,
        3 => 4,
        4 => 5,
        5 => 6,
        6 => 7,
        7 => 8,
        8 => 9,
        9 => 10,
        10 => 11,
        11 => 12,
        12 => 13,
        13 => 14,
        14 => 16,
        15 => 17,
        16 => 18,
        17 => 19,
        18 => 21,
        19 => 22,
        20 => 23,
        21 => 24,
        22 => 25,
        23 => 26,
        24 => 27,
        25 => 28,
        26 => 29,
        27 => 30,
        28 => 32,
        29 => 33,
        30 => 35,
        31 => 36,
        32 => 40,
        33 => 44,
      ),
      'Staff' => 
      array (
        0 => 17,
        1 => 26,
        2 => 27,
        3 => 28,
        4 => 40,
      ),
      'Guest' => 
      array (
        0 => 19,
        1 => 26,
        2 => 28,
        3 => 40,
      ),
    ),
    'bom_units' => 
    array (
      1 => 'Nos',
      2 => 'Cones',
      3 => 'Meters',
      4 => 'Rolls',
    ),
    'pdf_icon_width' => '20',
    'mail_icon_width' => '20',
    'plan_storage_size_validation' => '1',
    'plan_storage_free_mb' => '10',
    'plan_storage_free_mb_type' => '1',
    'task_inprogress_percentage' => '1',
    'techpack_comments_audio_enable' => '1',
    'techpack_comments_video_enable' => '1',
    'order_comments_enable' => '1',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'dms_backend',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'dump' => 
        array (
          'dump_binary_path' => 'D:\\xampp8.1\\mysql\\bin',
        ),
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'dms_backend',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => false,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'mariadb' => 
      array (
        'driver' => 'mariadb',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'eyJjaXBoZXJ0ZXh0IjoiOHJENWZEWllOdnpqWnQrcFYrRnZzQT09IiwiaXYiOiI5ZmYzMjQwOTMxNjNjODZkZDNlMjE2OTc3OTZhMDI0MCIsInNhbHQiOiJmMTdjZDYwNjhkNGE5MjMzNWMwNzY0NDNiNzA2NTg5NDU5Y2Q2MDJmODM5MjllZjZmNDdjNjZiNjI4NTg5NWZmZjhlMWIxNTU3OWJhMGQ2NWQ4NTg2MTMxOWUyNmI2MzJiYjIwYzUwYmMyMDQ5MDllOWU3Nzc2ZjI2NDQwZGM3OTY4ZWY0OWQ4MjM5NDJjNGE4NTg5MTBjZDMzZjViNmU0OGVkYzYyNzM3Yjk3NTU0NjM4YTM2Yjg4NGY2NGUwMTIxODk5OGNmYmY0MTEyYzVhNTI4OGIyMDQ4ZWYzNGFlMzllODBhNWU0NWNlYTZhN2Y2ODc1ZjFmNDA4YzRlODQxNmJmMWM4ZDRkMDZjZWNlMmQ2MjUyZDZiM2IzNGY4MTMwYmVkZTgyM2ZlZjQzYjI5NWVmZWU3M2YzM2I0ZDBmYzhjMzU2NmU0ZGQ0YzUwYWYxMTc2NTUzNDRmMDgzYTkzNTU4ZDU4YzMwMDIwNGQ3MjUxZGJlMTUwOGU5YTU2MDI5YTUwYmU3NDk4ODYyNjBmZDJiY2NkNTg4MmI2OWViODE5OTkwZGM5MzI3YTdjODU1MWM3MjhkOGZjYjM1OGVlNWYwNGU4MDMzZjAxYmRiNjVkYzU3NzRkNzhiMzg5MmVkNzFjZTFmOTI0ZTUyZjQyZjM5MzRlMzkzMjJkMDE5ZCIsIml0ZXJhdGlvbnMiOjk5OX0=',
        'username' => 'eyJjaXBoZXJ0ZXh0IjoiVFBZOVJaaDY4WVE4Qk5XQjM1ZnF3QT09IiwiaXYiOiJmNGUzN2UxZGI5NTgwZjFkMDQ0MDMyZjE2ZWMwZDRjMiIsInNhbHQiOiI0ZDU3ZTAxYzhjMGIzMGEzZTAwN2NiZTU4YTMxMTIyZjc3OTM5MGE1NjRiOGI4NmRmMzJmMzYzMTNmZDNmMTkyYjM4YzU0MjQyMTc2NDdkNjQ0ZTFiMTQ5MmI0ODE1MWVhNzg5ZWZmODY3ZDgwMjU3Mzg3M2QyOTZhN2MzMWUxMjliY2M1MzIzOTFkZjk4Njg1NGVlMDYxZTMxNjY4Y2MzNzhiNmRlZDhmNzdkMjUzNzE2NmM3ZjVjYWI4MDFkNzM0YzExODdiNzExMmM5ODU4MzgyOWVkODljYTAxMjJlMWUyMzNhMWZhYWZmM2NhMGZhYzE3YzgzYmJjZjVjMGRjNzRkNmRmYzBlNDUwZmNiMGIwYmJiZjBkYzc3YzFiMjE4ZDZhNGJhZTU4Y2Q0ZjhhZTI3NTk5NjlhYzAwYWI2NTE2NjJhNzc1ZjU3NjIyMDBiMGVlMjZjZTkzODc0NDMyMmZmMjNiZGZhZDA0ZWQ3ZmEyNTk0ZjY3YzRjMjE3NTZmMGUzOTE1Y2ZlOGVkOTUyNzY2M2ZjNjIxMDkyMWY4ODk1MTVmMmJhOGZhNDhjYjg5YmJkZmU3NTE5MGQ0NTljNDE3ODY4YWVhYTE4MTM2M2M2Y2UwNTI4MmRiZTY1NTk0NGQyYmJhZDU0NDNjOGZjYjg0NTFmNDNlOTMzNDQ4YiIsIml0ZXJhdGlvbnMiOjk5OX0=',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'eyJjaXBoZXJ0ZXh0IjoiOHJENWZEWllOdnpqWnQrcFYrRnZzQT09IiwiaXYiOiI5ZmYzMjQwOTMxNjNjODZkZDNlMjE2OTc3OTZhMDI0MCIsInNhbHQiOiJmMTdjZDYwNjhkNGE5MjMzNWMwNzY0NDNiNzA2NTg5NDU5Y2Q2MDJmODM5MjllZjZmNDdjNjZiNjI4NTg5NWZmZjhlMWIxNTU3OWJhMGQ2NWQ4NTg2MTMxOWUyNmI2MzJiYjIwYzUwYmMyMDQ5MDllOWU3Nzc2ZjI2NDQwZGM3OTY4ZWY0OWQ4MjM5NDJjNGE4NTg5MTBjZDMzZjViNmU0OGVkYzYyNzM3Yjk3NTU0NjM4YTM2Yjg4NGY2NGUwMTIxODk5OGNmYmY0MTEyYzVhNTI4OGIyMDQ4ZWYzNGFlMzllODBhNWU0NWNlYTZhN2Y2ODc1ZjFmNDA4YzRlODQxNmJmMWM4ZDRkMDZjZWNlMmQ2MjUyZDZiM2IzNGY4MTMwYmVkZTgyM2ZlZjQzYjI5NWVmZWU3M2YzM2I0ZDBmYzhjMzU2NmU0ZGQ0YzUwYWYxMTc2NTUzNDRmMDgzYTkzNTU4ZDU4YzMwMDIwNGQ3MjUxZGJlMTUwOGU5YTU2MDI5YTUwYmU3NDk4ODYyNjBmZDJiY2NkNTg4MmI2OWViODE5OTkwZGM5MzI3YTdjODU1MWM3MjhkOGZjYjM1OGVlNWYwNGU4MDMzZjAxYmRiNjVkYzU3NzRkNzhiMzg5MmVkNzFjZTFmOTI0ZTUyZjQyZjM5MzRlMzkzMjJkMDE5ZCIsIml0ZXJhdGlvbnMiOjk5OX0=',
        'username' => 'eyJjaXBoZXJ0ZXh0IjoiVFBZOVJaaDY4WVE4Qk5XQjM1ZnF3QT09IiwiaXYiOiJmNGUzN2UxZGI5NTgwZjFkMDQ0MDMyZjE2ZWMwZDRjMiIsInNhbHQiOiI0ZDU3ZTAxYzhjMGIzMGEzZTAwN2NiZTU4YTMxMTIyZjc3OTM5MGE1NjRiOGI4NmRmMzJmMzYzMTNmZDNmMTkyYjM4YzU0MjQyMTc2NDdkNjQ0ZTFiMTQ5MmI0ODE1MWVhNzg5ZWZmODY3ZDgwMjU3Mzg3M2QyOTZhN2MzMWUxMjliY2M1MzIzOTFkZjk4Njg1NGVlMDYxZTMxNjY4Y2MzNzhiNmRlZDhmNzdkMjUzNzE2NmM3ZjVjYWI4MDFkNzM0YzExODdiNzExMmM5ODU4MzgyOWVkODljYTAxMjJlMWUyMzNhMWZhYWZmM2NhMGZhYzE3YzgzYmJjZjVjMGRjNzRkNmRmYzBlNDUwZmNiMGIwYmJiZjBkYzc3YzFiMjE4ZDZhNGJhZTU4Y2Q0ZjhhZTI3NTk5NjlhYzAwYWI2NTE2NjJhNzc1ZjU3NjIyMDBiMGVlMjZjZTkzODc0NDMyMmZmMjNiZGZhZDA0ZWQ3ZmEyNTk0ZjY3YzRjMjE3NTZmMGUzOTE1Y2ZlOGVkOTUyNzY2M2ZjNjIxMDkyMWY4ODk1MTVmMmJhOGZhNDhjYjg5YmJkZmU3NTE5MGQ0NTljNDE3ODY4YWVhYTE4MTM2M2M2Y2UwNTI4MmRiZTY1NTk0NGQyYmJhZDU0NDNjOGZjYjg0NTFmNDNlOTMzNDQ4YiIsIml0ZXJhdGlvbnMiOjk5OX0=',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'eyJjaXBoZXJ0ZXh0IjoiOHJENWZEWllOdnpqWnQrcFYrRnZzQT09IiwiaXYiOiI5ZmYzMjQwOTMxNjNjODZkZDNlMjE2OTc3OTZhMDI0MCIsInNhbHQiOiJmMTdjZDYwNjhkNGE5MjMzNWMwNzY0NDNiNzA2NTg5NDU5Y2Q2MDJmODM5MjllZjZmNDdjNjZiNjI4NTg5NWZmZjhlMWIxNTU3OWJhMGQ2NWQ4NTg2MTMxOWUyNmI2MzJiYjIwYzUwYmMyMDQ5MDllOWU3Nzc2ZjI2NDQwZGM3OTY4ZWY0OWQ4MjM5NDJjNGE4NTg5MTBjZDMzZjViNmU0OGVkYzYyNzM3Yjk3NTU0NjM4YTM2Yjg4NGY2NGUwMTIxODk5OGNmYmY0MTEyYzVhNTI4OGIyMDQ4ZWYzNGFlMzllODBhNWU0NWNlYTZhN2Y2ODc1ZjFmNDA4YzRlODQxNmJmMWM4ZDRkMDZjZWNlMmQ2MjUyZDZiM2IzNGY4MTMwYmVkZTgyM2ZlZjQzYjI5NWVmZWU3M2YzM2I0ZDBmYzhjMzU2NmU0ZGQ0YzUwYWYxMTc2NTUzNDRmMDgzYTkzNTU4ZDU4YzMwMDIwNGQ3MjUxZGJlMTUwOGU5YTU2MDI5YTUwYmU3NDk4ODYyNjBmZDJiY2NkNTg4MmI2OWViODE5OTkwZGM5MzI3YTdjODU1MWM3MjhkOGZjYjM1OGVlNWYwNGU4MDMzZjAxYmRiNjVkYzU3NzRkNzhiMzg5MmVkNzFjZTFmOTI0ZTUyZjQyZjM5MzRlMzkzMjJkMDE5ZCIsIml0ZXJhdGlvbnMiOjk5OX0=',
        'username' => 'eyJjaXBoZXJ0ZXh0IjoiVFBZOVJaaDY4WVE4Qk5XQjM1ZnF3QT09IiwiaXYiOiJmNGUzN2UxZGI5NTgwZjFkMDQ0MDMyZjE2ZWMwZDRjMiIsInNhbHQiOiI0ZDU3ZTAxYzhjMGIzMGEzZTAwN2NiZTU4YTMxMTIyZjc3OTM5MGE1NjRiOGI4NmRmMzJmMzYzMTNmZDNmMTkyYjM4YzU0MjQyMTc2NDdkNjQ0ZTFiMTQ5MmI0ODE1MWVhNzg5ZWZmODY3ZDgwMjU3Mzg3M2QyOTZhN2MzMWUxMjliY2M1MzIzOTFkZjk4Njg1NGVlMDYxZTMxNjY4Y2MzNzhiNmRlZDhmNzdkMjUzNzE2NmM3ZjVjYWI4MDFkNzM0YzExODdiNzExMmM5ODU4MzgyOWVkODljYTAxMjJlMWUyMzNhMWZhYWZmM2NhMGZhYzE3YzgzYmJjZjVjMGRjNzRkNmRmYzBlNDUwZmNiMGIwYmJiZjBkYzc3YzFiMjE4ZDZhNGJhZTU4Y2Q0ZjhhZTI3NTk5NjlhYzAwYWI2NTE2NjJhNzc1ZjU3NjIyMDBiMGVlMjZjZTkzODc0NDMyMmZmMjNiZGZhZDA0ZWQ3ZmEyNTk0ZjY3YzRjMjE3NTZmMGUzOTE1Y2ZlOGVkOTUyNzY2M2ZjNjIxMDkyMWY4ODk1MTVmMmJhOGZhNDhjYjg5YmJkZmU3NTE5MGQ0NTljNDE3ODY4YWVhYTE4MTM2M2M2Y2UwNTI4MmRiZTY1NTk0NGQyYmJhZDU0NDNjOGZjYjg0NTFmNDNlOTMzNDQ4YiIsIml0ZXJhdGlvbnMiOjk5OX0=',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
      'second_mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'dms_chat',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => false,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'laravel_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'public_path' => NULL,
    'convert_entities' => true,
    'options' => 
    array (
      'font_dir' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\fonts',
      'font_cache' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\fonts',
      'temp_dir' => 'C:\\Users\\sivak\\AppData\\Local\\Temp',
      'chroot' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version',
      'allowed_protocols' => 
      array (
        'file://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'http://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'https://' => 
        array (
          'rules' => 
          array (
          ),
        ),
      ),
      'log_output_file' => NULL,
      'enable_font_subsetting' => true,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_paper_orientation' => 'portrait',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => true,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => true,
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
        'output_encoding' => '',
        'test_auto_detect' => true,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => false,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => NULL,
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
    ),
    'transactions' => 
    array (
      'handler' => 'db',
      'db' => 
      array (
        'connection' => NULL,
      ),
    ),
    'temporary_files' => 
    array (
      'local_path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\framework/cache/laravel-excel',
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\app',
        'throw' => false,
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\app/public',
        'url' => 'http://localhost/storage',
        'visibility' => 'public',
        'throw' => false,
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => 'AKIAYAK3SG2IJ3WKOZXU',
        'secret' => '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',
        'region' => 'us-west-2',
        'bucket' => 'new-dms-dev',
        'url' => 'https://new-dms-dev.s3.us-west-2.amazonaws.com/',
        'endpoint' => NULL,
        'options' => 
        array (
          'ACL' => 'private',
        ),
        'use_path_style_endpoint' => false,
        'throw' => false,
        'backup_extra_options' => 
        array (
          'StorageClass' => 'Standard-IA',
        ),
      ),
    ),
    'links' => 
    array (
      'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\public\\storage' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\app/public',
    ),
  ),
  'flare' => 
  array (
    'key' => NULL,
    'flare_middleware' => 
    array (
      0 => 'Spatie\\FlareClient\\FlareMiddleware\\RemoveRequestIp',
      1 => 'Spatie\\FlareClient\\FlareMiddleware\\AddGitInformation',
      2 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddNotifierName',
      3 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddEnvironmentInformation',
      4 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddExceptionInformation',
      5 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddDumps',
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddLogs' => 
      array (
        'maximum_number_of_collected_logs' => 200,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddQueries' => 
      array (
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddJobs' => 
      array (
        'max_chained_job_reporting_depth' => 5,
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestBodyFields' => 
      array (
        'censor_fields' => 
        array (
          0 => 'password',
          1 => 'password_confirmation',
        ),
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestHeaders' => 
      array (
        'headers' => 
        array (
          0 => 'API-KEY',
        ),
      ),
    ),
    'send_logs_as_events' => true,
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 65536,
      'threads' => 1,
      'time' => 4,
    ),
    'rehash_on_login' => true,
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'auto',
    'enable_share_button' => true,
    'register_commands' => false,
    'solution_providers' => 
    array (
      0 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\BadMethodCallSolutionProvider',
      1 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\MergeConflictSolutionProvider',
      2 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\UndefinedPropertySolutionProvider',
      3 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\IncorrectValetDbCredentialsSolutionProvider',
      4 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingAppKeySolutionProvider',
      5 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\DefaultDbNameSolutionProvider',
      6 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\TableNotFoundSolutionProvider',
      7 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingImportSolutionProvider',
      8 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\InvalidRouteActionSolutionProvider',
      9 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\ViewNotFoundSolutionProvider',
      10 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\RunningLaravelDuskInProductionProvider',
      11 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingColumnSolutionProvider',
      12 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownValidationSolutionProvider',
      13 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingMixManifestSolutionProvider',
      14 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingViteManifestSolutionProvider',
      15 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingLivewireComponentSolutionProvider',
      16 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UndefinedViewVariableSolutionProvider',
      17 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\GenericLaravelExceptionSolutionProvider',
    ),
    'ignored_solution_providers' => 
    array (
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
    'settings_file_path' => '',
    'recorders' => 
    array (
      0 => 'Spatie\\LaravelIgnition\\Recorders\\DumpRecorder\\DumpRecorder',
      1 => 'Spatie\\LaravelIgnition\\Recorders\\JobRecorder\\JobRecorder',
      2 => 'Spatie\\LaravelIgnition\\Recorders\\LogRecorder\\LogRecorder',
      3 => 'Spatie\\LaravelIgnition\\Recorders\\QueryRecorder\\QueryRecorder',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'deprecations' => 
    array (
      'channel' => 'null',
      'trace' => false,
    ),
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'daily',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
          'connectionString' => 'tls://:',
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
        'facility' => 8,
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => '465',
        'encryption' => 'ssl',
        'username' => 'catech.dev2@gmail.com',
        'password' => 'gvddfmeyxtabulqh',
        'timeout' => NULL,
        'local_domain' => NULL,
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'resend' => 
      array (
        'transport' => 'resend',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs -i',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
      'failover' => 
      array (
        'transport' => 'failover',
        'mailers' => 
        array (
          0 => 'smtp',
          1 => 'log',
        ),
      ),
      'roundrobin' => 
      array (
        'transport' => 'roundrobin',
        'mailers' => 
        array (
          0 => 'ses',
          1 => 'postmark',
        ),
        'retry_after' => 60,
      ),
      'second_mailer' => 
      array (
        'transport' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'encryption' => 'ssl',
        'username' => 'catech.dev2@gmail.com',
        'password' => 'gvddfmeyxtabulqh',
        'timeout' => NULL,
        'auth_mode' => NULL,
      ),
      'mailgun' => 
      array (
        'transport' => 'mailgun',
      ),
    ),
    'from' => 
    array (
      'address' => 'catech.dev2@gmail.com',
      'name' => 'DMS Support',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\resources\\views/vendor/mail',
      ),
    ),
  ),
  'passport' => 
  array (
    'guard' => 'web',
    'private_key' => NULL,
    'public_key' => NULL,
    'connection' => NULL,
    'client_uuids' => false,
    'personal_access_client' => 
    array (
      'id' => NULL,
      'secret' => NULL,
    ),
  ),
  'queue' => 
  array (
    'default' => 'database',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'AKIAYAK3SG2IJ3WKOZXU',
        'secret' => '345DMWQDCff291Z14HQ/CfcUrRjQkKT/v+cJxI4d',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'default',
        'suffix' => NULL,
        'region' => 'us-west-2',
        'after_commit' => false,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'after_commit' => false,
      ),
    ),
    'batching' => 
    array (
      'database' => 'mysql',
      'table' => 'job_batches',
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'sanctum' => 
  array (
    'stateful' => 
    array (
      0 => 'localhost',
      1 => 'localhost:3000',
      2 => '127.0.0.1',
      3 => '127.0.0.1:8000',
      4 => '::1',
      5 => 'localhost',
    ),
    'guard' => 
    array (
      0 => 'web',
    ),
    'expiration' => NULL,
    'token_prefix' => '',
    'middleware' => 
    array (
      'verify_csrf_token' => 'App\\Http\\Middleware\\VerifyCsrfToken',
      'encrypt_cookies' => 'App\\Http\\Middleware\\EncryptCookies',
    ),
  ),
  'services' => 
  array (
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'resend' => 
    array (
      'key' => NULL,
    ),
    'ses' => 
    array (
      'key' => 'AKIAYAK3SG2IJ3WKOZXU',
      'secret' => '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',
      'region' => 'us-west-2',
    ),
    'slack' => 
    array (
      'notifications' => 
      array (
        'bot_user_oauth_token' => NULL,
        'channel' => NULL,
      ),
    ),
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
      'scheme' => 'https',
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '120',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
  ),
  'stripe' => 
  array (
    'api_keys' => 
    array (
      'secret_key' => 'sk_test_51HmeE0JG9ay9QlS8tcUZZsQHFXLILR1UlW9qzVOz82RcDlZM2fSe3Ph0GpD0MBWut3sX9NICZr0BeJbgdKPNoM3Q00pwhjbLEJ',
      'publisher_key' => 'pk_test_51HmeE0JG9ay9QlS8Q4bijPcA2vFVrBVEN84cFFfNH9lqB4ZeuM8TGaPdoF1b3L5XuGUJYURZGHDeXgucKKYN3L0x00BJOdYPWk',
    ),
    'frontend_url' => 'http://192.168.1.24:3002/',
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\resources\\views',
    ),
    'compiled' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\storage\\framework\\views',
  ),
  'concurrency' => 
  array (
    'default' => 'process',
  ),
  'image' => 
  array (
    'driver' => 'gd',
  ),
  'pulse' => 
  array (
    'domain' => NULL,
    'path' => 'pulse',
    'enabled' => true,
    'storage' => 
    array (
      'driver' => 'database',
      'trim' => 
      array (
        'keep' => '7 days',
      ),
      'database' => 
      array (
        'connection' => NULL,
        'chunk' => 1000,
      ),
    ),
    'ingest' => 
    array (
      'driver' => 'storage',
      'buffer' => 5000,
      'trim' => 
      array (
        'lottery' => 
        array (
          0 => 1,
          1 => 1000,
        ),
        'keep' => '7 days',
      ),
      'redis' => 
      array (
        'connection' => NULL,
        'chunk' => 1000,
      ),
    ),
    'cache' => NULL,
    'middleware' => 
    array (
      0 => 'web',
      1 => 'Laravel\\Pulse\\Http\\Middleware\\Authorize',
    ),
    'recorders' => 
    array (
      'Laravel\\Pulse\\Recorders\\CacheInteractions' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'ignore' => 
        array (
          0 => '/(^laravel_vapor_job_attemp(t?)s:)/',
          1 => '/^.+@.+\\|(?:(?:\\d+\\.\\d+\\.\\d+\\.\\d+)|[0-9a-fA-F:]+)(?::timer)?$/',
          2 => '/^[a-zA-Z0-9]{40}$/',
          3 => '/^illuminate:/',
          4 => '/^laravel:pulse:/',
          5 => '/^laravel:reverb:/',
          6 => '/^nova/',
          7 => '/^telescope:/',
        ),
        'groups' => 
        array (
          '/^job-exceptions:.*/' => 'job-exceptions:*',
        ),
      ),
      'Laravel\\Pulse\\Recorders\\Exceptions' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'location' => true,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Pulse\\Recorders\\Queues' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Pulse\\Recorders\\Servers' => 
      array (
        'server_name' => 'CATECH-7',
        'directories' => 
        array (
          0 => '/',
        ),
      ),
      'Laravel\\Pulse\\Recorders\\SlowJobs' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'threshold' => 1000,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Pulse\\Recorders\\SlowOutgoingRequests' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'threshold' => 1000,
        'ignore' => 
        array (
        ),
        'groups' => 
        array (
        ),
      ),
      'Laravel\\Pulse\\Recorders\\SlowQueries' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'threshold' => 1000,
        'location' => true,
        'max_query_length' => NULL,
        'ignore' => 
        array (
          0 => '/(["`])pulse_[\\w]+?\\1/',
          1 => '/(["`])telescope_[\\w]+?\\1/',
        ),
      ),
      'Laravel\\Pulse\\Recorders\\SlowRequests' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'threshold' => 1000,
        'ignore' => 
        array (
          0 => '#^/pulse$#',
          1 => '#^/telescope#',
        ),
      ),
      'Laravel\\Pulse\\Recorders\\UserJobs' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Pulse\\Recorders\\UserRequests' => 
      array (
        'enabled' => true,
        'sample_rate' => 1,
        'ignore' => 
        array (
          0 => '#^/pulse$#',
          1 => '#^/telescope#',
        ),
      ),
    ),
  ),
  'livewire' => 
  array (
    'component_locations' => 
    array (
      0 => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\resources\\views/components',
      1 => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\resources\\views/livewire',
    ),
    'component_namespaces' => 
    array (
      'layouts' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\resources\\views/layouts',
      'pages' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\resources\\views/pages',
    ),
    'component_layout' => 'layouts::app',
    'component_placeholder' => NULL,
    'make_command' => 
    array (
      'type' => 'sfc',
      'emoji' => true,
      'with' => 
      array (
        'js' => false,
        'css' => false,
        'test' => false,
      ),
    ),
    'class_namespace' => 'App\\Livewire',
    'class_path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\app\\Livewire',
    'view_path' => 'D:\\xampp8.2\\htdocs\\DMS-Backend-Version\\resources\\views/livewire',
    'temporary_file_upload' => 
    array (
      'disk' => NULL,
      'rules' => NULL,
      'directory' => NULL,
      'middleware' => NULL,
      'preview_mimes' => 
      array (
        0 => 'png',
        1 => 'gif',
        2 => 'bmp',
        3 => 'svg',
        4 => 'wav',
        5 => 'mp4',
        6 => 'mov',
        7 => 'avi',
        8 => 'wmv',
        9 => 'mp3',
        10 => 'm4a',
        11 => 'jpg',
        12 => 'jpeg',
        13 => 'mpga',
        14 => 'webp',
        15 => 'wma',
      ),
      'max_upload_time' => 5,
      'cleanup' => true,
    ),
    'render_on_redirect' => false,
    'legacy_model_binding' => false,
    'inject_assets' => true,
    'navigate' => 
    array (
      'show_progress_bar' => true,
      'progress_bar_color' => '#2299dd',
    ),
    'inject_morph_markers' => true,
    'smart_wire_keys' => true,
    'pagination_theme' => 'tailwind',
    'release_token' => 'a',
    'csp_safe' => false,
    'payload' => 
    array (
      'max_size' => 1048576,
      'max_nesting_depth' => 10,
      'max_calls' => 50,
      'max_components' => 200,
    ),
  ),
);
