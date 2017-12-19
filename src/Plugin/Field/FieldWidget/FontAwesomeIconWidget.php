<?php

namespace Drupal\fontawesome\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Plugin implementation of the 'fontawesome_icon' widget.
 *
 * @FieldWidget(
 *   id = "fontawesome_icon_widget",
 *   module = "fontawesome",
 *   label = @Translation("Font Awesome Icon"),
 *   field_types = {
 *     "fontawesome_icon"
 *   }
 * )
 */
class FontAwesomeIconWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();

    $element['icon_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon Name'),
      '#size' => 50,
      '#field_prefix' => 'fa-',
      '#default_value' => $items[$delta]->get('icon_name')->getValue(),
      '#description' => $this->t('Name of the Font Awesome Icon. See @iconsLink for valid icon names, or begin typing for an autocomplete list.', [
        '@iconsLink' => Link::fromTextAndUrl($this->t('the Font Awesome icon list'), Url::fromUri('https://fontawesome.com/icons'))->toString(),
      ]),
      '#autocomplete_route_name' => 'fontawesome.autocomplete',
      '#element_validate' => [
        [static::class, 'validateIconName'],
      ],
    ];

    // Get current settings.
    $iconSettings = unserialize($items[$delta]->get('settings')->getValue());
    // Build additional settings.
    $element['settings'] = [
      '#type' => 'details',
      '#open' => FALSE,
      '#title' => $this->t('Additional Font Awesome Settings'),
    ];
    // Allow user to determine size.
    $element['settings']['style'] = [
      '#type' => 'select',
      '#title' => $this->t('Style'),
      '#description' => $this->t('This changes the style of the icon. Please note that this is not available for all icons, and for some of the icons this is only available in the pro version. If the icon does not render properly in the preview above, the icon does not support that style. Notably, brands do not support any styles. See @iconLink for more information.', [
        '@iconLink' => Link::fromTextAndUrl($this->t('the Font Awesome icon list'), Url::fromUri('https://fontawesome.com/icons'))->toString(),
      ]),
      '#options' => [
        'fas' => $this->t('Solid'),
        'far' => $this->t('Regular'),
        'fal' => $this->t('Light'),
      ],
      '#default_value' => $items[$delta]->get('style')->getValue(),
    ];
    // Allow user to determine size.
    $element['settings']['size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size'),
      '#description' => $this->t('This increases icon sizes relative to their container'),
      '#options' => [
        '' => $this->t('Default'),
        'fa-xs' => $this->t('Extra Small'),
        'fa-sm' => $this->t('Small'),
        'fa-lg' => $this->t('Large'),
        'fa-2x' => $this->t('2x'),
        'fa-3x' => $this->t('3x'),
        'fa-4x' => $this->t('4x'),
        'fa-5x' => $this->t('5x'),
        'fa-6x' => $this->t('6x'),
        'fa-7x' => $this->t('7x'),
        'fa-8x' => $this->t('8x'),
        'fa-9x' => $this->t('9x'),
        'fa-10x' => $this->t('10x'),
      ],
      '#default_value' => isset($iconSettings['size']) ? $iconSettings['size'] : '',
    ];
    // Set icon to fixed width.
    $element['settings']['fixed-width'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fixed Width?'),
      '#description' => $this->t('Use to set icons at a fixed width. Great to use when different icon widths throw off vertical alignment. Especially useful in things like nav lists and list groups.'),
      '#default_value' => isset($iconSettings['fixed-width']) ? $iconSettings['fixed-width'] : FALSE,
      '#return_value' => 'fa-fw',
    ];
    // Add border.
    $element['settings']['border'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Border?'),
      '#description' => $this->t('Adds a border to the icon.'),
      '#default_value' => isset($iconSettings['border']) ? $iconSettings['border'] : FALSE,
      '#return_value' => 'fa-border',
    ];
    // Invert color.
    $element['settings']['invert'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Invert color?'),
      '#description' => $this->t('Inverts the color of the icon (black becomes white, etc.)'),
      '#default_value' => isset($iconSettings['invert']) ? $iconSettings['invert'] : FALSE,
      '#return_value' => 'fa-inverse',
    ];
    // Animated the icon.
    $element['settings']['animation'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation'),
      '#description' => $this->t('Use spin to get any icon to rotate, and pulse to have it rotate with 8 steps. Works especially well with fa-spinner & everything in the @iconLink.', [
        '@iconLink' => Link::fromTextAndUrl($this->t('spinner icons category'), Url::fromUri('https://fontawesome.com/icons?c=spinner-icons'))->toString(),
      ]),
      '#options' => [
        '' => $this->t('None'),
        'fa-spin' => $this->t('Spin'),
        'fa-pulse' => $this->t('Pulse'),
      ],
      '#default_value' => isset($iconSettings['animation']) ? $iconSettings['animation'] : '',
    ];

    // Pull the icons.
    $element['settings']['pull'] = [
      '#type' => 'select',
      '#title' => $this->t('Pull'),
      '#description' => $this->t('This setting will pull the icon (float) to one side or the other in relation to its nearby content'),
      '#options' => [
        '' => $this->t('None'),
        'fa-pull-left' => $this->t('Left'),
        'fa-pull-right' => $this->t('Right'),
      ],
      '#default_value' => isset($iconSettings['pull']) ? $iconSettings['pull'] : '',
    ];

    // Build new power-transforms.
    $element['settings']['power_transforms'] = [
      '#type' => 'details',
      '#open' => FALSE,
      '#title' => $this->t('Power Transforms'),
      '#description' => $this->t('See @iconLink for additional information on Power Transforms. Note that these transforms only work with the SVG with JS version of Font Awesome. See the @adminLink to set your version of Font Awesome.', [
        '@iconLink' => Link::fromTextAndUrl($this->t('the Font Awesome `How to use` guide'), Url::fromUri('https://fontawesome.com/how-to-use/svg-with-js'))->toString(),
        '@adminLink' => Link::FromTextAndUrl($this->t('admin page'), Url::fromRoute('fontawesome.admin_settings'))->toString(),
      ]),
    ];
    // Rotate the icon.
    $element['settings']['power_transforms']['rotate']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Rotate'),
      '#field_suffix' => '&deg;',
      '#default_value' => isset($iconSettings['power_transforms']['rotate']['value']) ? $iconSettings['power_transforms']['rotate']['value'] : '',
      '#description' => $this->t('Power Transform rotating effects icon angle without changing or moving the container. To rotate icons use any arbitrary value. Units are degrees with negative numbers allowed.'),
    ];
    // Flip the icon.
    $element['settings']['power_transforms']['flip-h']['value'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Flip Horizontal?'),
      '#default_value' => isset($iconSettings['power_transforms']['flip-h']['value']) ? $iconSettings['power_transforms']['flip-h']['value'] : FALSE,
      '#description' => $this->t('Power Transform flipping effects icon reflection without changing or moving the container.'),
      '#return_value' => 'h',
    ];
    $element['settings']['power_transforms']['flip-v']['value'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Flip Vertical?'),
      '#default_value' => isset($iconSettings['power_transforms']['flip-v']['value']) ? $iconSettings['power_transforms']['flip-v']['value'] : FALSE,
      '#description' => $this->t('Power Transform flipping effects icon reflection without changing or moving the container.'),
      '#return_value' => 'v',
    ];
    // Scale the icon.
    $element['settings']['power_transforms']['scale'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Scale'),
      '#description' => $this->t('Power Transform scaling effects icon size without changing or moving the container. This field will scale icons up or down with any arbitrary value, including decimals. Units are 1/16em.'),
    ];
    $element['settings']['power_transforms']['scale']['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Scale Type'),
      '#options' => [
        '' => $this->t('None'),
        'shrink' => $this->t('Shrink'),
        'grow' => $this->t('Grow'),
      ],
      '#default_value' => isset($iconSettings['power_transforms']['scale']['type']) ? $iconSettings['power_transforms']['scale']['type'] : '',
      '#element_validate' => [
        [static::class, 'validatePowerTransforms'],
      ],
    ];
    $element['settings']['power_transforms']['scale']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Scale Value'),
      '#min' => 0,
      '#default_value' => isset($iconSettings['power_transforms']['scale']['value']) ? $iconSettings['power_transforms']['scale']['value'] : '',
      '#states' => [
        'disabled' => [
          ':input[name="' . $field_name . '[' . $delta . '][settings][power_transforms][scale][type]"]' => ['value' => ''],
        ],
      ],
    ];
    // Position the icon.
    $element['settings']['power_transforms']['position_y'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Position (Y Axis)'),
      '#description' => $this->t('Power Transform positioning effects icon location without changing or moving the container. This field will move icons up or down with any arbitrary value, including decimals. Units are 1/16em.'),
    ];
    $element['settings']['power_transforms']['position_y']['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Position Type'),
      '#options' => [
        '' => $this->t('None'),
        'up' => $this->t('Up'),
        'down' => $this->t('Down'),
      ],
      '#default_value' => isset($iconSettings['power_transforms']['position_y']['type']) ? $iconSettings['power_transforms']['position_y']['type'] : '',
      '#element_validate' => [
        [static::class, 'validatePowerTransforms'],
      ],
    ];
    $element['settings']['power_transforms']['position_y']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Position Value'),
      '#min' => 0,
      '#default_value' => isset($iconSettings['power_transforms']['position_y']['value']) ? $iconSettings['power_transforms']['position_y']['value'] : '',
      '#states' => [
        'disabled' => [
          ':input[name="' . $field_name . '[' . $delta . '][settings][power_transforms][position_y][type]"]' => ['value' => ''],
        ],
      ],
    ];
    $element['settings']['power_transforms']['position_x'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Position (X Axis)'),
      '#description' => $this->t('Power Transform positioning effects icon location without changing or moving the container. This field will move icons up or down with any arbitrary value, including decimals. Units are 1/16em.'),
    ];
    $element['settings']['power_transforms']['position_x']['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Position Type'),
      '#options' => [
        '' => $this->t('None'),
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
      ],
      '#default_value' => isset($iconSettings['power_transforms']['position_x']['type']) ? $iconSettings['power_transforms']['position_x']['type'] : '',
      '#element_validate' => [
        [static::class, 'validatePowerTransforms'],
      ],
    ];
    $element['settings']['power_transforms']['position_x']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Position Value'),
      '#min' => 0,
      '#default_value' => isset($iconSettings['power_transforms']['position_x']['value']) ? $iconSettings['power_transforms']['position_x']['value'] : '',
      '#states' => [
        'disabled' => [
          ':input[name="' . $field_name . '[' . $delta . '][settings][power_transforms][position_x][type]"]' => ['value' => ''],
        ],
      ],
    ];

    return $element;
  }

  /**
   * Validate the Font Awesome power transforms.
   */
  public static function validatePowerTransforms($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (strlen($value) == 0) {
      $form_state->setValueForElement($element, '');
      return;
    }

    // Check the value of the power transform.
    $transformSettings = $form_state->getValues();
    foreach (array_slice($element['#parents'], 0, 5) as $key) {
      $transformSettings = $transformSettings[$key];
    }

    if (!is_numeric($transformSettings['value'])) {
      $form_state->setError($element, t("Invalid value for Font Awesome Power Transform %value. Please see @iconLink for information on correct values.", [
        '%value' => $value,
        '@iconLink' => Link::fromTextAndUrl(t('the Font Awesome icon list'), Url::fromUri('https://fontawesome.com/how-to-use/svg-with-js'))->toString(),
      ]));
    }
  }

  /**
   * Validate the Font Awesome icon name.
   */
  public static function validateIconName($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (strlen($value) == 0) {
      $form_state->setValueForElement($element, '');
      return;
    }

    // Load the icon data so we can check for a valid icon.
    $iconData = fontawesome_extract_icon_metadata($value);

    if (!isset($iconData['name'])) {
      $form_state->setError($element, t("Invalid icon name %value. Please see @iconLink for correct icon names.", [
        '%value' => $value,
        '@iconLink' => Link::fromTextAndUrl(t('the Font Awesome icon list'), Url::fromUri('https://fontawesome.com/icons'))->toString(),
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    // Load the icon data so we can determine the icon type.
    $metadata = fontawesome_extract_icons();

    // Loop over each item and set the data properly.
    foreach ($values as &$item) {
      // Remove the prefix if the user accidentally added it.
      if (substr($item['icon_name'], 0, 3) == 'fa-') {
        $item['icon_name'] = substr($item['icon_name'], 3);
      }

      // Massage rotate and flip values to make them format properly.
      if (is_numeric($item['settings']['power_transforms']['rotate']['value'])) {
        $item['settings']['power_transforms']['rotate']['type'] = 'rotate';
      }
      else {
        unset($item['settings']['power_transforms']['rotate']);
      }
      if (!empty($item['settings']['power_transforms']['flip-h']['value'])) {
        $item['settings']['power_transforms']['flip-h']['type'] = 'flip';
      }
      else {
        unset($item['settings']['power_transforms']['flip-h']);
      }
      if (!empty($item['settings']['power_transforms']['flip-v']['value'])) {
        $item['settings']['power_transforms']['flip-v']['type'] = 'flip';
      }
      else {
        unset($item['settings']['power_transforms']['flip-v']);
      }
      // Determine the icon style - brands don't allow style.
      $item['style'] = isset($metadata[$item['icon_name']]['styles']) ? fontawesome_determine_prefix($metadata[$item['icon_name']]['styles'], $item['settings']['style']) : 'fas';
      unset($item['settings']['style']);

      $item['settings'] = serialize(array_filter($item['settings']));
    }

    return $values;
  }

}
