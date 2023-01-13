<?php

namespace Drupal\candidat_vote\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Candidat entity entity.
 *
 * @ingroup candidat_vote
 *
 * @ContentEntityType(
 *   id = "candidat_entity",
 *   label = @Translation("Candidat entity"),
 *   handlers = {
 *     "storage" = "Drupal\candidat_vote\CandidatEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\candidat_vote\CandidatEntityListBuilder",
 *     "views_data" = "Drupal\candidat_vote\Entity\CandidatEntityViewsData",
 *     "translation" = "Drupal\candidat_vote\CandidatEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\candidat_vote\Form\CandidatEntityForm",
 *       "add" = "Drupal\candidat_vote\Form\CandidatEntityForm",
 *       "edit" = "Drupal\candidat_vote\Form\CandidatEntityForm",
 *       "delete" = "Drupal\candidat_vote\Form\CandidatEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\candidat_vote\CandidatEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\candidat_vote\CandidatEntityAccessControlHandler",
 *   },
 *   base_table = "candidat_entity",
 *   data_table = "candidat_entity_field_data",
 *   revision_table = "candidat_entity_revision",
 *   revision_data_table = "candidat_entity_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer candidat entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/candidat_entity/{candidat_entity}",
 *     "add-form" = "/admin/structure/candidat_entity/add",
 *     "edit-form" = "/admin/structure/candidat_entity/{candidat_entity}/edit",
 *     "delete-form" = "/admin/structure/candidat_entity/{candidat_entity}/delete",
 *     "version-history" = "/admin/structure/candidat_entity/{candidat_entity}/revisions",
 *     "revision" = "/admin/structure/candidat_entity/{candidat_entity}/revisions/{candidat_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/candidat_entity/{candidat_entity}/revisions/{candidat_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/candidat_entity/{candidat_entity}/revisions/{candidat_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/candidat_entity/{candidat_entity}/revisions/{candidat_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/candidat_entity",
 *   },
 *   field_ui_base_route = "candidat_entity.settings"
 * )
 */
class CandidatEntity extends EditorialContentEntityBase implements CandidatEntityInterface {
  
  use EntityChangedTrait;
  use EntityPublishedTrait;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id()
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    
    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    
    return $uri_route_parameters;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    
    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);
      
      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }
    
    // If no revision author has been set explicitly,
    // make the candidat_entity owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    
    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);
    
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')->setLabel(t('Authored by'))->setDescription(t('The user ID of author of the Candidat entity entity.'))->setRevisionable(TRUE)->setSetting('target_type', 'user')->setSetting('handler', 'default')->setTranslatable(TRUE)->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'author',
      'weight' => 0
    ])->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'autocomplete_type' => 'tags',
        'placeholder' => ''
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    $fields['name'] = BaseFieldDefinition::create('string')->setLabel(t('Name'))->setDescription(t('The name of the Candidat entity entity.'))->setRevisionable(TRUE)->setSettings([
      'max_length' => 50,
      'text_processing' => 0
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    $fields['status']->setDescription(t('A boolean indicating whether the Candidat entity is published.'))->setDisplayOptions('form', [
      'type' => 'boolean_checkbox',
      'weight' => -3
    ]);
    $fields['image'] = BaseFieldDefinition::create('image')->setLabel(t('image'))->setDescription(t('Image of the Candidat entity entity'))->setDisplayConfigurable('form', TRUE)->setRevisionable(FALSE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    $fields['text_vote_btn'] = BaseFieldDefinition::create('string')->setLabel(t('vote_btn'))->setDescription(t('The string on vote brn of the Candidat entity entity.'))->setRevisionable(TRUE)->setDefaultValue("voter")->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Created'))->setDescription(t('The time that the entity was created.'));
    
    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'))->setDescription(t('The time that the entity was last edited.'));
    
    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')->setLabel(t('Revision translation affected'))->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))->setReadOnly(TRUE)->setRevisionable(TRUE)->setTranslatable(TRUE);
    
    return $fields;
  }
  
}
