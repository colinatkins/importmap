# TYPO3 Extension `importmap`

Easily import your JavaScript ES modules without the need of a bundler like Webpack
within the TYPO3 frontend.

## Getting started

Install importmap with the following command through composer or install it from extension repository:

```bash
composer require atkins/importmap
```

## Configure

Specify an importmap in your site sets settings.yaml file:

```bash
page:
  importmap:
    application:
      path: EXT:sitepackage/Resources/Public/JavaScript/application.js
    modules/stimulus:
        path: EXT:sitepackage/Resources/Public/JavaScript/modules/stimulus-3.2.2.js
        preload: 1
        override: '@hotwired/stimulus'
    controllers/hello_controller:
        path: EXT:sitepackage/Resources/Public/JavaScript/controllers/hello_controller.js
```

Always make sure to specify a path for the "application" module key as it is always used to
bootstrap your JavaScript application. You should always use a path relative of the Public-folder 
from your provider extension.


## Usage

Add an import statement to the top of your root application.js file to import the module:

```js
import ModuleName from 'moduleKey'

// Use module here...
ModuleName.doSomething()
```
