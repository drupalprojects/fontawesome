<?php

namespace Drupal\fontawesome\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Implementation of Font Awesome icon formatter.
 *
 * @FieldFormatter(
 *   id = "fontawesome_icon_formatter",
 *   label = @Translation("Font Awesome Icon"),
 *   field_types = {
 *     "fontawesome_icon"
 *   }
 * )
 */
class FontAwesomeIconFormatter extends FormatterBase implements ContainerFactoryPluginInterface {
  /**
   * Drupal LoggerFactory service container.
   *
   * @var Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ConfigFactory $config_factory) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->configFactory = $config_factory;
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
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [
      $this->t('Displays a Font Awesome icon.'),
    ];
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Early opt-out if the field is empty.
    if (count($items) <= 0) {
      return [];
    }

    // Load the configuration settings.
    $configurationSettings = $this->configFactory->get('fontawesome.settings');

    // Attach the libraries as needed.
    $fontawesomeLibraries = [];
    if ($configurationSettings->get('method') == 'webfonts') {
      // Webfonts method.
      $fontawesomeLibraries[] = 'fontawesome/fontawesome.webfonts';
    }
    else {
      // SVG method.
      $fontawesomeLibraries[] = 'fontawesome/fontawesome.svg';

      // Attach the shim file if needed.
      if ($configurationSettings->get('use_shim')) {
        $fontawesomeLibraries[] = 'fontawesome/fontawesome.svg.shim';
      }
    }

    // Loop over each icon and build data.
    $icons = [];
    foreach ($items as $key => $item) {
      // Get the icon settings.
      $iconSettings = unserialize($item->get('settings')->getValue());

      // Format power transforms.
      $iconTransforms = [];
      $powerTransforms = $iconSettings['power_transforms'];
      foreach ($powerTransforms as $transform) {
        $iconTransforms[] = $transform['type'] . '-' . $transform['value'];
      }
      unset($iconSettings['power_transforms']);

      $icons[] = [
        '#theme' => 'fontawesomeicon',
        '#name' => 'fa-' . $item->get('icon_name')->getValue(),
        '#style' => $item->get('style')->getValue(),
        '#settings' => implode(' ', $iconSettings),
        '#transforms' => implode(' ', $iconTransforms),
      ];
    }

    return [
      [
        '#theme' => 'fontawesomeicons',
        '#icons' => $icons,
      ],
      '#attached' => [
        'library' => $fontawesomeLibraries,
      ],
    ];
  }

}
