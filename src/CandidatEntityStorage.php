<?php

namespace Drupal\candidat_vote;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\candidat_vote\Entity\CandidatEntityInterface;

/**
 * Defines the storage handler class for Candidat entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Candidat entity entities.
 *
 * @ingroup candidat_vote
 */
class CandidatEntityStorage extends SqlContentEntityStorage implements CandidatEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CandidatEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {candidat_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {candidat_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CandidatEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {candidat_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('candidat_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
