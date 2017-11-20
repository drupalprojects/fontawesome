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
    $path = DRUPAL_ROOT . '/libraries/fontawesome';
  }
  // Create the path if it does not exist yet. Added substr check for preventing
  // any wrong attempts or hacks !
  if (substr($path, -11) == 'fontawesome' && !is_dir($path)) {
    drush_mkdir($path);
  }
  if (is_dir($path . '/css')) {
    drush_log(dt('Fontawesome already present at @path. No download required.', ['@path' => $path]), 'ok');
  }
  elseif (drush_op('chdir', $path) &&
      $fontawesome_library = \Drupal::service('library.discovery')->getLibraryByName('fontawesome', 'fontawesome.cdn');
      drush_shell_exec('wget ' . $fontawesome_library['remote'] . '  -O fontawesome.zip') &&
      drush_shell_exec('unzip fontawesome.zip') &&
      drush_shell_exec('mv Font-Awesome-*/* .') &&
      drush_shell_exec('rm -rf Font-Awesome-*')) {
    drush_log(dt('The latest Fontawesome library has been downloaded to @path', ['@path' => $path]), 'success');
  }
  else {
    drush_log(dt('Drush was unable to download the Fontawesome library to @path', ['@path' => $path]), 'error');
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
