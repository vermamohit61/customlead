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
 * Provides a resource to post school nodes.
 *
 * @RestResource(
 *   id = "create_school_resource_post_api",
 *   label = @Translation("Create School Rest Resource"),
 *   uri_paths = {
 *     "create" = "/rest/api/v1/post/create-school"
 *   }
 * )
 */
class CreateSchoolResource extends ResourceBase {

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
   * Responds to POST requests.
   * Creates a new node school type.
   * @param mixed $data
   *   Data to create the node.
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {
    $nid = nucleus_id_exist($data['nucleus_id']);

    if ((!empty($data['nucleus_id'])) && ($nid != null)) {
      CreateSchoolResource::update_school_data($data, $nid);
      $this->logger->notice('school updated with nucleus_id ' . $data['nucleus_id']);
      $response = ['School updated with id' => $data['nucleus_id']];
      return new ModifiedResourceResponse($response, 201);
    }
    $alias = '/'.$data['url_alias'] ?? ''; //getting url alias
    $coordinates = ['lat'=> '','long'=> '']; //initialise array
    $gmap_url = $data['gmap_url'];
    if(!empty($gmap_url)){
      if(get_lat_long_from_url($gmap_url)){
        $coordinates = get_lat_long_from_url($gmap_url);
    }
    $node = Node::create(
        [
          'type' => 'school',
          'title' => $data['school_name_official'],
          'body' => [
            'summary' => '',
            'value' => $data['write_up_about_school'],
            'format' => 'full_html',
          ],
          'field_school_name' => $data['school_name_print'],
          'field_city' => reference_target_id('cities', $data['city']), //city name
          'field_pincode' => $data['pincode'],
          'field_school_district' => $data['district'], //district name
          'field_google_map_url' => get_link_field_data($data['gmap_url']),
          'field_cta' => [
            'value' => $data['google_map_code'],
            'format' => 'full_html',
          ],
          'field_geo_location' => ['lat'=> $coordinates['lat'], 'lng' => $coordinates['long']],
          'field_affiliation_code' => $data['affiliation_code'],
          'field_nucleus_id' => $data['nucleus_id'], //unique nucleus id
          'field_name_of_school_owner' => $data['name_school_owner'], //name of school owner
          'field_school_owner_photo' => get_link_field_data($data['school_owner_photo']),
          'field_school_owner_video' => get_link_field_data($data['school_owner_video']), //url for school owner video
          'field_school_virtual_tour_video' => get_link_field_data($data['school_virtual_tour_video']), //url for school virtual video
          'field_established_in_year' => $data['established_in_year'], //year
          'field_school_photo' => get_link_field_data($data['school_photo']),
          'field_co_education_status' => $data['co_education_status'], //yes and no
          'field_marketing_template' => reference_target_id('school_type', $data['co_education_status']), //term id value,school type
          'field_board_affiliation' => reference_target_id('board_affiliation', $data['board_affiliation']), //term id value
          'field_medium_of_education' => reference_target_id('medium_of_education', $data['medium_of_education']), //term id value
          'field_message' => $data['specify_medium'], //specify medium
          'field_banner_image' => get_link_field_data($data['banner_image']),//banner image url
          'field_school_logo' => get_link_field_data($data['school_logo']),//logo url
          'field_school_brochure' => get_link_field_data($data['school_brochure']),//file url
          'field_contact_name' => $data['contact_name'], //name
          'field_contact_phone1' => $data['contact_phone_1'], //phone number 1
          'field_contact_phone2' => $data['contact_phone_2'], //phone number 2
          'field_facebook_url' => get_link_field_data($data['facebook_url']),//fb url
          'field_address_line1' => $data['address_line_1'], //address 1
          'field_address_line2' => $data['address_line_2'], //address 2
          'field_grades_available' => $this->reference_termid_array($data['grades'], 'grades'),
          'field_name_of_contact_person' => $data['name_of_contact_person'], //name of contact person
          'field_contact_person_phone' => $data['contact_person_phone'], //contact person phone
          'field_office_start_time' => reference_target_id('office_timings', $data['office_start_time']), //office start timing
          'field_office_close_time' => reference_target_id('office_timings', $data['office_close_time']), //office end timing
          'field_fee_structure' => get_link_field_data($data['fee_structure']),
          'field_admission_process_step_1'=> $data['admission_process_step_1'], //admission process
          'field_admission_process_step_2'=> $data['admission_process_step_2'], //admission process
          'field_admission_process_step_3'=> $data['admission_process_step_3'], //admission process
          'field_admission_process_step_4'=> $data['admission_process_step_4'], //admission process
          'field_admission_process_step_5'=> $data['admission_process_step_5'], //admission process
          'field_admission_process_step_6'=> $data['admission_process_step_6'], //admission process
          //New Admission Section
          'field_website_link' => [
            'uri' => $data['website_url'],
            'title' => $data['website_url'], //title
          ],


          'field_acad_year_starting_month' => reference_target_id('months', $data['academic_year_start_month']), // academic start year
          'field_mid_year_intake' => $data['mid_year_intake'], //mid year intake
          'field_admissions_open_all_year' => $data['admission_open_all_year'], //admission open all year
          'field_start_month' => reference_target_id('months', $data['admission_start_month']), // admission start month
          'field_end_month' => reference_target_id('months', $data['admission_end_month']), //admission end month

          //New Testimonial Section
          'field_select_infrastructure' => $this->get_infra_component($data),
          'field_faculties' => $this->get_faculty_component($data),//referencing faculty members
          //'field_testimonial' => $data['testimonials'],
          'field_testimonials' => $this->get_testimonial_component($data),
          //'field_principal_photo' => get_link_field_data($data['principal_photo']),//principal photo
          'field_academic_rankers' => $this->get_rankers_component($data), //assigning values of rankers
          'field_select_infrastructure' => $this->get_infra_component($data), //assigning values of infrastructure
          //'field_salutation' =>  reference_target_id('salutation', $data['salutation']), //salutation
          //'field_name_of_the_principal' =>  $data['name_of_principal'], //name of principal
          //'field_qualification' =>  $data['qualification'], //teacher qualification
          'field_number_of_teachers' =>  $data['number_of_teachers'], //number of teachers
          'field_teacher_student_ratio'=>  $data['teacher_student_ratio'], //ratio
          'field_comment_if_any' => $data['comment'],//comments by parents
          'field_banner_text'=> get_schools_attribute($data),

          'path' => [
            'alias' => $alias,
            'pathauto' => PathautoState::SKIP,
          ]
        ]
      );
      $node->set('field_meta_tags', serialize([
        'title' => $data['metatag_page_title'],
        'description' => $data['metatag_description'],
        'keywords' => $data['metatag_keywords'],
      ]));
      $node->enforceIsNew();
      $node->save();
      $this->logger->notice($this->t("Node school type with nid @nid saved!\n", ['@nid' => $node->id()]));
      $nodes[] = $node->id();

    $message['info'] = $this->t("New school created successfully with id : @message", ['@message' => implode(",", $nodes)]);
    $message['data'] = implode(",", $nodes);
    $message_response = json_encode($message);
    $payload = json_encode($data);
    $type = 'gt-cms';
    store_log_data($payload, $message_response, $type);
    return new ResourceResponse($message, 200);

  }
}

  /**
   * Loads the term array field in vocabulary.
   * @param string $vid, $tid_name_array
   * @return array
   */
  public function reference_termid_array($tid_name_array, $vid){
    foreach($tid_name_array as $val){
      $tids[] = reference_target_id($vid, $val);
    }
    return $tids;
  }

    /**
   * Loads the faculties paragraph data.
   * @param string $data
   * @return array
   */
  public function get_members_component($data, $type){
    $members = [];
    if($type == 'faculties'){
      $combined_data[0]['image'] = $data['principal_photo'][0]['image'];
      $combined_data[0]['name'] = $data['name_of_principal'];
      $combined_data[0]['title'] = $data['principal_photo'][0]['title'];
      $combined_data[0]['alt'] = $data['principal_photo'][0]['alt'];
      $final_data = array_merge($combined_data, $data['teachers_image_and_name']);
      foreach ($final_data as $key => $members_data) {
        if($key == 0){
          $members_data['designation'] = 'Principal';
        }else{
          $members_data['designation'] = '';
        }
        $members[] = Paragraph::create([
        'type' => $type,
        'field_ranker_name' => $members_data['name'],
        'field_ranker_image' => get_link_field_datap($members_data),
        'field_question' => $members_data['designation'],
        ]);
      }
    }elseif($type == 'two_column_layout'){
    foreach($data['ranker_image_and_name'] as $members_data) {
      $members[] = Paragraph::create([
        'type' => $type,
        'field_ranker_name' => $members_data['name'],
        'field_ranker_image' => get_link_field_datap($members_data),
      ]);
    }
  }
    return $members;
  }

/**
 * Loads the rankers paragraph data.
 * @param string $data
 * @return array
 */
  public function get_rankers_component($data){
    $rankers = [];
    foreach ($data['ranker_image_and_name'] as $rankers_data) {
      $rankers[] = Paragraph::create([
        'type' => 'two_column_layout',
        'field_ranker_name' => $rankers_data['name'],//name
        'field_ranker_image' => get_link_field_datap($rankers_data),//image
        'field_question' => $rankers_data['achievement'],//achievement
      ]);
    }
    return $rankers;
  }


/**
 * Loads the faculty paragraph data.
 * @param string $data
 * @return array
 */
  public function get_faculty_component($data){
    $faculties = [];
    foreach ($data['teachers_image_and_name'] as $faculty_data) {
      $faculties[] = Paragraph::create([
        'type' => 'faculties',
        'field_ranker_image' => get_link_field_datap($faculty_data), //term id value,
        'field_ranker_name' => $faculty_data['name'], //term id value,
        'field_designation' => reference_target_id('designation', $faculty_data['designation']), //term id value,
        'field_child_name' => $faculty_data['qualification'],
        'field_salutation' => reference_target_id('salutation', $faculty_data['salutation']), //term id value,,
      ]);
    }
    return $faculties;
  }

  /**
   * Loads the infra paragraph data.
   * @param string $data
   * @return array
   */
  public function get_infra_component($data){
    $infrastructure = [];
    foreach($data['infrastructure'] as $infrastructure_data) {
      $infrastructure[] = Paragraph::create([
        'type' => 'infrastructure_grouping',
        'field_infrastructure' => reference_target_id('infrastructure', $infrastructure_data['category']), //term id value,
        'field_status' => $infrastructure_data['status'],
        'field_image' => get_link_field_img_data($infrastructure_data['image'][0]),
        'field_infrastructure_images' => get_link_field_data_array($infrastructure_data['image']),
        'field_weight' => $infrastructure_data['weight'],
      ]);
    }
    return $infrastructure;
  }
    /**
   * Loads the testimonial paragraph data.
   * @param string $data
   * @return array
   */
  public function get_testimonial_component($data){
    $testimonials = [];
    foreach($data['testimonials'] as $testimonial_data) {
      $testimonials[] = Paragraph::create([
        'type' => 'testimonial',
        'field_ranker_name' => $testimonial_data['parent_name'], // parent name,
        'field_testimonial' => [
            'value' => $testimonial_data['testimonial'], // testimonial value
            'format' => 'full_html',
        ],
      ]);
    }
    return $testimonials;
  }

/**
   * update the school by passing the nucleas id.
   * @param $data
   * @return The HTTP response object.
   */
   public function update_school_data($data, $nid) {
    $alias = '/'.$data['url_alias'] ?? ''; //getting url alias
    $coordinates = ['lat'=> '','long'=> '']; //initialise array
    $gmap_url = $data['gmap_url'];
    if(!empty($gmap_url)){
      if(get_lat_long_from_url($gmap_url)){
        $coordinates = get_lat_long_from_url($gmap_url);
      }
      $node = Node::load($nid);
      $node->setTitle($data['school_name_official']);
      $node->set('body', [
        'summary' => '',
        'value' => $data['write_up_about_school'],
        'format' => 'full_html',

      ]);
      $node->set('field_school_name', $data['school_name_print']);
      $node->set('field_city', reference_target_id('cities', $data['city']));
      $node->set('field_pincode', $data['pincode']);
      $node->set('field_school_district', $data['district']);
      $node->set('field_google_map_url', get_link_field_data($data['gmap_url']));
      $node->set('field_cta', [
        'value' => $data['google_map_code'],
        'format' => 'full_html',

      ]);
      $node->set('field_geo_location', ['lat'=> $coordinates['lat'], 'lng' => $coordinates['long']]);
      $node->set('field_affiliation_code', $data['affiliation_code']);
      $node->set('field_nucleus_id', $data['nucleus_id']);
      $node->set('field_name_of_school_owner', $data['name_school_owner']);
      $node->set('field_school_owner_photo', get_link_field_data($data['school_owner_photo']));
      $node->set('field_school_owner_video', get_link_field_data($data['school_owner_video']));
      $node->set('field_school_virtual_tour_video', get_link_field_data($data['school_virtual_tour_video']));
      $node->set('field_established_in_year', $data['established_in_year']);
      $node->set('field_school_photo', get_link_field_data($data['school_photo']));
      $node->set('field_co_education_status', $data['co_education_status']);
      $node->set('field_marketing_template', reference_target_id('school_type', $data['co_education_status']));
      $node->set('field_board_affiliation', reference_target_id('board_affiliation', $data['board_affiliation']));
      $node->set('field_medium_of_education', reference_target_id('medium_of_education', $data['medium_of_education']));
      $node->set('field_message', $data['specify_medium']); //specify medium
      $node->set('field_banner_image', get_link_field_data($data['banner_image'])); //banner image url
      $node->set('field_school_logo', get_link_field_data($data['school_logo'])); //logo url
      $node->set('field_school_brochure', get_link_field_data($data['school_brochure'])); //file url
      $node->set('field_contact_name', $data['contact_name']); //name
      $node->set('field_contact_phone1', $data['contact_phone_1']); //phone number 1
      $node->set('field_contact_phone2', $data['contactphone_2']); //phone number 1
      $node->set('field_facebook_url', get_link_field_data($data['facebook_url'])); //fb url
      $node->set('field_address_line1', $data['address_line_1']); //address_line_1
      $node->set('field_address_line2', $data['address_line_2']); //address_line_1
      $node->set('field_grades_available', $this->reference_termid_array($data['grades'], 'grades')); //grades
      $node->set('field_name_of_contact_person', $data['contact_person_phone']); //contact_person_phone
      $node->set('field_office_start_time', reference_target_id('office_timings', $data['office_start_time'])); //office_start_time
      $node->set('field_office_close_time', reference_target_id('office_timings', $data['office_close_time'])); //office_close_time
      $node->set('field_fee_structure', get_link_field_data($data['fee_structure'])); //fee_structure
      $node->set('field_admission_process_step_1', $data['admission_process_step_1']); //admission_process_step_1
      $node->set('field_admission_process_step_2', $data['admission_process_step_2']); //admission_process_step_1
      $node->set('field_admission_process_step_3', $data['admission_process_step_3']); //admission_process_step_3
      $node->set('field_admission_process_step_4', $data['admission_process_step_4']); //admission_process_step_4
      $node->set('field_admission_process_step_5', $data['admission_process_step_5']); //admission_process_step_5
      $node->set('field_admission_process_step_6', $data['admission_process_step_6']); //admission_process_step_4
      $node->set('field_website_link', [
        'uri' => $data['website_url'],
        'title' => $data['website_url'], //title
      ]); //New Admission Section
      $node->set('field_acad_year_starting_month', reference_target_id('months', $data['academic_year_start_month'])); //field_acad_year_starting_month
      $node->set('field_mid_year_intake', $data['mid_year_intake']); //field_mid_year_intake
      $node->set('field_admissions_open_all_year', $data['admission_open_all_year']); //field_admissions_open_all_year
      $node->set('field_start_month', $data['start_month']); //field_start_month
      $node->set('field_end_month', $data['end_month']); //field_end_month

      // infrastructure
      $node->set('field_select_infrastructure', $this->get_infra_component($data));
      $node->set('field_faculties', $this->get_faculty_component($data));
      //$node->set('field_testimonial', $data['testimonials']);
      $node->set('field_testimonials', $this->get_testimonial_component($data));
      //$node->set('field_principal_photo', get_link_field_data($data['principal_photo']));
      $node->set('field_academic_rankers', $this->get_rankers_component($data));
      $node->set('field_select_infrastructure', $this->get_infra_component($data));
      //$node->set('field_salutation', reference_target_id('salutation', $data['salutation']));
      //$node->set('field_name_of_the_principal', $data['name_of_principal']);
      //$node->set('field_qualification', $data['qualification']);
      $node->set('field_number_of_teachers', $data['number_of_teachers']);
      $node->set('field_teacher_student_ratio', $data['teacher_student_ratio']);
      $node->set('field_comment_if_any', $data['comment']);
      $node->set('field_banner_text', get_schools_attribute($data));
      $node->set('path', [
        'alias' => $alias,
        'pathauto' => PathautoState::SKIP,
      ]);

      $node->set('field_meta_tags', serialize([
        'title' => $data['metatag_page_title'],
        'description' => $data['metatag_description'],
        'keywords' => $data['metatag_keywords'],
      ]));
      $node->save();
      $nodes[] = $node->id();

      $message['info'] = $this->t("New school updated successfully with id : @message", ['@message' => implode(",", $nodes)]);
      $message['data'] = implode(",", $nodes);
      $message_response = json_encode($message);
      $payload = json_encode($data);
      $type = 'gt-cms';
      store_log_data($payload, $message_response, $type);
      //save to update node
      $node->save();
      // code for updation
    }
  }

}

