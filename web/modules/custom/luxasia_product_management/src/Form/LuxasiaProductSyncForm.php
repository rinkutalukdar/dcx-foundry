<?php

namespace Drupal\luxasia_product_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\luxasia_product_management\LuxasiaProductService;

/**
 * Class LuxasiaProductSyncForm.
 */
class LuxasiaProductSyncForm extends FormBase {

  /**
   * Drupal\luxasia_product_management\LuxasiaProductService definition.
   *
   * @var \Drupal\luxasia_product_management\LuxasiaProductService
   */
  protected $luxasiaProductManagementSync;
  /**
   * Constructs a new LuxasiaProductSyncForm object.
   */
  public function __construct(LuxasiaProductService $luxasia_product_management_sync) {
    $this->luxasiaProductManagementSync = $luxasia_product_management_sync;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('luxasia_product_management.sync')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'luxasia_product_sync_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['sync_now'] = [
      '#markup' => $this->t('Do you want to sync now. Click here: '),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sync Now'),
    ];
    return $form;
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
    $batch = $this->luxasiaProductManagementSync->get_magento_products_sync_batches();
    batch_set($batch);
  }

}
