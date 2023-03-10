<?php

namespace Drupal\candidat_vote\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Lots entity entities.
 *
 * @ingroup candidat_vote
 */
interface LotsEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Lots entity name.
   *
   * @return string
   *   Name of the Lots entity.
   */
  public function getName();

  /**
   * Sets the Lots entity name.
   *
   * @param string $name
   *   The Lots entity name.
   *
   * @return \Drupal\candidat_vote\Entity\LotsEntityInterface
   *   The called Lots entity entity.
   */
  public function setName($name);

  /**
   * Gets the Lots entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Lots entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Lots entity creation timestamp.
   *
   * @param int $timestamp
   *   The Lots entity creation timestamp.
   *
   * @return \Drupal\candidat_vote\Entity\LotsEntityInterface
   *   The called Lots entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Lots entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Lots entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\candidat_vote\Entity\LotsEntityInterface
   *   The called Lots entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Lots entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Lots entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\candidat_vote\Entity\LotsEntityInterface
   *   The called Lots entity entity.
   */
  public function setRevisionUserId($uid);

}
