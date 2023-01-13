<?php

namespace Drupal\candidat_vote\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for candidat vote routes.
 */
class CandidatVoteController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
