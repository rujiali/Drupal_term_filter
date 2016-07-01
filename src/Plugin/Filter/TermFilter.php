<?php

namespace Drupal\termfilter\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a filter to convert terms into links.
 *
 * @filter(
 *   id = "termfilter",
 *   title = @Translation("Term Filter"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   settings = {
 *   },
 *   weight = 0
 * )
 */
class TermFilter extends FilterBase {
  protected $termfilters;

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['term_filter'] = array(
      '#type' => 'fieldset',
      '#title' => t('Term filter'),
      '#description' => t('You can define a global list of terms to be filtered on the <a href="!url">Terms Filter settings page</a>.', array('!url' => url('admin/config/content/termfilter'))),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    //$list = _termfilter_list();
    //return _termfilter_perform_subs($text, $list);
  }
  
  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return t("If you include a term in your post that's in the whitelist, it will be augmented by an &lt;a&gt; tag.") .'<br />';
    }
    else {
      return t('Whitelisted terms will be augmented with an &lt;a&gt; tag.');
    }
  }
}
