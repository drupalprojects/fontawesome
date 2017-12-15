<?php

/**
 * @file
 * Drush integration for fontawesome.
 */

/**
 * Implements hook_drush_command().
 */
function fontawesome_drush_command() {
  $items = [];

  $items['fa-download'] = [
    'callback' => 'fontawesome_drush_lib_download',
    'description' => dt('Downloads the required Fontawesome library from http://fortawesome.github.io'),
    'aliases' => ['fadl'],
    'arguments' => [
      'path' => dt('Optional. A path to the fontawesome module. If omitted Drush will use the default location.'),
    ],
  ];
  return $items;
}

/**
 * Implements hook_drush_help().
 */
function fontawesome_drush_help($section) {
  switch ($section) {
    case 'drush:fa-download':
      return dt("Downloads the required Fontawesome library from http://fortawesome.github.io.");
  }
}

/**
 * Drush command callback to install fontawesome.
 */
function fontawesome_drush_lib_download($path = '') {
  if (empty($path)) {
    // We have dependencies on libraries module so no need to check for that
    // TODO: any way to get path for libraries directory?
    // Just in case if it is site specific? e.g. sites/domain.com/libraries ?
    $path = drush_get_context('DRUSH_DRUPAL_ROOT') . '/libraries/fontawesome';
  }

  // Create the path if it does not exist yet. Added substr check for preventing
  // any wrong attempts or hacks !
  if (substr($path, -11) == 'fontawesome' && !is_dir($path)) {
    drush_mkdir($path);
  }
  if (is_dir($path . '/css')) {
    drush_log(dt('Font Awesome already present at @path. No download required.', ['@path' => $path]), 'ok');
  }
  else {
    // Change to the Font Awesome destination directory.
    if (drush_op('chdir', $path)) {
      // Load the Font Awesome defined library.
      if ($fontawesome_library = \Drupal::service('library.discovery')->getLibraryByName('fontawesome', 'fontawesome.svg')) {
        // Attempt to download the remote Font Awesome.
        if (drush_shell_exec('wget ' . $fontawesome_library['remote'] . '  -O fontawesome.zip')) {
          // Attempt to unzip Font Awesome to the library directory.
          if (drush_shell_exec('unzip fontawesome.zip') && drush_shell_exec('mv fontawesome-free-' . $fontawesome_library['version'] . '/* .')) {
            // Here we have a working Font Awesome installation.
            drush_log(dt('The latest Font Awesome library has been downloaded to @path', ['@path' => $path]), 'success');
            // Remove the downloaded files.
            drush_shell_exec('rm -rf fontawesome-free-' . $fontawesome_library['version'] . ' fontawesome.zip');
          }
          else {
            // Clear the directory.
            drush_shell_exec('rm -rf ' . $path . '/*');
            drush_log(dt('Drush was unable to properly unzip and place the Font Awesome library'), 'error');
          }
        }
        else {
          // Clear the directory.
          drush_shell_exec('rm -rf ' . $path . '/*');
          drush_log(dt('Drush was unable to download the Font Awesome library from @remote', ['@remote' => $fontawesome_library['remote']]), 'error');
        }
      }
      else {
        drush_log(dt('Drush was unable to load the Font Awesome library'), 'error');
      }
    }
    else {
      drush_log(dt('Drush was unable to find the Font Awesome directory at @path', ['@path' => $path]), 'error');
    }
  }
}

/**
 * Implements drush_MODULE_post_COMMAND().
 */
function drush_fontawesome_post_pm_enable() {
  $extensions = func_get_args();
  // Deal with comma delimited extension list.
  if (strpos($extensions[0], ',') !== FALSE) {
    $extensions = explode(',', $extensions[0]);
  }

  if (in_array('fontawesome', $extensions) && !drush_get_option('skip')) {
    fontawesome_drush_lib_download();
  }
}
