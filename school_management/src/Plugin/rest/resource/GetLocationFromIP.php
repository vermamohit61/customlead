<?php

namespace Drupal\school_management\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\pathauto\PathautoState;
use Drupal\rest\ModifiedResourceResponse;


/**
 * Provides a resource to get user location from api.
 *
 * @RestResource(
 *   id = "get_location_from_ip_get_api",
 *   label = @Translation("Get Location from IP"),
 *   uri_paths = {
 *     "canonical" = "/rest/api/v1/get/get-location"
 *   }
 * )
 */
class GetLocationFromIP extends ResourceBase {

  use StringTranslationTrait;

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;
  private $status;
	private $info;
	private $data;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user) {

    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
    $this->status = false;
		$this->info = null;
    $this->data = null;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('create_school'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to GET requests.
   * Get location from IP.
   * @param mixed $data
   *   Data to get the location.
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
public function get() {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    try {
      // Initialize cURL.
      $ch = curl_init();
      // Set the URL that you want to GET by using the CURLOPT_URL option.
      curl_setopt($ch, CURLOPT_URL, 'https://ipgeolocation.abstractapi.com/v1/?api_key=83eb0873ade64c949e66e2bb894904b9');
      // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      // Execute the request.
      $response = curl_exec($ch);
      // Close the cURL handle.
      curl_close($ch);
      // Print the data out onto the page.
      //echo $data;
    } catch (RequestException $exception) {
      \Drupal::logger('school_management')->info($exception);
    }
    return new ResourceResponse($response);
  }
}

