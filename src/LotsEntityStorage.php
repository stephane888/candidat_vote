<?php

namespace Drupal\candidat_vote;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\candidat_vote\Entity\LotsEntityInterface;

/**
 * Defines the storage handler class for Lots entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Lots entity entities.
 *
 * @ingroup candidat_vote
 */
class LotsEntityStorage extends SqlContentEntityStorage implements LotsEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(LotsEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {lots_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {lots_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(LotsEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {lots_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('lots_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
