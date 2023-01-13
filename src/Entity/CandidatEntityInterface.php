<?php

namespace Drupal\candidat_vote\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Candidat entity entities.
 *
 * @ingroup candidat_vote
 */
interface CandidatEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Candidat entity name.
   *
   * @return string
   *   Name of the Candidat entity.
   */
  public function getName();

  /**
   * Sets the Candidat entity name.
   *
   * @param string $name
   *   The Candidat entity name.
   *
   * @return \Drupal\candidat_vote\Entity\CandidatEntityInterface
   *   The called Candidat entity entity.
   */
  public function setName($name);

  /**
   * Gets the Candidat entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Candidat entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Candidat entity creation timestamp.
   *
   * @param int $timestamp
   *   The Candidat entity creation timestamp.
   *
   * @return \Drupal\candidat_vote\Entity\CandidatEntityInterface
   *   The called Candidat entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Candidat entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Candidat entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\candidat_vote\Entity\CandidatEntityInterface
   *   The called Candidat entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Candidat entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Candidat entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\candidat_vote\Entity\CandidatEntityInterface
   *   The called Candidat entity entity.
   */
  public function setRevisionUserId($uid);

}
