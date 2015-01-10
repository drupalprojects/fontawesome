
CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Installation
 * Credits


INTRODUCTION
------------
Font Awesome (http://fontawesome.io) is the iconic font designed for use with
Bootstrap (http://getbootstrap.com). 1.x version of this module supports 
Font Awesome v3.2.1 library (http://fortawesome.github.io/Font-Awesome/3.2.1)

"fontawesome" module provides integration of "Font Awesome" with Drupal. Once enabled
"Font Awesome" icon fonts could be used as:

1. Directly inside of any HTML (node/block/view/panel). Inside HTML you can
   place Font Awesome icons just about anywhere with an <i> or a <span> tag.

   Example for an info icon: <i class="icon icon-flag"></i>

   See more examples of using "Font Awesome" within HTML at:
   http://fortawesome.github.io/Font-Awesome/3.2.1/examples/

2. Icon API (https://drupal.org/project/icon) integration:
   This module provides easy to use interfaces that quickly allow you to inject
   icons in various aspects of your Drupal site: blocks, menus, fields, filters.


INSTALLATION
------------

1. Using Drush (https://github.com/drush-ops/drush#readme)

    $ drush pm-enable fontawesome

    Upon enabling, this will also attempt to download and install the library
    in `sites/all/libraries/fontawesome`. If, for whatever reason, this process
    fails, you can re-run the library install manually by first clearing Drush
    caches:

    $ drush cc drush

    and then using:-

    $ drush fa-download

2. Manually

    a. Install the "Font Awesome" library following one of these 2 options:
       - run "drush fa-download" (recommended, it will download the right
         package and extract it at the right place for you.)
       - manual install: Download & extract "Font Awesome" 
         (http://fontawesome.io) and place inside 
         "sites/all/libraries/fontawesome" directory. The CSS file should
         be sites/all/libraries/fontawesome/css/font-awesome.css
         Direct link for downloading version 3.2.1 is: 
         http://fortawesome.github.io/Font-Awesome/3.2.1/assets/font-awesome.zip
    b. Enable the module at Administer >> Site building >> Modules.

CREDITS
-------
* Rob Loach (RobLoach) http://robloach.net
* Inder Singh (inders) http://indersingh.com | https://www.drupal.org/u/inders
* Mark Carver https://www.drupal.org/u/mark-carver
* Brian Gilbert https://drupal.org/u/realityloop
