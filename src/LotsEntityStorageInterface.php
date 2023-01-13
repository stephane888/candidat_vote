<?php

namespace Drupal\candidat_vote;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface LotsEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Lots entity revision IDs for a specific Lots entity.
   *
   * @param \Drupal\candidat_vote\Entity\LotsEntityInterface $entity
   *   The Lots entity entity.
   *
   * @return int[]
   *   Lots entity revision IDs (in ascending order).
   */
  public function revisionIds(LotsEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Lots entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Lots entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\candidat_vote\Entity\LotsEntityInterface $entity
   *   The Lots entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(LotsEntityInterface $entity);

  /**
   * Unsets the language for all Lots entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
