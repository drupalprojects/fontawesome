/**
 * @file
 * Javascript custom config to prevent CKEditor from stripping certain tags.
 */

 (function ($, drupalSettings, CKEDITOR) {
  'use strict';

  $.each(drupalSettings.editor.formats.allowedEmptyTags, function (_, v) {
    CKEDITOR.dtd.$removeEmpty[v] = 0;
  });
})(jQuery, drupalSettings, CKEDITOR);
