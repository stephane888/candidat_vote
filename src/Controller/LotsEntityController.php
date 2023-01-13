<?php

namespace Drupal\candidat_vote\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\candidat_vote\Entity\LotsEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LotsEntityController.
 *
 *  Returns responses for Lots entity routes.
 */
class LotsEntityController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Lots entity revision.
   *
   * @param int $lots_entity_revision
   *   The Lots entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($lots_entity_revision) {
    $lots_entity = $this->entityTypeManager()->getStorage('lots_entity')
      ->loadRevision($lots_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('lots_entity');

    return $view_builder->view($lots_entity);
  }

  /**
   * Page title callback for a Lots entity revision.
   *
   * @param int $lots_entity_revision
   *   The Lots entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($lots_entity_revision) {
    $lots_entity = $this->entityTypeManager()->getStorage('lots_entity')
      ->loadRevision($lots_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $lots_entity->label(),
      '%date' => $this->dateFormatter->format($lots_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Lots entity.
   *
   * @param \Drupal\candidat_vote\Entity\LotsEntityInterface $lots_entity
   *   A Lots entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(LotsEntityInterface $lots_entity) {
    $account = $this->currentUser();
    $lots_entity_storage = $this->entityTypeManager()->getStorage('lots_entity');

    $langcode = $lots_entity->language()->getId();
    $langname = $lots_entity->language()->getName();
    $languages = $lots_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $lots_entity->label()]) : $this->t('Revisions for %title', ['%title' => $lots_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all lots entity revisions") || $account->hasPermission('administer lots entity entities')));
    $delete_permission = (($account->hasPermission("delete all lots entity revisions") || $account->hasPermission('administer lots entity entities')));

    $rows = [];

    $vids = $lots_entity_storage->revisionIds($lots_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\candidat_vote\Entity\LotsEntityInterface $revision */
      $revision = $lots_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $lots_entity->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.lots_entity.revision', [
            'lots_entity' => $lots_entity->id(),
            'lots_entity_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $lots_entity->toLink($date)->toString();
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
              Url::fromRoute('entity.lots_entity.translation_revert', [
                'lots_entity' => $lots_entity->id(),
                'lots_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.lots_entity.revision_revert', [
                'lots_entity' => $lots_entity->id(),
                'lots_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.lots_entity.revision_delete', [
                'lots_entity' => $lots_entity->id(),
                'lots_entity_revision' => $vid,
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

    $build['lots_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
