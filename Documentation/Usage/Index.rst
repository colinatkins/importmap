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

The corresponding TypoScript configuration would look like this:

.. code-block:: typoscript

    page.importmap {
        application {
            path = EXT:your_extension/Resources/Public/JavaScript/application.js
        }
        stimulus {
            path = EXT:your_extension/Resources/Public/JavaScript/modules/stimulus.js
            preload = 1
        }
        alpinejs {
            path = EXT:your_extension/Resources/Public/JavaScript/modules/alpinejs.js
            preload = 1
        }
        controllers/header_controller {
            path = EXT:your_extension/Resources/Public/JavaScript/controllers/header_controller.js
            preload = 1
        }
    }
