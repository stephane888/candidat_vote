<?php

namespace Drupal\candidat_vote\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\candidat_vote\Entity\CandidatEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CandidatEntityController.
 *
 *  Returns responses for Candidat entity routes.
 */
class CandidatEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Candidat entity revision.
   *
   * @param int $candidat_entity_revision
   *   The Candidat entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($candidat_entity_revision) {
    $candidat_entity = $this->entityTypeManager()->getStorage('candidat_entity')
      ->loadRevision($candidat_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('candidat_entity');

    return $view_builder->view($candidat_entity);
  }

  /**
   * Page title callback for a Candidat entity revision.
   *
   * @param int $candidat_entity_revision
   *   The Candidat entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($candidat_entity_revision) {
    $candidat_entity = $this->entityTypeManager()->getStorage('candidat_entity')
      ->loadRevision($candidat_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $candidat_entity->label(),
      '%date' => $this->dateFormatter->format($candidat_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Candidat entity.
   *
   * @param \Drupal\candidat_vote\Entity\CandidatEntityInterface $candidat_entity
   *   A Candidat entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CandidatEntityInterface $candidat_entity) {
    $account = $this->currentUser();
    $candidat_entity_storage = $this->entityTypeManager()->getStorage('candidat_entity');

    $langcode = $candidat_entity->language()->getId();
    $langname = $candidat_entity->language()->getName();
    $languages = $candidat_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $candidat_entity->label()]) : $this->t('Revisions for %title', ['%title' => $candidat_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all candidat entity revisions") || $account->hasPermission('administer candidat entity entities')));
    $delete_permission = (($account->hasPermission("delete all candidat entity revisions") || $account->hasPermission('administer candidat entity entities')));

    $rows = [];

    $vids = $candidat_entity_storage->revisionIds($candidat_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\candidat_vote\Entity\CandidatEntityInterface $revision */
      $revision = $candidat_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $candidat_entity->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.candidat_entity.revision', [
            'candidat_entity' => $candidat_entity->id(),
            'candidat_entity_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $candidat_entity->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.candidat_entity.translation_revert', [
                'candidat_entity' => $candidat_entity->id(),
                'candidat_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.candidat_entity.revision_revert', [
                'candidat_entity' => $candidat_entity->id(),
                'candidat_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.candidat_entity.revision_delete', [
                'candidat_entity' => $candidat_entity->id(),
                'candidat_entity_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['candidat_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
