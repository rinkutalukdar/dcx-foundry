<?php

namespace Drupal\luxasia_product_management\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class LuxasiaProductController.
 */
class LuxasiaProductController extends ControllerBase {

  /**
   * Luxasia_product_info.
   *
   * @return string
   *   Return Hello string.
   */
  public function luxasia_product_info() {
    $nids = \Drupal::entityQuery('node')->condition('type','product')->execute();
    $content['text'] = $this->t("Total no of products: ") . count($nids);
    $content['sync_form'] =  \Drupal::formBuilder()->getForm('\Drupal\luxasia_product_management\Form\LuxasiaProductSyncForm');
    return [
      '#theme' => 'luxasia_product_management',
      '#content' => $content,
    ];
  }
}
