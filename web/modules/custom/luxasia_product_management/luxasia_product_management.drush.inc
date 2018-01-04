<?php
/**
* @file Contains the code to generate the custom drush commands.
*/
/**
* Implements hook_drush_command().
*/
function luxasia_product_management_drush_command() {
 $items = array();
 $items['luxasia-product-sync'] = [
   'description' => 'Sync the Magento Product to drupal database',
   'aliases' => ['lps'],
 ];
 return $items;
}

/**
* Call back function drush_luxasia_product_management_luxasia_product_sync()
* The call back function name in the  following format
*   drush_{module_name}_{item_id_for_command}()
*/
function drush_luxasia_product_management_luxasia_product_sync() {
  $batch = \Drupal::service('luxasia_product_management.sync')->get_magento_products_sync_batches();
  batch_set($batch);
  drush_backend_batch_process();
}
