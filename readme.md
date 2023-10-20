# Cloudflare Images Filesystem Driver
This package provides a filesystem driver for Cloudflare Images.

## Requirements

- PHP >= 8.0
- Laravel >= 9.0

## Confuguration
Add the following to your config/filesystems.php file:
```
'cloudflare-images' => [
    'driver' => 'cloudflare-images',
    'account_id' => env('CLOUDFLARE_IMAGES_ACCOUNT_ID'),
    'api_email' => env('CLOUDFLARE_IMAGES_API_EMAIL', env('CLOUDFLARE_API_EMAIL')),
    'api_key' => env('CLOUDFLARE_IMAGES_API_KEY', env('CLOUDFLARE_API_KEY')),
],
```
Add the following environment variables to your .env file:
```
CLOUDFLARE_IMAGES_ACCOUNT_ID=<account id>
```
If you did not have our [Laravel-Cloudflare](https://github.com/food-ticket/laravel-cloudflare) wrapper yet you also need to add the following environment variables to your .env file:
```
CLOUDFLARE_IMAGES_API_EMAIL=<API email>
CLOUDFLARE_IMAGES_API_KEY=<API key>
```

## Getting started
The Cloudflare Images filesystem driver can be used as you would use another filesystem driver. The documentation for the Laravel filesystem can be found [here](https://laravel.com/docs/9.x/filesystem). 

The following example shows how to use the Cloudflare Images filesystem driver to store a file.
```
use Illuminate\Support\Facades\Storage;

Storage::disk('cloudflare-images')->put('example.txt', 'Contents');
```

## Notes
Cloudflare Images doesnot support directories so not all filesystem methods are available. The following methods are supported:

- `fileExists`
- `write`
- `read`
- `delete`
- `visibility`
- `mimeType`
- `lastModified`
- `fileSize`
- `move`
- `copy`

The following methods are not supported:
- `directoryExists`
- `writeStream`
- `readStream`
- `deleteDirectory`
- `createDirectory`
- `listContents`

The following methods still need to be implemented:
- `setVisibility`

## Security Vulnerabilities

If you discover a security vulnerability within this project, please email me via [development@foodticket.nl](mailto:development@foodticket.nl).
