<?php

namespace Drupal\candidat_vote\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "candidat_vote_app",
 *   admin_label = @Translation("App vote"),
 *   category = @Translation("candidat vote")
 * )
 */
class CandidatVoteBlock extends BlockBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app',
        'class' => [
          'm-5',
          'p-5'
        ]
      ]
    ];
    $build['content']['#attached']['library'][] = 'candidat_vote/candidat_vote_app';
    return $build;
  }
  
}
