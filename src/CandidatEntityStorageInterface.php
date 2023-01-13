<?php

namespace Drupal\candidat_vote;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface CandidatEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Candidat entity revision IDs for a specific Candidat entity.
   *
   * @param \Drupal\candidat_vote\Entity\CandidatEntityInterface $entity
   *   The Candidat entity entity.
   *
   * @return int[]
   *   Candidat entity revision IDs (in ascending order).
   */
  public function revisionIds(CandidatEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Candidat entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Candidat entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\candidat_vote\Entity\CandidatEntityInterface $entity
   *   The Candidat entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CandidatEntityInterface $entity);

  /**
   * Unsets the language for all Candidat entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
