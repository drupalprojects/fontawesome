<?php

namespace Drupal\fontawesome\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a Font Awesome icon dialog for text editors.
 */
class EditorIconDialog extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fontawesome_icon_dialog';
  }

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   The text editor to which this dialog corresponds.
   */
  public function buildForm(array $form, FormStateInterface $form_state, Editor $editor = NULL) {
    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 'editor/drupal.editor.dialog';

    $form['#prefix'] = '<div id="fontawesome-icon-dialog-form">';
    $form['#suffix'] = '</div>';

    $form['information'] = [
      '#type' => 'container',
      '#attributes' => [],
      '#children' => $this->t('For more information on icon selection, see @iconLink. If an icon below is displayed with a question mark, it is like a Font Awesome Pro icon, unavailable with the free version of Font Awesome.', [
        '@iconLink' => Link::fromTextAndUrl($this->t('the Font Awesome icon list'), Url::fromUri('https://fontawesome.com/icons'))->toString(),
      ]),
    ];

    $form['preview'] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#value' => $this->t('Preview'),
      ],
      [
        '#type' => 'container',
        '#attributes' => [
          'id' => 'fontawesome-icon-preview',
        ],
        '#children' => $this->t('<i class=":iconclass"></i> :iconclass', [
          ':iconclass' => $this->buildClassString('solid', ['flag']),
        ]),
      ],
    ];

    // Build additional settings.
    $form['settings'] = [
      '#type' => 'details',
      '#open' => FALSE,
      '#title' => $this->t('Additional Settings'),
    ];
    // Allow user to determine size.
    $form['settings']['style'] = [
      '#type' => 'select',
      '#title' => $this->t('Style'),
      '#description' => $this->t('This changes the style of the icon. Please note that this is not available for all icons, and for some of the icons this is only available in the pro version. If the icon does not render properly in the preview above, the icon does not support that style. Notably, brands do not support any styles. See @iconLink for more information.', [
        '@iconLink' => Link::fromTextAndUrl($this->t('the Font Awesome icon list'), Url::fromUri('https://fontawesome.com/icons'))->toString(),
      ]),
      '#options' => [
        'solid' => $this->t('Solid'),
        'regular' => $this->t('Regular'),
        'light' => $this->t('Light'),
      ],
      '#default_value' => 'fas',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    // Allow user to determine size.
    $form['settings']['size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size'),
      '#description' => $this->t('This increases icon sizes relative to their container'),
      '#options' => [
        '' => $this->t('Default'),
        'lg' => $this->t('Large'),
        '2x' => $this->t('2x'),
        '3x' => $this->t('3x'),
        '4x' => $this->t('4x'),
        '5x' => $this->t('5x'),
      ],
      '#default_value' => '',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    // Set icon to fixed width.
    $form['settings']['fixed-width'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fixed Width?'),
      '#description' => $this->t('Use to set icons at a fixed width. Great to use when different icon widths throw off alignment. Especially useful in things like nav lists & list groups.'),
      '#default_value' => FALSE,
      '#return_value' => 'fw',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    // Add border.
    $form['settings']['border'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Border?'),
      '#description' => $this->t('Adds a border to the icon.'),
      '#default_value' => FALSE,
      '#return_value' => 'border',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    // Invert color.
    $form['settings']['invert'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Invert color?'),
      '#description' => $this->t('Inverts the color of the icon (black becomes white, etc.)'),
      '#default_value' => FALSE,
      '#return_value' => 'inverse',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    // Rotate/flip the icon..
    $form['settings']['rotate'] = [
      '#type' => 'select',
      '#title' => $this->t('Rotate'),
      '#options' => [
        '' => $this->t('None'),
        'rotate-90' => $this->t('90°'),
        'rotate-180' => $this->t('180°'),
        'rotate-270' => $this->t('270°'),
      ],
      '#default_value' => '',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    $form['settings']['flip'] = [
      '#type' => 'select',
      '#title' => $this->t('Flip'),
      '#options' => [
        '' => $this->t('None'),
        'flip-horizontal' => $this->t('Horizontal'),
        'flip-vertical' => $this->t('Vertical'),
      ],
      '#default_value' => '',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    // Animated the icon.
    $form['settings']['animation'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation'),
      '#options' => [
        '' => $this->t('None'),
        'spin' => $this->t('Spin'),
        'pulse' => $this->t('Pulse'),
      ],
      '#default_value' => '',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];

    // Pull the icons.
    $form['settings']['pull'] = [
      '#type' => 'select',
      '#title' => $this->t('Pull'),
      '#description' => $this->t('This setting will pull the icon (float) to one side or the other in relation to its nearby content'),
      '#options' => [
        '' => $this->t('None'),
        'pull-left' => $this->t('Left'),
        'pull-right' => $this->t('Right'),
      ],
      '#default_value' => '',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];

    // Select an icon.
    $form['icon'] = [
      '#type' => 'radios',
      '#title' => $this->t('Icon'),
      '#description' => $this->t('For information on how to stack icons or use them in ordered lists, see @iconLink.', [
        '@iconLink' => Link::fromTextAndUrl($this->t('the guide on how to use Font Awesome'), Url::fromUri('https://fontawesome.com/how-to-use/svg-with-js'))->toString(),
      ]),
      '#options' => [],
      '#default_value' => 'solid flag',
      '#ajax' => [
        'callback' => [$this, 'previewIcon'],
        'wrapper' => 'fontawesome-icon-preview',
        'method' => 'html',
      ],
    ];
    // Add all icon options to the list.
    foreach (fontawesome_extract_icons() as $icon) {
      // Build the preview icon.
      $iconClass = $this->buildClassString($icon['type'], [
        $icon['name'],
        'lg',
        'fw',
      ]);
      $form['icon']['#options'][$icon['type'] . ' ' . $icon['name']] = $this->t('<i class=":iconclass" title=":iconname"></i> :iconname', [
        ':iconclass' => $iconClass,
        ':iconname' => $icon['name'],
      ]);
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['save_modal'] = [
      '#type' => 'submit',
      '#value' => $this->t('Insert Icon'),
      // No regular submit-handler. This form only works via JavaScript.
      '#submit' => [],
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * Callback for previewing the Icon.
   */
  public function previewIcon(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValues();
    // We need both the type and the name to render properly.
    $iconInfo = explode(' ', $form_values['icon']);
    $iconType = $iconInfo[0];
    $iconName = $iconInfo[1];
    // Determine the type if it is different from the default.
    // Don't allow style changes for brands.
    if ($iconType != 'brands') {
      $iconType = $form_values['settings']['style'];
    }
    unset($form_values['settings']['style']);

    // Build the icon class.
    $iconClass = $this->buildClassString($iconType, [$iconName] + $form_values['settings']);
    return [
      '#type' => 'html_tag',
      '#tag' => 'i',
      '#attributes' => [
        'class' => $iconClass,
      ],
      '#suffix' => $iconClass,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    if ($form_state->getErrors()) {
      unset($form['#prefix'], $form['#suffix']);
      $form['status_messages'] = [
        '#type' => 'status_messages',
        '#weight' => -10,
      ];
      $response->addCommand(new HtmlCommand('#fontawesome-icon-dialog-form', $form));
    }
    else {
      $form_values = $form_state->getValues();
      // We need both the type and the name to render properly.
      $iconInfo = explode(' ', $form_values['icon']);
      $iconType = $iconInfo[0];
      $iconName = $iconInfo[1];
      // Determine the type if it is different from the default.
      // Don't allow style changes for brands.
      if ($iconType != 'brands') {
        $iconType = $form_values['settings']['style'];
      }
      unset($form_values['settings']['style']);

      $icon_attributes = [
        'attributes' => [
          'class' => $this->buildClassString($iconType, [$iconName] + $form_values['settings']),
        ],
      ];

      $response->addCommand(new EditorDialogSave($icon_attributes));
      $response->addCommand(new CloseModalDialogCommand());
    }

    return $response;
  }

  /**
   * Build Font Awesome class string from an array of attributes.
   *
   * @param string $type
   *   The icon type being displayed.
   * @param array $attributes
   *   The attributes being used for this Font Awesome icon.
   *
   * @return string
   *   The class string for rendering an icon.
   */
  private function buildClassString($type, array $attributes) {
    // We have to prefix our icons differently depending on type.
    switch ($type) {
      case 'brands':
        $prefix = 'fab';
        break;

      case 'light':
        $prefix = 'fal';
        break;

      case 'solid':
        $prefix = 'fas';
        break;

      case 'regular':
      default:
        $prefix = 'far';
        break;
    }

    $attributes = array_filter($attributes);
    foreach ($attributes as &$attribute) {
      if (substr($attribute, 0, 3) != 'fa-') {
        $attribute = 'fa-' . $attribute;
      }
    }
    return $prefix . ' ' . implode(' ', $attributes);
  }

}
