<?php

namespace Drupal\album_multimedia\Plugin\views\style;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\file\FileInterface;
use Drupal\file\Plugin\Field\FieldType\FileFieldItemList;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\media\Entity\Media;

/**
 * Style plugin to render the album multimedia.
 *
 * @ViewsStyle(
 *   id = "views_album_multimedia",
 *   title = @Translation("Album Multimedia"),
 *   help = @Translation("Displays Album Multimedia."),
 *   theme = "album_multimedia_views_style",
 *   theme_file = "album_multimedia_views_style.theme.inc",
 *   display_types = {"normal"}
 * )
 */
class AlbumMultimedia extends StylePluginBase {

  /**
   * Specifies if the plugin uses fields.
   *
   * @var bool
   */
  protected $usesFields = TRUE;

  /**
   * Specifies if the plugin uses row plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = FALSE;

    /**
   * Contains all available fields on view.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldSources;

   /**
   * A Drupal entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

   /**
   * Generated url for files.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

    /**
   * Constructs a view style plugin.
   *
   * @param array $configuration
   *   The configuration array.
   * @param string $plugin_id
   *   The plugin id for the view style.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The Entity Type Manager.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file url generator.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityFieldManagerInterface $entity_field_manager, FileUrlGeneratorInterface $file_url_generator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityFieldManager = $entity_field_manager;
    $this->fileUrlGenerator = $file_url_generator;
  }

   /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Create a new instance of the plugin. This also allows us to extract
    // services from the container and inject them into our plugin via its own
    // constructor as needed.
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_field.manager'),
      $container->get('file_url_generator')
    );
  }


  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['grid_padding'] = ['default' => 1];
    $options['max_height'] = ['default' => 0];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $this->fieldSources = $this->confGetFieldSources();

    $form["album_multimedia_core"] = [
      '#type'=> "details",
      '#title' => 'Album Multimedia settings',
      '#open' => true,
      '#weight' => -10,
    ];

    $form["album_multimedia_core"]["image_field"] = [
      '#type' => 'select',
      '#title' => 'Image field',
      '#empty_value' => true,
      '#default_value' => $this->options["album_multimedia_core"]['image_field'] ?? null,
      '#description' => "Select the field you want to use as image.",
      '#required' => false,
      '#options' => $this->getImageFields(),
    ];
    
    $form["album_multimedia_core"]["title_field"] = [
      '#type' => 'select',
      '#title' => 'Title field',
      '#empty_value' => true,
      '#default_value' => $this->options["album_multimedia_core"]['title_field'] ?? null,
      '#description' => "Select the field you want to use to display the title.",
      '#required' => false,
      '#options' => $this->getNonImageFields(),
    ];

    $form["album_multimedia_core"]["title_node"]= [
      '#type' => 'checkbox',
      '#title' => 'Use node Title if Title filed is empty',
      '#default_vale' => true,
      '#description' => "Use node Title if Title filed is empty.",
      '#required' => false,
    ];

    $form["album_multimedia_core"]["authors_field"] = [
      '#type' => 'select',
      '#title' => 'Authors field',
      '#empty_value' => true,
      '#default_value' => $this->options["album_multimedia_core"]['authors_field'] ?? null,
      '#description' => "Select the field you want to use to display the authors.",
      '#required' => false,
      '#options' => $this->getNonImageFields(),
    ];
    $form["album_multimedia_core"]["date_field"] = [
      '#type' => 'select',
      '#title' => 'Date field',
      '#empty_value' => true,
      '#default_value' => $this->options["album_multimedia_core"]['date_field'] ?? null,
      '#description' => "Select the field you want to use to display the date.",
      '#required' => false,
      '#options' => $this->getNonImageFields(),
    ];

    $form["album_multimedia_core"]["authors_field"] = [
      '#type' => 'select',
      '#title' => 'Authors field',
      '#empty_value' => true,
      '#default_value' => $this->options["album_multimedia_core"]['authors_field'] ?? null,
      '#description' => "Select the field you want to use to display the authors.",
      '#required' => false,
      '#options' => $this->getTaxoFields(),
    ];

    $form["album_multimedia_core"]["group_field"] = [
      '#type' => 'select',
      '#title' => 'Group field',
      '#empty_value' => true,
      '#default_value' => $this->options["album_multimedia_core"]['group_field'] ?? null,
      '#description' => "Select the field you want to use to display the album group.",
      '#required' => false,
      '#options' => $this->getTaxoFields(),
    ];

    $form["album_multimedia_jquery_sortable_photo"] = [
      '#type'=> "details",
      '#title' => 'Jquery Sortable Photo settings',
      '#open' => true,
      '#weight' => -9,
    ];
    $form["album_multimedia_jquery_sortable_photo"]['grid_padding'] = [
      '#type' => 'number',
      '#title' => $this->t('Padding'),
      '#size' => 2,
      '#description' => $this->t('The amount of padding in pixels in between grid items.'),
      '#default_value' => $this->options['grid_padding'],
      '#maxlength' => 2,
    ];

    $form["album_multimedia_jquery_sortable_photo"]['max_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum image height'),
      '#size' => 4,
      '#description' => $this->t('The maximum image height in pixels.'),
      '#default_value' => $this->options['max_height'],
      '#maxlength' => 4,
    ];
  }

  /**
   * Returns the name of the image field used in the view.
   */
  public function getImageFieldName() {
    $fields = $this->displayHandler->handlers['field'];

    // Find the first non-excluded image field.
    foreach ($fields as $key => $field) {
      /* @var \Drupal\views\Plugin\views\field\EntityField $field */

      // Get the storage definition in order to determine the field type.
      $field_storage_definitions = $this->entityFieldManager->getFieldStorageDefinitions($field->definition['entity_type']);

      /* @var \Drupal\field\Entity\FieldStorageConfig $field_storage */
      $field_storage = $field_storage_definitions[$field->field];
      $field_type = $field_storage->getType();

      if (empty($field->options['exclude']) && (($field_type == 'image') || ($field_type == 'entity_reference'))) {
        return $field->field; 
      }
    }

    return FALSE;
  }

  /**
   * Validates the view configuration.
   * Fails if there is a non-image field, or there are more
   * than one image fields that are not excluded from display.
   */
  function validate() {
    $errors = parent::validate();

    if ($this->view->storage->isNew()) {
      // Skip validation when the view is being created.
      // (the default field is a title field, which would fail.)
      return $errors;
    }

    if (! isset($this->options["album_multimedia_core"]["image_field"])) {
      $errors[] = $this->t('The image field must be defined');
    }

    return $errors;
  }

    /**
   * Render fields.
   *
   * @Override parent.
   */
  public function renderAllFields(array $result) {
    $rendered_fields = [];
    $this->view->row_index = 0;
    $keys = array_keys($this->view->field);

    // If all fields have a field::access FALSE there might be no fields, so
    // there is no reason to execute this code.
    if (!empty($keys)) {
      $fields = $this->view->field;
      $field_sources = $this->confGetFieldSources();
      $image_fields = array_keys($field_sources['field_options_images']);
      foreach ($result as $count => $row) {
        $this->view->row_index = $count;
        foreach ($keys as $id) {
          if (in_array($id, $image_fields)) {
            // This is an image/thumb field.
            // Create URI for selected image style.
            $field_settings = $this->view->field[$id]->options['settings'];
            $image_style = NULL;
            if (array_key_exists('lightgallery_core', $field_settings)) {
              $image_style = $field_settings['lightgallery_core']['lightgallery_image_style'];
            }
            if (array_key_exists('image_style', $field_settings)) {
              $image_style = $field_settings['image_style'];
            }

            $field_name = $fields[$id]->field;

            $field = $result[$count]->_entity->{$field_name};
            if ($field instanceof FileFieldItemList) {
              foreach ($field as $entity_field) {
                $file = $entity_field->entity;
                if ($file instanceof FileInterface && $uri = $file->getFileUri()) {
                  if (!empty($image_style)) {
                    $rendered_fields[$count][$id][] = ImageStyle::load($image_style)
                      ->buildUrl($uri);
                  }
                  else {
                    $rendered_fields[$count][$id][] = $this->fileUrlGenerator->generateAbsoluteString($uri);
                  }
                }
              }
            }
            elseif ($field instanceof EntityReferenceFieldItemList) {
              foreach ($field as $entity_field) {
                $file_id = $entity_field->get('entity')->getTargetIdentifier();
                $bg_file = Media::load($file_id);
                // $path = $bg_file->createFileUrl();
                // $rendered_fields[$count][$id][] = $path;
              }
            }
          }
          else {
            // Just render the field as views would do.
            $rendered_fields[$count][$id] = $this->view->field[$id]->render($row);
          }
        }

        // Populate row tokens.
        $this->rowTokens[$count] = $this->view->field[$id]->getRenderTokens([]);
      }
    }
    unset($this->view->row_index);

    return $rendered_fields;
  }

  /**
   * Returns available image fields on view.
   */
  private function getImageFields() {
    return !empty($this->fieldSources['field_options_images']) ? $this->fieldSources['field_options_images'] : [];
  }

   /**
   * Returns available options fields on view.
   */
  private function getNonImageFields() {
    return !empty($this->fieldSources['field_options']) ? $this->fieldSources['field_options'] : [];
  }

  
   /**
   * Returns available taxo fields on view.
   */
  private function getTaxoFields() {
    return !empty($this->fieldSources['field_taxo']) ? $this->fieldSources['field_taxo'] : [];
  }

    /**
   * Utility to determine which view fields can be used for image data.
   */
  protected function confGetFieldSources() {
    $options = [
      'field_options_images' => [],
      'field_options' => [],
      'field_taxo' => [],
    ];
    $view = $this->view;
    $field_handlers = $view->display_handler->getHandlers('field');
    $field_labels = $view->display_handler->getFieldLabels();

    /** @var \Drupal\views\Plugin\views\field\FieldHandlerInterface $handler */
    // Separate image fields from non-image fields. For image fields we can
    // work with fids and fields of type image or file.
    foreach ($field_handlers as $field => $handler) {
      $is_image = FALSE;
      $target_type = '';
      $id = $handler->getPluginId();
      $name = $field_labels[$field];
      if ($id == 'field') {
        // The field definition is on the handler, it's right bloody there, but
        // it's protected so we can't access it. This means we have to take the
        // long road (via our own injected entity manager) to get the field type
        // info.
        $entity_type = $handler->getEntityType();

        // Fetch the real field name, because views alters the field name if the
        // same fields gets added multiple times.
        $field_name = $handler->field;
        $field_definition = $this->entityFieldManager->getFieldStorageDefinitions($entity_type)[$field_name];
        if ($field_definition) {
          $field_type = $field_definition->getType();
          if (method_exists($field_definition,'getSettings')) {
            $settings = $field_definition->getSettings();
            $target_type = $settings['target_type'];
          }
          else {
            $target_type = '';
          }

          if ($field_type == 'image' || $field_type == 'file' || 
            ($field_type == 'entity_reference') && ($target_type == 'media')) {
            $field_cardinality = $field_definition->get('cardinality');
            $options['field_options_images'][$field] = $name . ($field_cardinality == 1 ? '' : '*');
            $is_image = TRUE;
          }
        }
      }
      if (!$is_image) {
        if ($target_type == 'taxonomy_term') {
          $options['field_taxo'][$field] = $name;
        }
        else {
          $options['field_options'][$field] = $name;
        }
      }
    }

    return $options;
  }
}
