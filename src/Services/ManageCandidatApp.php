<?php

namespace Drupal\candidat_vote\Services;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxy;
use Drupal\votingapi\Entity\Vote;

/**
 *
 * @author stephane
 *        
 */
class ManageCandidatApp extends ControllerBase {
  /**
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $current_user;
  
  /**
   *
   * @var array
   */
  protected $config = [];
  
  function __construct(AccountProxy $current_user) {
    $this->current_user = $current_user;
  }
  
  /**
   * Contient l'id de l'entite et la note.
   *
   * @param array $vote
   */
  public function setVotes(array $vote, $multipleVote = false) {
    $this->validVote($vote);
    $user_id = $this->current_user->id();
    if ($user_id) {
      // Recuperation du candidat.
      $candidat = $this->entityTypeManager()->getStorage('candidat_entity')->load($vote['entity_id']);
      $query = $this->entityTypeManager()->getStorage('vote')->getQuery();
      $query->condition('type', 'votings_renders_note');
      $query->condition('user_id', $user_id);
      $query->condition('entity_id', $candidat->id());
      $query->condition('entity_type', $candidat->getEntityTypeId());
      $ids = $query->execute();
      if (!empty(($ids))) {
        $votes = \Drupal::entityTypeManager()->getStorage('vote')->loadMultiple($ids);
        return reset($votes);
      }
      $vote = Vote::create([
        'type' => 'votings_renders_note',
        'entity_id' => $candidat->id(),
        'entity_type' => $candidat->getEntityTypeId(),
        'value_type' => 'option',
        'value' => $vote['note']
      ]);
      return $vote->save();
    }
    throw new \Exception("You must be logged in to be able to");
  }
  
  protected function validVote(array $vote) {
    if (!isset($vote['entity_id']))
      throw new \Exception("Entity id is not set");
    if (!isset($vote['note']))
      throw new \Exception("The vote has no value");
  }
  
  /**
   *
   * @return mixed[][]
   */
  public function getLots() {
    $configs = $this->getConfig();
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
    $configs = $this->getConfig();
    $style = $configs['style_image'] ? $configs['style_image'] : 'medium';
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
    $configs = $this->getConfig();
    $title = isset($configs['candidat_vote_title']) ? $configs['candidat_vote_title'] : 'Quel est la meilleur entreprise nigériane au cameroun ?';
    return $title;
  }
  
  /**
   *
   * @return string
   */
  public function getTitleLots() {
    $configs = $this->getConfig();
    $title = isset($configs['lots_title']) ? $configs['lots_title'] : "Votez pour la meilleur entreprise nigériane au cameroun et tentez de gagnez de nombreux lots";
    return $title;
  }
  
  protected function getConfig() {
    if (empty($this->config)) {
      $this->config = $this->config('candidat_vote.settings')->getRawData();
    }
    return $this->config;
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