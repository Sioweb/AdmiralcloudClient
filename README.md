### PHP Client for Admiralcloud

This is a simple example wrapper to use the admiralcloud search API.

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

To test the requests, just remove the comments (// ) from behind a die()-block.

```php
<?php

use Sioweb\AdmiralcloudClient\Api\Request;

include '../vendor/autoload.php';

$Request = new Request();

// // Get Media by mediaContainerId & mediaId
// die(json_encode($Request->media([
//     'mediaContainerId' => 9999999,
//     'mediaId' => 9999999
// ])));


// // Get Media by mediaContainerId only
// die(json_encode($Request->media([
//     'mediaContainerId' => 9999999
// ])));


// Get Mediacontainer by mediaContainerId (not id!)
// die(json_encode($Request->mediacontainer([
//     'mediaContainerId' => 9999999
// ])));


// // Get 250 Media IDs from Mediacontainer
// die(json_encode($Request->mediacontainerBatch()));


// // Get 10 Media IDs from Mediacontainer
// die(json_encode($Request->mediacontainerBatch([
//     'limit' => 10
// ])));


// // how to search by meta fields
// die(json_encode($Request->search([
//     'searchTerm' => 'lorem ipsum',
//     'field' => 'meta_yourOwnMetaDataField'
// ])));


```

#### Unable to read the "/.../.env" environment file

You can set a custom path to your .env file:

```php
$Request = new Request('/var/www/html/yourproject/');
```
