# Microsoft API PHP Wrapper
Microsoft provides its application registration via its server side of giant service service called Azure. In order to use its API and this wrapper you must need to register your own Microsoft App in Azure Portal.


### Installation
You can install this library to your project via composer using the following command:

`composer require smileandlearn/microsoft-api-php`

### Authentication
Microsoft uses OAuth 2.0 meaning Access Token is the key to pull data from the Microsoft servers on behalf of the user for which access token is generated. Access token is generated only for the nodes for which the authenticated user allowed your app to.

These nodes are called as scopes which dictates what types of data and functions can the app perform on the behalf of the authenticated user (against her access token). You can get the applicable scopes, in this [document](https://docs.microsoft.com/en-us/graph/permissions-reference "document").

This wrapper also supports tenants. If you want users of only your organization (Azure) to use this app, you can use respective tenant otherwise common will work fine for general microsoft user authentication.

```
session_start(); // Important
require "vendor/autoload.php";

use Smileandlearn\Microsoft\mAuth;

$tenant = "common"; 
$client_id = "6b152c50-4225-48f8-b824-..........";
$client_secret = "jXcajMv~SAFDDF~GBbvNPM7_Q0v5j02_p.";
$scopes = [
            "User.Read",
            'Files.Read.All',
            '.......'
        ];
$callback_url = "https://domain.com/callback.php";

$microsoft = new mAuth($tenant, $client_id,  $client_secret, $callback, $scopes);
```
    
###    Fetching & Setting Access Token (Shorter life)
```php
    // INITIALIZATION
$microsoft = new mAuth($tenant, $client_id,  $client_secret, $callback, $scopes);
    header("location: ". $microsoft->getAuthUrl()); //Redirecting to get access token
```
```php
// ON CALLBACK
session_start(); 
require "vendor/autoload.php";

use Smileandlearn\Microsoft\mAuth;
use Smileandlearn\Microsoft\Handlers\mSession;

$microsoft = new mAuth(mSession::get("tenant_id"),mSession::get("client_id"),  mSession::get("client_secret"), mSession::get("redirect_uri"), mSession::get("scopes"));
$tokens = $microsoft->getToken($_REQUEST['code'], mSession::get("state"));

// Setting access token to the wrapper
$microsoft->setAccessToken($tokens->access_token);
 
```

### Fetching user data
Once the access token is set to the wrapper, you can just initialize the User model and the related data will get pulled out for you.
```php
use Smileandlearn\Microsoft\Models\User;

$user = (new mUser); // User get pulled only if access token was generated for scope User.Read
echo $user->data->getGivenName(); // Adnan
echo $user->data->getOnPremisesImmutableId(); // adnanhussainturki@gmail.com

 
```

###     Fetching Refresh Token (Longer Life)
For fetching refresh token along with the access token, you need to provide `offline_access` as an scope and then fetch access token. On the callback, you  will recieve refresh token along with access token.
```php
// ON CALLBACK
session_start(); 
require "vendor/autoload.php";

use Smileandlearn\Microsoft\mAuth;
use Smileandlearn\Microsoft\Handlers\mSession;

$microsoft = new mAuth(mSession::get("tenant_id"),mSession::get("client_id"),  mSession::get("client_secret"), mSession::get("redirect_uri"), mSession::get("scopes"));
$tokens = $microsoft->getToken($_REQUEST['code'], mSession::get("state"));

$refreshToken = $tokens->refresh_token;
 
```
###     Using Refresh Token
If you have active refresh token for an user, you do need ask for the user permission to get accesss token against that user. You can set the refresh token to the wrapper and the wrapper will fetch access token for you against it.

```php

$refreshToken = "M.R3_BAY.CbCa*dfsafayrRe9NFNcFEWJBZF9*sXaIYH1HHEFb6i2uUFCGT0KvyXzXulrjPqC3qRgw*NAuajBICU6PmdvfHOyeWGdmE8tUZ4f6XSluF3aKHBGbs*FGSvY7nkUgHhJ*F*4Pfg6SLuNNHY8mh6U8pMNuY1EwnKgAI9s1X4Tt0VXm*mIeLoiw8MTifTukr1aK!7rQOA18ow84bOSpPyu7lZbwATC2pygflRZEOPiHi2!MGrw6CuCxLPgGVu88rsWZJJw3rLjSTofJF78Sgb8ZjkIJAwcfZukotN0lF0GaTThWvM35QEricRyVBYxIC*8iXywFmqKkeClJFeVYx!US35inDel3oXg9**jtd8FAN7x!050JGWN7iJgJA!eMg4h1L6PjcmCZfuVnv0s5eGJ3jauimRBPKJLT6rgzVvkAtI5mJitumZzKnzQNRCxn03w$$";

$microsoft = new mAuth(mSession::get("tenant_id"),mSession::get("client_id"),  mSession::get("client_secret"), mSession::get("redirect_uri"), mSession::get("scopes"));
 
$microsoft->setRefreshToken($refreshToken);
$accessToken = $auth->setAccessToken();

$user = (new mUser); // User get pulled only if refresh token was generated for scope User.Read
echo $user->data->getGivenName(); // Adnan
echo $user->data->getOnPremisesImmutableId(); // adnanhussainturki@gmail.com

 
```


### License
Licensed under Apache 2.0. You can check its details [here](https://choosealicense.com/licenses/apache-2.0/ "here").
