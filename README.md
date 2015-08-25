# Mediawiki-OpauthLogin
Handles user authorization based on Mediawiki-Opauth. This extension requres [Mediawiki-Opauth](https://github.com/vedmaka/Mediawiki-Opauth) to be installed.

# Installation

1. Put **OpauthLogin** folder into **extensions** folder
2. Add these lines into **LocalSettings.php** at bottom:
```php
require_once "$IP/extensions/OpauthLogin/OpauthLogin.php";
```
3. Run mediawiki update script:
```bash
php maintenance/update.php --quick
```
4. Done. Now users will be handled automatically when logged/registerd via social networks (via Opauth). 
Please notice that by default users will have name set to randomized string and their nicknames from social netowrks will be put in "real name" field of users table.
