<?php

namespace Drupal\candidat_vote;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Candidat entity entities.
 *
 * @ingroup candidat_vote
 */
class CandidatEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Candidat entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\candidat_vote\Entity\CandidatEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.candidat_entity.edit_form',
      ['candidat_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
