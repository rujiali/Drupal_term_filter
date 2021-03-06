<?php

namespace Drupal\termfilter\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class TermfilterForm
 * @package Drupal\termfilter\Form
 */
class TermfilterForm extends ConfigFormBase {
  /**
   * Drupal configFactory object.
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'termfilter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['termfilter.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Compose the vocabulary list.
    $vocab_list = taxonomy_vocabulary_get_names();
    $checklist_vocab_array = [];
    foreach ($vocab_list as $key => $item) {
      $vocab = Vocabulary::load($key);
      $value = $vocab->label();
      $checklist_vocab_array[$key] = $value;
    }

    $termfilter_settings = $this->configFactory->getEditable('termfilter.settings');
    $default_vocabs = $termfilter_settings->get('vocablist');

    $form['termfilter_vocablist'] = [
      '#type' => 'select',
      '#title' => t('Select the vocabulary you want to filter.'),
      '#options' => $checklist_vocab_array,
      '#default_value' => $default_vocabs,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $formState) {
    $vocabList = $formState->getValue('termfilter_vocablist');
    $this->configFactory->getEditable('termfilter.settings')
      ->set('vocablist', $vocabList)
      ->save();
  }
}
