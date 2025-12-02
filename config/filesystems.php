<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        // 🔥 إضافة جديدة: ديسك خاص ومحمي للسير الذاتية
        'resumes' => [
            'driver' => 'local',
            'root' => storage_path('app/resumes'), // هذا المجلد غير مرتبط بالـ public
            'visibility' => 'private', // لن يتمكن أحد من الوصول إليه عبر رابط مباشر
            'throw' => false,
        ],

        'cloud' => [
            'driver' => 's3',
            'key' => env('LARAVEL_CLOUD_ACCESS_KEY_ID'),
            'secret' => env('LARAVEL_CLOUD_SECRET_ACCESS_KEY'),
            'region' => env('LARAVEL_CLOUD_DEFAULT_REGION'),
            'bucket' => env('LARAVEL_CLOUD_BUCKET'),
            'url' => env('LARAVEL_CLOUD_URL'),
            'endpoint' => env('LARAVEL_CLOUD_ENDPOINT'),
            'use_path_style_endpoint' => env('LARAVEL_CLOUD_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
```

### 🛠️ كيف تستخدم هذا التعديل في الكود؟

عند رفع الملف في الـ Controller (`JobApplicationsController`)، استخدم الديسك الجديد بدلاً من `public`:

**السابق (غير آمن):**
```php
$path = $file->store('resumes', 'public');
```

**الجديد (الآمن):**
```php
// سيتم التخزين في storage/app/resumes/
$path = $file->store('/', 'resumes'); 
```

**كيف تعرض الملف للمدير؟**
بما أنه ملف خاص، لا يمكنك وضع رابط مباشر `href`. يجب عمل Route خاص يقوم بتحميل الملف (Download Response):

```php
return Storage::disk('resumes')->download($resume->fileUrl);
