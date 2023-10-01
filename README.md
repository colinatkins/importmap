# TYPO3 Extension `importmap`

Easily import your JavaScript ES modules without the need of a bundler like Webpack
within the TYPO3 frontend.

## Getting started

Install importmap with the following command through composer or install it from extension repository:

```bash
composer require atkins/importmap
```

## Configure

Specify an importmap to the PAGE-object of your TypoScript e.g.

```bash
page = PAGE
page.importmap {
    application {
        path = EXT:your_extension/Resources/Public/JavaScript/application.js
    }
    moduleKey {
        path = EXT:your_extension/Resources/Public/JavaScript/modules/module-1.2.3.js
        preload = 1
    }
}
```

Always make sure to specify a path for the "application" module key as it is always used to
bootstrap your JavaScript application. You should always use a path relative of the Public-folder 
from your provider extension. You can check out a working example at the EXT:pagedoctor_starter
extension [here](https://github.com/pagedoctor/t3-starter).


## Usage

Add an import statement to the top of your root application.js file to import the module:

```js
import ModuleName from 'moduleKey'

// Use module here...
ModuleName.doSomething()
```
