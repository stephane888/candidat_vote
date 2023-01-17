<?php

namespace Drupal\candidat_vote\Controller;

use Drupal\Core\Controller\ControllerBase;
use Stephane888\DrupalUtility\HttpResponse;
use Stephane888\Debug\ExceptionExtractMessage;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Serialization\Json;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\candidat_vote\Services\ManageCandidatApp;

/**
 * Returns responses for candidat vote routes.
 */
class CandidatVoteController extends ControllerBase {
  /**
   *
   * @var \Drupal\candidat_vote\Services\ManageCandidatApp
   */
  protected $ManageCandidatApp;
  
  function __construct(ManageCandidatApp $ManageCandidatApp) {
    $this->ManageCandidatApp = $ManageCandidatApp;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('candidat_vote.manage_api'));
  }
  
  /**
   * Builds the response.
   */
  public function build() {
    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app',
        'class' => [
          'm-5',
          'p-5'
        ]
      ]
    ];
    $build['content']['#attached']['library'][] = 'candidat_vote/candidat_vote_app';
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
      $datas = [
        'lots_title' => $this->ManageCandidatApp->getTitleLots(),
        'candidats_title' => $this->ManageCandidatApp->getTitleCandidats(),
        'lots' => $this->ManageCandidatApp->getLots(),
        'candidats' => $this->ManageCandidatApp->getCandidats()
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
