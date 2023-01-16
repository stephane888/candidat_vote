<?php

namespace Drupal\candidat_vote\Services;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 * @author stephane
 *        
 */
class ManageCandidatApp extends ControllerBase {
  
  /**
   *
   * @return mixed[][]
   */
  public function getLots() {
    $configs = $this->config('candidat_vote.settings')->getRawData();
    $style = $configs['style_image'] ? $configs['style_image'] : 'large';
    $lots = [];
    // Recuperation des lots.
    $entities = $this->entityTypeManager()->getStorage('lots_entity')->loadMultiple();
    foreach ($entities as $entity) {
      $images = [];
      
      /**
       *
       * @var \Drupal\candidat_vote\Entity\LotsEntity $entity
       */
      $this->getUrlImages($entity->get('lot_images')->getValue(), $images, $style);
      foreach ($images as $url) {
        $lots[] = [
          'text' => $entity->label(),
          'image' => $url
        ];
      }
    }
    return $lots;
  }
  
  /**
   *
   * @return mixed[][]
   */
  public function getCandidats() {
    $candidats = [];
    //
    $entities = $this->entityTypeManager()->getStorage('candidat_entity')->loadMultiple();
    foreach ($entities as $entity) {
      $candidats[] = $entity->toArray();
    }
    return $candidats;
  }
  
  /**
   *
   * @param array $fids
   * @param array $images
   * @param string $style
   */
  protected function getUrlImages(array $fids, array &$images, string $style) {
    /**
     *
     * @var \Drupal\candidat_vote\Entity\LotsEntity $entity
     */
    foreach ($fids as $image) {
      if (!empty($image['target_id'])) {
        $file = \Drupal\file\Entity\File::load($image['target_id']);
        $images[] = \Drupal\image\Entity\ImageStyle::load($style)->buildUrl($file->getFileUri());
      }
    }
  }
  
}