<?php

/**
 * @file
 * Contains \Drupal\school_management\Form\SchoolSearchForm.
 */

namespace Drupal\school_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;


class SchoolSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'school_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $default_val = '';
    if(null !== \Drupal::request()->query->get('search')){
      $default_val = \Drupal::request()->query->get('search');
    }

    $grade_availablev = array();
    $dffgrade = NURSERYUKG;
    if(null !== \Drupal::request()->query->get('grade_available')){
      $grade_availablev = \Drupal::request()->query->get('grade_available');
      if(is_array($grade_availablev)){
        $dffgrade = implode(',', $grade_availablev);
      }
      else{
        $dffgrade = \Drupal::request()->query->get('grade_available');
      }

    }

    $meroption = array(NURSERYUKG => 'Nursery - UKG', CLASSGRADE15 => '1-5th Grade', CLASSGRADE612 => '6-12th Grade');
    //$meroption = array('42,43,44' => 'Nursery - UKG', '45,46,47,48,49' => '1-5th Grade', '50,51,52,53,54,55,56' => '6-12th Grade');

    $form['grade_available'] = [
      '#type' => 'radios',
      '#title' => $this->t(''),
      '#options' => $meroption,
      '#prefix'=>'<div class="location-search-grades">',
      '#suffix'=>'</div>',
      '#default_value' => $dffgrade,
      '#attributes' => array('class' => array('gradetab')),
    ];

    $form['search'] = [
	    '#type' => 'textfield',
      '#default_value' => $default_val,
      '#autocomplete_route_name' => 'school_management.school_search_autocomplete',
	    '#size' => 40,
      '#maxlength' => 150,
      '#placeholder' => 'Enter Locality or Pincode',
      '#attributes' => array('id' =>  array('searchfull')),
    ];

    if( \Drupal::request()->query->get('search') != null){ //checking if search has some value
      $query_params = \Drupal::request()->query->all();
      if(isset($query_params['q'])){
        unset($query_params['q']);
      }
      $query_params['search'] = '';
      $query_string = http_build_query($query_params);
      $form['#markup'] = '<a class="reset-srch" href="/find-a-school/schools?'.$query_string.'">reset</a>';
    }
    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];
    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    return $form;
  }
  /**
 * {@inheritdoc}
 */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $search_text = $form_state->getValue('search') ?? '';
    $response = get_google_search_result($search_text);
    if ($response->status != 'OK') {
      $form_state->setErrorByName('search', $this->t('Please Use Valid Search Suggestion'));
    }
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {
    //Get the full string
    $query_params = \Drupal::request()->query->all();
    $search_text = $form_state->getValue('search') ?? '';
    $grade = $form_state->getValue('grade_available') ?? '';
    $dis = '';
    $latitude = '';
    $longitude = '';
    if ($grade == NURSERYUKG) {
      $dis = NURDISTANSE;
    } else if ($grade == CLASSGRADE15) {
      $dis = CLASS5DISTANSE;
    } else if ($grade == CLASSGRADE612) {
      $dis = CLASS6DISTANSE;
    }
    $gradearr = explode(',', $grade);
    // url to redirect
    $path = '/find-a-school/schools';
    // query string
    $response = get_google_search_result($search_text);
    $latitude = $response->results[0]->geometry->location->lat;
    $longitude = $response->results[0]->geometry->location->lng;
    setcookie("latName", $latitude, time() + 2 * 24 * 60 * 60);
    setcookie("longName", $longitude, time() + 2 * 24 * 60 * 60);
    //checking if user already filtered or searched
    //if (isset($query_params['search']) || isset($query_params['grade_available[]'])) {
      unset($query_params['q']);
      unset($query_params['page']);
      //removing the default q key
      $query_params['search'] = $search_text; //setting the search text
      $query_params['grade_available'] = $gradearr; //setting the search text
      $query_params['distance'] = $dis; //setting the search text
      $query_params['latitude'] = $latitude;
      $query_params['longitude'] = $longitude;
      $path_param = $query_params; //assigning the current query string values
    //}
    // this will redirect to school listing page
    $url = Url::fromUserInput($path, ['query' => $path_param]);
    $form_state->setRedirectUrl($url);
  }
}
