SSE_TranslationHints
======
Adds a new configuration option next to the template hints "Translation Hints" which will enable information about source of translations.

## Requirements ##

* Magento 1.x
* PHP 5.4 or higher

## Installation

1. Manual installation: download [the latest release](https://github.com/schmengler/TranslationHints/zipball/master) and copy the directories `app`, `js` and `skin` into the Magento installation.
2. Install via composer as dev dependency:

    "require-dev": {
        "sse/translationhints": "dev-master"
    }
    

## Configuration

Enable translation hints per website or per store view in System > Configuration > Advanced > Developer > Debug:

![Screenshot](https://github.com/schmengler/TranslationHints/raw/master/screenshot-configuration.png)

## Frontend

Translation hints replace a translated string `$translation` in the form `[__$translation__]($source)`.
If the translated string is inside a DOM text node, it gets converted to a tooltip icon via JavaScript:

![Screenshot](https://github.com/schmengler/TranslationHints/raw/master/screenshot-frontend.png)

Possible translation sources are *module*, *db* and *theme*.
Strings where the translation is the same as the original do not count as translated (Magento removes those from the translation array).

## Version 
* Version 0.1.0

## License 
* see [LICENSE](https://github.com/schmengler/TranslationHints/blob/master/license.txt) file
