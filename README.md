# AuthManager
The AuthManager is a CakePHP plugin which can be used for handling authentication such as OAUTH2.

Currently supported authentication methods:
- OAUTH2

Supported media platforms:
- Google Analytics
- Facebook Ads

Adding a new Media Platform
---
1. Copy the Lib/ExampleMediaPlatform folder.
2. Rename the folder, files and classes to the name of your new Media Platform.
3. Implement the methods that are defined, all methods currently throw an exception.
4. Add a constant to the MediaPlatform model.
 1. Also add an authentication type if necessary.
5. Add the Media Platform to the types array in Lib/MediaPlatformAuthManagerFactory.