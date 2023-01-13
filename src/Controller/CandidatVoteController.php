<?php

namespace Drupal\candidat_vote\Controller;

use Drupal\Core\Controller\ControllerBase;
use Stephane888\DrupalUtility\HttpResponse;
use Stephane888\Debug\ExceptionExtractMessage;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Serialization\Json;

/**
 * Returns responses for candidat vote routes.
 */
class CandidatVoteController extends ControllerBase {
  
  /**
   * Builds the response.
   */
  public function build() {
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!')
    ];
    
    return $build;
  }
  
  public function getVote(Request $request) {
    try {
      if (\Drupal::currentUser()->id()) {
        $vote = Json::decode($request->getContent());
        return HttpResponse::response($datas);
      }
      throw \Exception("Vous n'etes pas connecté(e)");
    }
    catch (\Exception $e) {
      $errors = ExceptionExtractMessage::errorAllToString($e);
      $this->getLogger('candidat_vote')->critical($e->getMessage() . '<br>' . $errors);
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 400, $e->getMessage());
    }
  }
  
  public function Datas() {
    try {
      $lots = [];
      $candidats = [];
      $entities = $this->entityTypeManager()->getStorage('lots_entity')->loadMultiple();
      foreach ($entities as $entity) {
        $lots[] = $entity->toArray();
      }
      $entities = $this->entityTypeManager()->getStorage('candidat_entity')->loadMultiple();
      foreach ($entities as $entity) {
        $candidats[] = $entity->toArray();
      }
      $datas = [
        'lots' => $lots,
        'candidats' => $candidats
      ];
      return HttpResponse::response($datas);
    }
    catch (\Exception $e) {
      $errors = ExceptionExtractMessage::errorAllToString($e);
      $this->getLogger('candidat_vote')->critical($e->getMessage() . '<br>' . $errors);
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 400, $e->getMessage());
    }
  }
  
}
