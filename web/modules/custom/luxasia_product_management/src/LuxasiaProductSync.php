<?php

namespace Drupal\luxasia_product_management;

class LuxasiaProductSync {
  public static function import_luxasia_product($data, &$context) {
    $nid = \Drupal::service('luxasia_product_management.sync')->save_magento_products($data);
    if (!$nid) {
      $context['message'] = t('Product already exist in drupal database');
      return;
    }
    $context['message'] = t('Saved product: nid - @nid', ['@nid' => $nid]);
  }

  public static function import_luxasia_product_finished_batch($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(count($results),
        'One post processed.', '@count product processed.'
      );
    }
    else {
      $message = t('Product sync failed with an error.');
    }
    \Drupal::logger('luxasia_product_management')->notice($message);
  }
}
