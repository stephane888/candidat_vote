<?php

namespace Drupal\candidat_vote;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Lots entity entity.
 *
 * @see \Drupal\candidat_vote\Entity\LotsEntity.
 */
class LotsEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\candidat_vote\Entity\LotsEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished lots entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published lots entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit lots entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete lots entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add lots entity entities');
  }


}
