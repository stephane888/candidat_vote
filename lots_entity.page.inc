<?php

/**
 * @file
 * Contains lots_entity.page.inc.
 *
 * Page callback for Lots entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Lots entity templates.
 *
 * Default template: lots_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_lots_entity(array &$variables) {
  // Fetch LotsEntity Entity Object.
  $lots_entity = $variables['elements']['#lots_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
