<?php

namespace Drupal\luxasia_product_management;

use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Component\Serialization\Json;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\State\StateInterface;

/**
 * Class LuxasiaProductService.
 */
class LuxasiaProductService {

  /**
   * The state key/value store.
   *
   * @var \Drupal\Core\State\StateInterface
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
   * Prepare the batches of product
   * for sync in drupal database
   */
  function get_magento_products_sync_batches() {
    $raw_products = $this->get_magento_products();
    if ($raw_products == NULL) {
      \Drupal::logger('luxasia_product_management')->notice(t('No product found. cron execution stopped.'));
      return;
    }

    \Drupal::logger('luxasia_product_management')->notice(t('Luxasia product sync starts.'));
    $operations = [];
    foreach ($raw_products as $key => $value) {
      $operations[] = ['\Drupal\luxasia_product_management\LuxasiaProductSync::import_luxasia_product', [$value]];
    }
    $batch = [
      'operations' => $operations,
      'finished' => array('Drupal\luxasia_product_management\LuxasiaProductSync', 'import_luxasia_product_finished_batch'),
      'title' => t('Product Sync starts'),
      'init_message' => t('Starting import .....'),
      'progress_message' => t('Completed @current step of @total.'),
      'error_message' => t('Importing has encountered an error.'),
    ];
    return $batch;
  }

  /**
   * Prepare the list of magento products
   * products as array
   */
  public function get_magento_products() {
    try {
      $source_url = $this->state->get('magento_product_feed_url');
      $response = \Drupal::httpClient()->get($source_url, array('headers' => array('Accept' => 'application/json')));
      $data = (string) $response->getBody();

      if (!empty($data)) {
        $rows = Json::decode($data);
        return array_slice($rows, 0, 24);
      }
      return NULL;
    }
    catch (RequestException $e) {
      \Drupal::logger('luxasia_product_management')->error('Magento API exception occurred: @exception', ['@exception' => $e->getMessage()]);
    }
  }

  /**
   * Save magento products as
   * product node
   */
  public function save_magento_products($data) {
    try {
      // Check product exist with id.
      $product_entity = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties([
          'field_external_id' => $data['id']
        ]);

      if ($product_entity) {
        return FALSE;
      }

      $image = file_get_contents($data['thumbnailUrl']);
      $file = file_save_data($image, "public://thumb-" . str_shuffle('Image') . ".png", FILE_EXISTS_REPLACE);
      $node = Node::create([
        'type' => 'product',
        'title' => (string) $data['title'],
        'field_external_id' => (string) $data['id'],
        'field_product_brand' => str_shuffle('Brand'),
        'field_product_name' => (string) $data['title'],
        'field_product_image' => [
          'target_id' => $file->id(),
          'alt' => 'Sample',
          'title' => 'Sample File'
        ],
        'uid' => 1,
        'field_product_availability' => rand(0, 1),
        'body' => str_shuffle('This will be product description.'),
        'field_product_price' => rand(10, 100) . "$",

      ]);
      $node->save();
      return $node->id();
    }
    catch (Exception $e) {
      \Drupal::logger('luxasia_product_management')->error('Product creation failed: @exception', ['@exception' => $e->getMessage()]);
    }
  }
}
