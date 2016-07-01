<?php

namespace Drupal\termfilter\TermfilterForm;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form;

class TermfilterForm extends ConfigFormBase {

  public function getFormId() {
    return 'termfilter_form';
  }

  public function getEditableConfigNames() {
    return ['termfilter.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // Compose the vocabulary list.
    $vocab_list = taxonomy_vocabulary_get_names();
    $checklist_vocab_array = array();
    foreach ($vocab_list as $item) {
      $key = $item->machine_name;
      $value = $item->name;
      $checklist_vocab_array[$key] = $value;
    }

    $termfilter_settings = \Drupal::config('termfilter.settings');
    $default_vocabs = $termfilter_settings->get('termfilter_vocablist');
    
    $form['termfilter_vocablist'] = array(
      '#type'             => 'radios',
      '#title'            => t('Select the vocabulary you want to filter.'),
      '#position'         => 'left' ,
      '#options'          => $checklist_vocab_array ,
      '#default_value'    => $default_vocabs,
    );
   
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $vocabList = $form_state->getValue('termfilter_vocablist');
    \Drupal::configFactory()->getEditable('termlist.settings')->set('termlist_vocablist', $vocabList)->save();
  }
}