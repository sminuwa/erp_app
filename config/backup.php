<?php

return [

    'backup' => [

        'name' => env('APP_NAME', 'ERP_ALBABELLO'),

        'source' => [

            'files' => [
                'include' => [
                    base_path(),
                ],
                'exclude' => [
                    storage_path(),
                    base_path('vendor'),
                    base_path('node_modules'),
                    base_path('.git'),
                ],
                'follow_links' => false,
                'ignore_unreadable_directories' => false,
                'relative_path' => null,
            ],

            'databases' => [
                'mysql',
            ],
        ],

        'database_dump_compressor' => null,

        'database_dump_file_extension' => 'sql',

        'destination' => [
            'filename_prefix' => 'backup_',
            'disks' => ['local'], // Change to ['s3'] if using cloud backups
        ],

        'temporary_directory' => storage_path('app/backups'),

        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        'encryption' => 'default',
    ],

    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailed::class => [],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFound::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailed::class => [],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessful::class => [],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFound::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessful::class => [],
        ],

        'notifiable' => null,//\App\Models\User::class,  // Completely disables notifications

        'mail' => [
            'to' => '',  // Prevents email notification errors
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username' => '',
            'avatar_url' => '',
        ],
    ],


    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'ERP_ALBABELLO'),
            'disks' => ['local'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 7,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],

];
