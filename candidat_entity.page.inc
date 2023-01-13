<?php

/**
 * @file
 * Contains candidat_entity.page.inc.
 *
 * Page callback for Candidat entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Candidat entity templates.
 *
 * Default template: candidat_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_candidat_entity(array &$variables) {
  // Fetch CandidatEntity Entity Object.
  $candidat_entity = $variables['elements']['#candidat_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
