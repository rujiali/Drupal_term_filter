<?php

/**
 * @file
 * Administrative file for termfilter module.
 */


/**
 * Query to get the list of terms to filter in the
 * filter processing stage. Does not use a pager.
 *
 * @return
 *   a list of terms to filter
 */
function _termfilter_list($refresh = 0) {
  $vocab_name = variable_get('termfilter_vocablist');
  $vocab = taxonomy_vocabulary_machine_name_load($vocab_name);
  $list = array();
  $results = db_select('taxonomy_term_data', 't')
    ->fields('t')
    ->condition('t.vid', $vocab->vid, '=')
    ->addTag('node_access')
    ->execute();
  while($record = $results->fetchAssoc()) {
    $list[$record['name']] = $vocab->machine_name;
  }
  return $list;
}

/**
 * Admin settings page callback.
 *
 * @return
 *  A list of vocabularies to filter with.
 *
 */
function termfilter_admin_list($form, &$form_state) {

  $vocab_list = taxonomy_vocabulary_get_names();
  $checklist_vocab_array = array();
  foreach ($vocab_list as $item) {
    $key = $item->machine_name;
    $value = $item->name;
    $checklist_vocab_array[$key] = $value;
  }

  $form['termfilter_vocablist'] = array(
    '#type'             => 'radios',
    '#title'            => t('Select the vocabulary you want to filter.'),
    '#position'         => 'left' ,
    '#options'          => $checklist_vocab_array ,
    '#default_value'    => variable_get('termfilter_vocablist'),
  );

  return system_settings_form($form);
}
