# Cloudflare Images Filesystem Driver
A Laravel filesystem driver for Cloudflare Images.

## Requirements

- PHP >= 8.0
- Laravel >= 9.0

## Confuguration
Add the following to your config/filesystems.php file:
```
'cloudflare-images' => [
    'driver' => 'cloudflare-images',
    'account_id' => env('CLOUDFLARE_IMAGES_ACCOUNT_ID'),
    'api_token' => env('CLOUDFLARE_IMAGES_API_TOKEN'),
],
```
And add the following environment variables to your .env file:
```
CLOUDFLARE_IMAGES_ACCOUNT_ID=<account id>
CLOUDFLARE_IMAGES_API_TOKEN=<API token>
```

## Security Vulnerabilities

If you discover a security vulnerability within this project, please email me via [development@foodticket.nl](mailto:development@foodticket.nl).
