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
        modes: { wysiwyg: 1 },
        canUndo: true,
        exec(execEditor) {
          // Prepare a save callback to be used upon saving the dialog.
          const saveCallback = (returnValues) => {
            execEditor.fire('saveSnapshot');

            // Create a new icon element if needed.
            const selection = execEditor.getSelection();
            const range = selection.getRanges(1)[0];

            // Create the container span for the icon.
            var container = new CKEDITOR.dom.element('span', execEditor.document);
            container.addClass('fontawesome-icon-inline');
            // Create the icon element from the editor.
            var icon = new CKEDITOR.dom.element(returnValues.tag, execEditor.document);
            icon.setAttributes(returnValues.attributes);
            // Add the icon to the container.
            container.append(icon);
            // CKEditor doesn't play well with SVG - this allows editing.
            container.appendHtml('&nbsp;');

            // Add the container to the range.
            range.insertNode(container);
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
