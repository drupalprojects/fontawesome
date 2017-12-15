/**
 * @file
 * Drupal Font Awesome plugin.
 *
 * @ignore
 */

(($, Drupal, drupalSettings, CKEDITOR) => {
  'use strict';

  CKEDITOR.plugins.add('drupalfontawesome', {
    icons: 'drupalfontawesome',
    hidpi: true,

    init(editor) {
      // Add the command for inserting Font Awesome icons.
      editor.addCommand('drupalfontawesome', {
        allowedContent: {
          i: {
            attributes: {
              '!class': true,
            },
            classes: {},
          },
        },
        requiredContent: new CKEDITOR.style({
          element: 'i',
          attributes: {
            class: '',
          },
        }),
        modes: { wysiwyg: 1 },
        canUndo: true,
        exec(execEditor) {
          // Prepare a save callback to be used upon saving the dialog.
          const saveCallback = (returnValues) => {
            execEditor.fire('saveSnapshot');

            // Create a new icon element if needed.
            const selection = execEditor.getSelection();
            const range = selection.getRanges(1)[0];

            // Create the icon text element.
            const icon = new CKEDITOR.dom.text('', execEditor.document);
            range.insertNode(icon);
            range.selectNodeContents(icon);
            // Apply the new style to the icon text.
            const style = new CKEDITOR.style({ element: 'i', attributes: returnValues.attributes });
            style.type = CKEDITOR.STYLE_INLINE;
            style.applyToRange(range);
            range.select();

            // Save snapshot for undo support.
            execEditor.fire('saveSnapshot');

            // Fire custom event so we can reload SVGs.
            execEditor.fire('insertedIcon');
          };

          // Drupal.t() will not work inside CKEditor plugins because CKEditor
          // loads the JavaScript file instead of Drupal. Pull translated
          // strings from the plugin settings that are translated server-side.
          const dialogSettings = {
            title: execEditor.config.drupalFontAwesome_dialogTitleAdd,
            dialogClass: 'fontawesome-icon-dialog',
          };

          // Open the dialog for the edit form.
          Drupal.ckeditor.openDialog(execEditor, Drupal.url(`fontawesome/dialog/icon/${execEditor.config.drupal.format}`), {}, saveCallback, dialogSettings);
        },
      });

      // Add button for icons.
      if (editor.ui.addButton) {
        editor.ui.addButton('DrupalFontAwesome', {
          label: Drupal.t('Font Awesome'),
          command: 'drupalfontawesome',
        });
      }
    },
  });
})(jQuery, Drupal, drupalSettings, CKEDITOR);
