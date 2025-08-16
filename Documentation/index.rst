pluploadbe
==========

PluploadBE is a TYPO3 backend extension that integrates **Plupload**, enabling multi-file uploads with progress tracking, client/server-side image resizing, and file validation.

Features
--------

- Multiple file uploads with drag-and-drop support
- Progress bar for uploads
- File size and type restrictions
- Client-side and server-side image resizing
- Automatic error handling
- Cleanup of broken files

Installation
------------

**Via Composer (recommended):**

.. code-block:: bash

   composer require syntaxoops/pluploadbe

Configuration
-------------

- Configure allowed file types and max file size in the extension settings.
- Optional: Configure image auto-resize:
  - Mode: `0 = Off`, `1 = Client-side`, `2 = Server-side`
  - Width / Height / Quality

Usage
-----

Upload large files and monitor progress and errors.

Contributing
------------

Contributions are welcome! Please fork the repository and submit a pull request with improvements or bug fixes.

License
-------

MIT License – see the `LICENSE <https://github.com/SyntaxOops/pluploadbe/blob/main/LICENSE>`_ file.
