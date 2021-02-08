### PHP Client for Admiralcloud

This is a simple wrapper to use the admiralcloud search API.

#### Installation

```shell
composer req sioweb/admiralcloud-client
```

#### Test

**.env**

Create a .env file in the root of your project and add your API data:

```
AC_API_URL="https://api.admiralcloud.com"
AC_API_KEY="......................"
AC_SECRET_KEY="........-....-....-....-............"
AC_API_VERSION="v5"
```

**public/index.php**

Create a file in a subdirectory of your project: `public/index.php`. Its not necessary how you name the public dir, it also could be named `web`, or what ever you prever. The index.php file just **should not** be in the same level as `vendor`.

```php
<?php

use Sioweb\AdmiralcloudClient\Api\Request;

include '../vendor/autoload.php';


$Request = new Request();

die('<pre>' . print_r($Request->mediacontainerSearch([
    'searchTerm' => 'Exaple value in your own metadata field',
    'field' => 'meta_yourOwnMetadataField'
]), true) . '</pre>');
```

#### Unable to read the "/.../.env" environment file

You can set a custom path to your .env file:

```php
$Request = new Request('/var/www/html/yourproject/');
```
