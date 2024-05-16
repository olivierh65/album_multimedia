<?php

/**
 * @file
 * The module view theme.
 */

use Drupal\lightgallery\Optionset\LightgalleryOptionset;
use Drupal\lightgallery\Manager\LightgalleryManager;
use Drupal\image\Entity\ImageStyle;



/**
 * Prepares variables for Album Multimedia view templates.
 *
 * Default template: album-multimedia-views.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - view: A ViewExecutable object.
 *   - rows: The raw row data.
 */
function template_preprocess_album_multimedia_views_style(array &$variables) {
  // Only run the preprocessor if it is a view.
  if (empty($variables['view'])) {
    return;
  }


  /**
   * @var \Drupal\lightgallery\Plugin\views\style\LightGallery $handler
   * @var \Drupal\views\Plugin\views\field\Field $field
   */
  $view = $variables['view'];
  $rows = $variables['rows'];
  $fields = $view->field;
  $handler = $view->style_plugin;

  // The lightgallery handler is not properly configured yet.
  if (empty($handler->options['album_multimedia_core'])) {
    return;
  }

  $options_album_multimedia = $handler->options['album_multimedia_core'];
  $options_jquery_sortble_photo = $handler->options['album_multimedia_jquery_sortable_photo'];

  $items = [];

  foreach ($rows as $row) {
    /** @var \Drupal\views\ResultRow $row */
    if ($row->_entity->getType() != 'album_multimedia') {
      continue;
    }
    $album = [];
    $markup = $view->field[$options_album_multimedia['image_field']]->advancedRender($row)->__toString();
    $album['markup'] = $markup;

    // Set unique id, so that multiple instances on one page can be created.
    $unique_id = uniqid();
    $title = null;
    if (isset($row->_entity->getFields()[$options_album_multimedia["title_field"]])) {
      $title_markup = $view->field[$options_album_multimedia['title_field']]->advancedRender($row);
      if (method_exists($title_markup, '__toString')) {
        $title = $title_markup->__toString();
      }
      elseif ((gettype($title_markup) === 'string') && (strlen($title_markup) > 0)) {
        $title = $title_markup;
      }
    }
    if ($title == null) {
      if ((isset($options_album_multimedia['title_node'])) && ($options_album_multimedia['title_node'] == true)) {
        $title = $row->_entity->getTitle();
      }
      else {
        $title = '';
      }
    }
    
    $album['title'] = $title;
    $album['date'] = isset($row->_entity->getFields()[$options_album_multimedia["date_field"]]) ?
                        $view->field[$options_album_multimedia['date_field']]->advancedRender($row)->__toString() : '';
    $album['authors'] = isset($row->_entity->getFields()[$options_album_multimedia["authors_field"]]) ?
                        $view->field[$options_album_multimedia['authors_field']]->advancedRender($row)->__toString() : '';
    $album['group'] = isset($row->_entity->getFields()[$options_album_multimedia["group_field"]]) ?
                        $view->field[$options_album_multimedia['group_field']]->advancedRender($row)->__toString() : '';
    $album['id'] = $unique_id;
    // $album['medias'] = $row->_entity->getFields()[$options_album_multimedia['image_field']]->view();

    $items[] = $album;
  }

  $variables['theme'] = 'album_multimedia_views_style';
  $variables['items'] = $items;
  $variables['#attached']['drupalSettings']['viewsPhotoGrid']['gridPadding'] = $handler->options['grid_padding'];
  $variables['#attached']['drupalSettings']['viewsPhotoGrid']['maxHeight'] = $handler->options['max_height'];
  $variables['#attached']['library'][] =  'album_multimedia/freewall';
  // $variables['#attached']['library'][] =  'album_multimedia/justified_gallery';
  // $variables['#attached']['library'][] =  'album_multimedia/pycs-layout';
}

function getEntityValue( $entity) {
  if (! isset($entity)) {
    return '';
  }
  switch ($entity->getFieldDefinition()->getType()) {
    case 'text':
      return $entity->view()[0]['#text'];
      break;
    case 'datetime':
      return $entity->getString();
      break;
    case 'entity_reference':
      return $entity->getString();
    break;
  }

}