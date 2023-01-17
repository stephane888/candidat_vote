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
    $style = $configs['style_image_lot'] ? $configs['style_image_lot'] : 'large';
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
    $configs = $this->config('candidat_vote.settings')->getRawData();
    $style = $configs['style_image_candidat'] ? $configs['style_image_candidat'] : 'moyen';
    $entities = $this->entityTypeManager()->getStorage('candidat_entity')->loadMultiple();
    
    /**
     *
     * @var \Drupal\candidat_vote\Entity\LotsEntity $entity
     */
    foreach ($entities as $entity) {
      $urls = [];
      
      // $this->getUrlImages()
      
      $this->getUrlImages($entity->get('image')->getValue(), $urls, $style);
      $candidats[] = [
        'label' => $entity->getName(),
        'logo' => $urls[0]
      ];
    }
    return $candidats;
  }
  
  /**
   *
   * @return string
   */
  public function getTitleCandidats() {
    $configs = $this->config('candidat_vote.settings')->getRawData();
    $title = isset($configs['candidat_vote_title']) ? $configs['candidat_vote_title'] : 'Quel est la meilleur entreprise nigériane au cameroun ?';
    return $title;
  }
  
  /**
   *
   * @return string
   */
  public function getTitleLots() {
    $configs = $this->config('candidat_vote.settings')->getRawData();
    $title = isset($configs['lots_title']) ? $configs['lots_title'] : "Votez pour la meilleur entreprise nigériane au cameroun et tentez de gagnez de nombreux lots";
    return $title;
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