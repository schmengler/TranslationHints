SSE_TranslationHints
======
Adds a new configuration option next to the template hints "Translation Hints" which will enable information about source of translations.

## Version 
* Version 0.2.3

## Requirements ##

* Magento 1.x
* PHP 5.4 or higher

## Installation

1. Manual installation: download [the latest release](https://github.com/schmengler/TranslationHints/zipball/master) and copy the directories `app`, `js` and `skin` into the Magento installation.
2. Install via composer as dev dependency:

        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/schmengler/TranslationHints.git"
            }
        },
        "require-dev": {
            "sse/translationhints": "~0.2.3"
        }
    

## Configuration

Enable translation hints per website or per store view in System > Configuration > Advanced > Developer > Debug:

![Screenshot](https://github.com/schmengler/TranslationHints/raw/master/screenshot-configuration.png)

## Frontend

Translation hints replace a translated string `$translation` in the form `[__$translation__]($source)`.
If the translated string is inside a DOM text node, it gets converted to a tooltip icon via JavaScript:

![Screenshot](https://github.com/schmengler/TranslationHints/raw/master/screenshot-frontend.png)

Possible translation sources are *module*, *db* and *theme*. The selected translation is marked with a green checkmark.

In the example above you see the scope of the translation (Mage_Customer), the translation for this scope,
as well as the translation that would be used for global scope, i.e. if there was no scope specific translation.
The CACHED tag tells us that the translations have been loaded from translation cache.

If you see a red cross next to a translation, it has been **discarded** because the translated string equals the
original string. This is a flaw in Magentos translation design which makes it impossible to force a translation like
"OK" => "OK" for one module as soon as any other module translates "OK" differently.

![Screenshot](https://github.com/schmengler/TranslationHints/raw/master/screenshot-discarded-translations.png)

## Technical Information

The extension rewrites `Mage_Core_Model_Translate`. If translation hints are not enabled, the behaviour does not change.

## Support

If you have any issues with this extension, open an issue on [GitHub](https://github.com/schmengler/TranslationHits/issues).

## Contribution

Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

## Developer

Fabian Schmengler
[http://www.schmengler-se.de](http://www.schmengler-se.de)  
[@fschmengler](https://twitter.com/fschmengler)

## License 
* see [LICENSE](https://github.com/schmengler/TranslationHints/blob/master/license.txt) file
