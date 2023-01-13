<?php

namespace Drupal\candidat_vote\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Lots entity entities.
 */
class LotsEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
