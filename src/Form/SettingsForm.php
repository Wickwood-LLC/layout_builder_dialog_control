<?php

namespace Drupal\layout_builder_dialog_control\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;

use Drupal\contextual\Element\ContextualLinks;

/**
 * Configure example settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /** 
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'layout_builder_dialog_control.settings';

  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'layout_builder_dialog_control_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $element = [
      '#contextual_links' => [
        'layout_builder_block' => [
          'route_parameters' => [
            'section_storage_type' => 'test',
            'section_storage' => 'dummy',
            'delta' => 'dummy',
            'region' => 'dummy',
            'uuid' => 'dummy',
          ]
        ],
      ]
    ];

    $contextual_links_manager = \Drupal::service('plugin.manager.menu.contextual_link');

    $links = $contextual_links_manager->getContextualLinkPluginsByGroup('layout_builder_block');

    $contextual_links = [];
    foreach ($links as $id => $link) {
      $contextual_links[$id] = $link['title'];
    }

    $form['contextual_links'] = [
      '#type' => 'container',
      '#title' => $this->t('Load hidden for roles'),
      '#tree' => TRUE,
    ];

    $links_settings = $config->get('layout_builder_contextual_links');
    foreach ($links as $key => $link) {
      $form['contextual_links'][$key] = [
        '#type' => 'fieldset',
        '#title' => $link['title'],
      ];
      $type_settings = isset($links_settings[$key]) ? $links_settings[$key] : ['dialog_type' => 'default'];

      $form['contextual_links'][$key]['route_name'] = [
        '#type' => 'value',
        '#value' => $link['route_name'],
      ];
      $form['contextual_links'][$key]['dialog_type'] = [
        '#type' => 'select',
        '#options' => [
          'default' => $this->t('Default'),
          'overlay' => $this->t('Overlay'),
          'off-screen' => $this->t('Off screen'),
        ],
        '#default_value' => $type_settings['dialog_type'],
      ];
    }

    return parent::buildForm($form, $form_state);
  }


  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('layout_builder_contextual_links', $form_state->getValue('contextual_links'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
