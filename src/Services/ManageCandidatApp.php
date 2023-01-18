<?php

namespace Drupal\candidat_vote\Services;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxy;
use Drupal\votingapi\Entity\Vote;
use Drupal\votingapi\VoteResultFunctionManager;

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
   * @var integer
   */
  protected $totalVote = 0;
  
  /**
   *
   * @var array
   */
  protected $config = [];
  
  /**
   *
   * @var \Drupal\votingapi\VoteResultFunctionManager
   */
  protected $VoteResultFunctionManager;
  /**
   * Ce entité doit etre creer dans config/install voir le module test de
   * votingAPI.
   *
   * @var string
   */
  private $typeDeVote = 'votings_renders_note';
  
  /**
   *
   * @param AccountProxy $current_user
   * @param VoteResultFunctionManager $VoteResultFunctionManager
   */
  function __construct(AccountProxy $current_user, VoteResultFunctionManager $VoteResultFunctionManager) {
    $this->current_user = $current_user;
    $this->VoteResultFunctionManager = $VoteResultFunctionManager;
  }
  
  /**
   * Contient l'id de l'entite et la note.
   *
   * @param array $vote
   * @param boolean $multipleVote
   *        Permet de voter une seule pour une campagne s'il est à false et
   *        plusieurs s'il est a true.
   * @throws \Exception
   * @return Array|number
   */
  public function setVotes(array $vote, $multipleVote = false) {
    $this->validVote($vote);
    $user_id = $this->current_user->id();
    if ($user_id) {
      // Recuperation du candidat.
      $candidat = $this->entityTypeManager()->getStorage('candidat_entity')->load($vote['entity_id']);
      $query = $this->entityTypeManager()->getStorage('vote')->getQuery();
      $query->condition('type', $this->typeDeVote);
      $query->condition('user_id', $user_id);
      if ($multipleVote)
        $query->condition('entity_id', $candidat->id());
      $query->condition('entity_type', $candidat->getEntityTypeId());
      $ids = $query->execute();
      if (!empty(($ids))) {
        $votes = \Drupal::entityTypeManager()->getStorage('vote')->loadMultiple($ids);
        $vote = reset($votes);
        return $vote->toArray();
      }
      $vote = Vote::create([
        'type' => $this->typeDeVote,
        'entity_id' => $candidat->id(),
        'entity_type' => $candidat->getEntityTypeId(),
        'value_type' => 'option',
        'value' => $vote['note']
      ]);
      return $vote->save();
    }
    throw new \Exception("You must be logged in to be able to");
  }
  
  public function userHasVoted() {
    $query = $this->entityTypeManager()->getStorage('vote')->getQuery();
    $query->condition('type', $this->typeDeVote);
    $query->condition('user_id', $this->current_user->id());
    $ids = $query->execute();
    if (!empty($ids)) {
      /**
       *
       * @var \Drupal\votingapi\Entity\Vote $vote
       */
      $vote = \Drupal::entityTypeManager()->getStorage('vote')->load(reset($ids));
      $candidat = $this->entityTypeManager()->getStorage('candidat_entity')->load($vote->getVotedEntityId());
      return [
        'title' => $candidat->label()
      ];
    }
    return false;
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
          'entity_id' => $entity->id(),
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
      
      // $this->getUrlImages();
      $this->getUrlImages($entity->get('image')->getValue(), $urls, $style);
      $voteResults = $this->VoteResultFunctionManager->getResults($entity->getEntityTypeId(), $entity->id());
      if (!empty($voteResults[$this->typeDeVote]))
        $voteResults = $voteResults[$this->typeDeVote];
      //
      $vote_average = 0;
      if (isset($voteResults['vote_average']))
        $vote_average = $voteResults['vote_average'];
      //
      $vote_count = 0;
      if (isset($voteResults['vote_count']))
        $vote_count = $voteResults['vote_count'];
      //
      $vote_sum = 0;
      if (isset($voteResults['vote_sum']))
        $vote_sum = $voteResults['vote_sum'];
      // le client souhaite faire une tricherie pour le marketing.
      switch ($entity->id()) {
        case 1:
          $vote_count += 300;
          break;
        case 2:
          $vote_count += 5;
          break;
        case 3:
          $vote_count += 27;
          break;
        case 4:
          $vote_count += 112;
          break;
        case 5:
          $vote_count += 8;
          break;
      }
      $candidats[] = [
        'entity_id' => $entity->id(),
        'label' => $entity->getName(),
        'logo' => $urls[0],
        'total_votes' => $vote_count,
        'vote_sum' => $vote_sum,
        'vote_average' => $vote_average
        // 'getEntityTypeId' => $entity->getEntityTypeId(),
        // 'voteResults' => $voteResults
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