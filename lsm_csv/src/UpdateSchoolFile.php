<?php

namespace Drupal\lsm_csv;

use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Class UpdateSchoolFile.
 *
 * @package Drupal\lsm_csv
 */
class UpdateSchoolFile {

  public function __construct(LoggerInterface $logger, AccountProxyInterface $current_user) {
    $this->logger = $logger;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('logger.factory')->get('csv_import'), $container->get('current_user')
    );
  }

  public static function content($data) {
    if ($data == NULL) {
      throw new BadRequestHttpException('No data received for school updation.');
    }
    // getting data from csv
    $stream = $data['stream'];
    $exam = $data['exam'];
    $total_selection = $data['total_selction'];
    $year = $data['year'];

    // Node creation
    $node = Node::create([
        'type' => 'school',
        'field_notification_select_stream' => $stream,
        'field_class_exams' => $exam,
        'title' => $total_selection,
        'field_testimonial_year' => $year,
    ]);
    try {
      $node->set('field_domain_access', [CMS_DOMAIN, AAKASH_DOMAIN]);
      $node->save();
      $message = t('Created total selection with name %stream.', ['%stream' => $stream]);
      \Drupal::logger('import-total-selection')->notice($message);
      $message_status = t('Created center with name %stream.', ['%stream' => $stream]);
      drupal_set_message($message_status, 'status', TRUE);
    }
    catch (EntityStorageException $e) {
      $message = t(' Total selection creation Failed with name %stream.', ['%stream' => $stream]);
      \Drupal::logger('import-total-selection')->notice($message);
      throw new HttpException(500, 'Internal Server Error', $e);
    }
  }

}
