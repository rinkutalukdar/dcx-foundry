<?php

namespace Drupal\luxasia_product_management\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;

/**
 * Class LuxasiaProductConfigForm.
 */
class LuxasiaProductConfigForm extends ConfigFormBase {

  /**
   * Drupal\Core\State\State definition.
   *
   * @var \Drupal\Core\State\State
   */
  protected $state;
  /**
   * Constructs a new LuxasiaProductConfigForm object.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'luxasia_product_management.luxasiaproductconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'luxasia_product_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->state;
    $form['magento_product_feed_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Magento Product feed url'),
      '#description' => $this->t('Please enter the url of the magento product feed url.<br> e.g: https://jsonplaceholder.typicode.com/photos'),
      '#maxlength' => 200,
      '#size' => 64,
      '#default_value' => $config->get('magento_product_feed_url'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->state;
    $config->set('magento_product_feed_url', $form_state->getValue('magento_product_feed_url'));
    drupal_set_message($this->t('Configuration has been saved.'));
    $url = Url::fromRoute('luxasia_product_management.luxasia_product_info');
    $form_state->setRedirectUrl($url);
  }

}
