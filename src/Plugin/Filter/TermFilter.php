<?php

namespace Drupal\termfilter\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\Plugin\FilterBase;
use Drupal\termfilter\TermfilterData;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\filter\FilterProcessResult;

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
class TermFilter extends FilterBase implements ContainerFactoryPluginInterface {
  
  protected $termfilterData;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, TermfilterData $termfilterData) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    
    $this->termfilterData = $termfilterData;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('termfilter.data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['term_filter'] = array(
      '#type' => 'fieldset',
      '#title' => t('Term filter'),
      '#description' => t('You can define a global list of terms to be filtered on the <a href="!url">Terms Filter settings page</a>.', array('!url' => Url::fromRoute('termfilter.admin')->toString())),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $list = $this->termfilterData->getTermfilterList();
    $new_text = $this->termfilterData->termfilterPerformSubs($text, $list);
    return new FilterProcessResult($new_text);
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
