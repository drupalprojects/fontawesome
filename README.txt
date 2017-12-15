
CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Installation
 * Credits


INTRODUCTION 8.2.x version
------------
Font Awesome (http://fontawesome.com) is the web's most popular icon set and
toolkit. This release of the Font Awesome Icons module supports Font Awesome
versions higher than 5.0. For older versions of Font Awesome, you should
download and install Font Awesome Icons 8.1.x. See the Font Awesome Icons
page on Drupal.org for more information.

"fontawesome" provides integration of "Font Awesome" with Drupal. Once enabled
"Font Awesome" icon fonts could be used as:

1. Directly inside of any HTML (node/block/view/panel). Inside HTML you can
   place Font Awesome icons just about anywhere with an <i> tag.

   Example for an info icon: <i class="fas fa-camera-retro"></i>

   See more examples of using "Font Awesome" within HTML at:
   https://fontawesome.com/how-to-use/svg-with-js


INSTALLATION
------------

1. Using Drush (https://github.com/drush-ops/drush#readme)

    $ drush en fontawesome

    Upon enabling, this will also attempt to download and install the library
    in `/libraries/fontawesome`. If, for whatever reason, this process
    fails, you can re-run the library install manually by first clearing Drush
    caches:

    $ drush cc drush

    and then using another drush command:-

    $ drush fa-download

2. Manually

    a. Install the "Font Awesome" library following one of these 2 options:
       - run "drush fa-download" (recommended, it will download the right
         package and extract it at the right place for you.)
       - manual install: Download & extract "Font Awesome"
         (http://fontawesome.com) and place inside
         "/libraries/fontawesome" directory. The JS file should
         be at /libraries/fontawesome/svg-with-js/js/fontawesome-all.js
         Direct link for downloading latest version (current is v5.0.1) is:
         https://use.fontawesome.com/releases/v5.0.1/fontawesome-free-5.0.1.zip
    b. Enable the module at Administer >> Site building >> Modules.

CREDITS
-------
* Rob Loach (RobLoach) http://robloach.net
* Inder Singh (inders) http://indersingh.com | https://www.drupal.org/u/inders
* Mark Carver https://www.drupal.org/u/mark-carver
* Brian Gilbert https://drupal.org/u/realityloop
* Daniel Moberly https://drupal.org/u/danielmoberly
