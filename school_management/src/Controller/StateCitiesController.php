<?php
/**
 * @file
 * Contains \Drupal\school_management\Controller\StateCitiesController.
 */
namespace Drupal\school_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Connection;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;



Class StateCitiesController extends ControllerBase {
  /**
   * Contents HTTP Client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * AutocompleteController constructor.
   *
   * @param \GuzzleHttp\Client $http_client
   *   The HTTP Client service.
   */
  public function __construct(Client $http_client) {

    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }
  public function GetCityCallback($state_id) {
    if($state_id == 0){
      $result = ['' =>'Select City'];
    }else{
      $query = \Drupal::database()->select('taxonomy_term__field_state', 'ti');
      $query->join('taxonomy_term_field_data', 'nfd', 'ti.entity_id = nfd.tid');
      $query->fields('ti', ['entity_id']);
      $query->fields('nfd', ['name']);
      $query->condition('ti.field_state_target_id', $state_id);
      $result = $query->execute()->fetchAllKeyed(1, 1);
    }
    return new JsonResponse($result);
  }

  public function AdminGetCityCallback($state_id) {
    $query = \Drupal::database()->select('taxonomy_term__field_state', 'ti');
    $query->join('taxonomy_term_field_data', 'nfd', 'ti.entity_id = nfd.tid');
    $query->fields('ti', ['entity_id']);
    $query->fields('nfd', ['name']);
    if($state_id!='All'){
    $query->condition('ti.field_state_target_id', $state_id);
    }
    $result = $query->execute()->fetchAll();
    echo json_encode($result);
    exit;
  }

  /**
  * Handler for search autocomplete request.
  */
  public function schoolSearchAutocomplete(Request $request) {
    $results = '';
      if($input = $request->query->get('q')){
        if(strlen($input) > 2){
          $typedString = Tags::explode($input);
          $typedString = mb_strtolower(array_pop($typedString));
          $trimedString = preg_replace("/\s+/", "+", $typedString);
          $results = $this->showRelatedPlaces($trimedString);
          return new JsonResponse($results);
        }
      }
      return new JsonResponse($results);
  }

 /**
 * Handler for pincode search autocomplete request.
 */
  public function pincodeSearchAutocomplete(Request $request) {
    $matches = [];
    if($input = $request->query->get('q')){
      if(strlen($input) > 2){
        $typed_string = Tags::explode($input);
        $typed_string = mb_strtolower(array_pop($typed_string));
        $query = \Drupal::database()->select('node__field_pincode', 'pc');
        $query->fields('pc', array('field_pincode_value'));
        $query->condition('pc.bundle', 'school');
        $result = $query->execute()->fetchCol();
        $suggestion_result = array_unique($result);
        $matches = [];
        foreach ($suggestion_result as $k => $v) {
          if (preg_match("/{$typed_string}/i", $v)) {
            $matches[] = [
              'value' => $v,
              'label' => $v,
            ];
          }
        }
      }
    }
  return new JsonResponse($matches);
  }

  /**
   * Function to shows related places to the typed in the autocomplete field.
   */
  public function showRelatedPlaces($trimedString) {
    $results = [];
    $apiKey = GOOGLE_PLACE_AUTOCOMPLETE_API_KEY;
    $endpoint = GOOGLE_PLACE_AUTOCOMPLETE_API.'?input='.$trimedString. '&key=' . $apiKey.'&components=country:in';
    try {
      $request = $this->httpClient->get($endpoint);
      $response = $request->getBody()->getContents();
      $jasonResponse = json_decode($response);
      for ($i = 0; $i < count($jasonResponse->predictions); $i++) {
        $description = $jasonResponse->predictions[$i]->description;
        $main_text = $jasonResponse->predictions[$i]->structured_formatting->main_text;
        $secondary_text = $jasonResponse->predictions[$i]->structured_formatting->secondary_text;
        $searchedPlace = '<span class="main-text">' . $main_text . '</span>, <span class="secondary-text">' . $secondary_text . '</span>';
        $results[] = [
          'value' => $description,
          'label' => $searchedPlace,
        ];
      }
      return($results);
    }
    catch (RequestException $e) {
      return($this->t('Error occured.'));
    }
  }

}
