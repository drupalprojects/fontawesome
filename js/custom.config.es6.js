/**
 * @file
 * Javascript custom config to prevent CKEditor from stripping certain tags.
 */

(($, Drupal, CKEDITOR) => {
  'use strict';

  // Allow empty tags in the CKEditor since Font Awesome requires them.
  $.each(drupalSettings.editor.formats.allowedEmptyTags, (_, tag) => {
    CKEDITOR.dtd.$removeEmpty[tag] = 0;
  });

  // Define FontAwesome conversion functions.
  Drupal.FontAwesome = {};

  // Converts HTML tags to SVG by loading the attached libraries.
  Drupal.FontAwesome.tagsToSvg = (drupalSettings, thisEditor) => {
    // Loop over each SVG library and include them. These convert the tags.
    $.each(drupalSettings.editor.formats.fontawesomeLibraries, (_, library) => {
      // Create a script.
      const $script = document.createElement('script');
      const $editorInstance = CKEDITOR.instances[thisEditor.editor.name];
      // Point the script at our library.
      $script.src = library;

      $editorInstance.document.getHead().$.appendChild($script);
    });
  };

  // Converts the resulting SVG tags back to their original HTML tags.
  Drupal.FontAwesome.svgToTags = (thisEditor) => {
    // Get the current body of text.
    let htmlBody = thisEditor.editor.getData();
    // Turn the SVGs back into their original icons.
    htmlBody = htmlBody.replace(/<svg .*?class="svg-inline--fa.*?<\/svg><!--\s?(.*?)\s?-->/g, '$1');
    // Set the body to the new value.
    thisEditor.editor.setData(htmlBody);
  };

  // After CKEditor is ready.
  CKEDITOR.on(
    'instanceReady',
    (ev) => {
      // On initial load, convert icons to SVGs.
      Drupal.FontAwesome.tagsToSvg(drupalSettings, ev);

      // On mode change, deal with the changes on the fly.
      ev.editor.on('mode', () => {
        if (ev.editor.mode === 'source') {
          // If we are showing source, turn SVG back to original tags.
          Drupal.FontAwesome.svgToTags(ev);
        }
        else if (ev.editor.mode === 'wysiwyg') {
          // If switching back to the display mode, have to load SVGs again.
          Drupal.FontAwesome.tagsToSvg(drupalSettings, ev);
        }
      });

      // Listen to the event for inserting icons from the plugin.
      ev.editor.on('insertedIcon', () => {
        // todo: For some reason this throws an 'Uncaught TypeError'.
        // Force an update to the content.
        ev.editor.setData(ev.editor.getData());
        // Then reload the SVGs.
        Drupal.FontAwesome.tagsToSvg(drupalSettings, ev);
      });
    },
  );
})(jQuery, Drupal, CKEDITOR);
