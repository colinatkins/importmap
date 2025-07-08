.. include:: /Includes.rst.txt


.. _usage:

=====
Usage
=====

Once you defined your importmap you are able to import your modules within the application module or
any other loaded module file within your importmap context.

.. _how-to-import-modules:

How to import modules in JavaScript?
====================================

The import statement has to be added to the top of your **application** file specified at
`importmap.application.path`:

.. code-block:: js

    import { Application } from 'stimulus'
    import Alpine from 'alpinejs'
    import HeaderController from 'controllers/header_controller'

The corresponding YAML configuration would look like this:

.. code-block:: yaml

    page:
        importmap:
            application:
            path: EXT:your_extension/Resources/Public/JavaScript/application.js
            modules/stimulus:
                path: EXT:your_extension/Resources/Public/JavaScript/modules/stimulus-3.2.2.js
                preload: 1
                override: '@hotwired/stimulus'
            modules/turbo:
                path: EXT:your_extension/Resources/Public/JavaScript/modules/turbo-8.0.13.js
                preload: 1
                override: '@hotwired/turbo'
            modules/alpinejs:
                path: EXT:your_extension/Resources/Public/JavaScript/modules/alpinejs-3.14.9.js
                preload: 1
                override: alpinejs
            controllers/hello_controller:
                path: EXT:your_extension/Resources/Public/JavaScript/controllers/hello_controller.js
