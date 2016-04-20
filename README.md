# AuthManager
The AuthManager is a CakePHP plugin which can be used for handling authentication such as OAUTH2. Compatible with PHP 5.4+.

Currently supported authentication methods:
- OAUTH2

Supported media platforms:

1. Google Analytics
2. Facebook Ads
3. Bitly
4. Google Tag Manager
5. Google Webmaster Tools
6. Bing Ads
7. DoubleClick DCM/DFA Reporting and Trafficking API and DoubleClick Bid Manager API
8. Coosto (DCM/DFA Reporting and Trafficking API)

Usage
---

### Authenticate user
To add an user you should POST to `/AuthManager/MediaPlatformUser/addUser` with:

```php
[
  'MediaPlatform' => [
    'id' => <MEDIA_PLATFORM_ID>
  ]
]
```

Where `<MEDIA_PLATFORM_ID>` is a MediaPlatform ID that is implemented by the AuthManager. This page will redirect the user to an OAUTH page of the media platform asking for permission. The callback URL is automatically set, which is `AuthManager/MediaPlatformUser/callback/<MEDIA_PLATFORM_ID>`.

### Using AuthContainer
Once an user has been added, you can get it's credentials through the `AuthContainerFactory`. You get an AuthContainer by doing the following:

```php
$authContainerFactory = new AuthContainerFactory();
// Where 1 is the MediaPlatformUser ID.
$authContainerFactory->createAuthContainer(1);
// If MediaPlatformUser ID #1 is a Facebook Ads account, it would return;
//> FacebookAdsAuthContainer
```

The AuthContainer contains different attributes depending on the media platform. For example `FacebookAdsAuthContainer` contains;

- `Facebook $facebookSdk`
- `\FacebookAds\Api $facebookAds`

With these objects, which are Facebook's API objects, you can execute any API calls as the authenticated user.

Adding a new Media Platform
---
1. Copy the Lib/ExampleMediaPlatform folder.
2. Rename the folder, files and classes to the name of your new Media Platform.
3. Add a constant to the MediaPlatform model and also to the media_platforms table.
 1. Also add an authentication type if necessary.
4. Implement the methods that are defined, all methods currently throw an exception.
5. Add the Media Platform to the types array in Lib/MediaPlatformAuthManagerFactory.
