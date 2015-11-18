# AuthManager
The AuthManager is a CakePHP plugin which can be used for handling authentication such as OAUTH2.

Currently supported authentication methods:
- OAUTH2

Supported media platforms:
- Google Analytics
- Facebook Ads
- Bitly
- Google Tag Manager
- Google Webmaster Tools
- Bing Ads

Adding a new Media Platform
---
1. Copy the Lib/ExampleMediaPlatform folder.
2. Rename the folder, files and classes to the name of your new Media Platform.
3. Add a constant to the MediaPlatform model and also to the media_platforms table.
 1. Also add an authentication type if necessary.
4. Implement the methods that are defined, all methods currently throw an exception.
5. Add the Media Platform to the types array in Lib/MediaPlatformAuthManagerFactory.
