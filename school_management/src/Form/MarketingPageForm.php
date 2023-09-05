<?php

/**
 * @file
 * Contains \Drupal\school_management\Form\MarketingPageForm.
 */

namespace Drupal\school_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

class MarketingPageForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'marketing_page_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // example.com?tag=1
    $default_val = '';
    if(null !== \Drupal::request()->query->get('msearch')){
      $default_val = \Drupal::request()->query->get('msearch');
    }
	  $form['msearch'] = [
	    '#type' => 'hidden',
	    '#default_value' => $default_val,
	    '#size' => 40,
	    '#maxlength' => 128,
	  ];
	  // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Schools'),
    ];
   return $form;
  }

/**
 * submitForm function 
 */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //Get the current path url
    $current_path = \Drupal::service('path.current')->getPath();
    //Get the full url
    $current_uri = \Drupal::request()->getRequestUri();
    if($current_path != $current_uri ){
      $queryString = $_SERVER['QUERY_STRING'];
      $query_parmas = \Drupal::request()->request->all();
      if(isset($query_parmas['msearch'])){
        // query string
        $path_param = [
        'grades' => '',
        'board' => '',
        'state' => '',
        'city' => '',
        'pincode' => '',
        'infrastructure' => ''
        ];
        // save admin marketing page
          $node = Node::create(['type' => 'marketing_management']);
          $node->set('title', 'Marketing test');
          $node->set('field_search_params', $queryString);
          $node->set('field_page_type', 'dynamic');
          $node->set('uid', 1);
          $node->langcode = 'en';
          $node->status = 0;
          $node->enforceIsNew();
          $node->created = REQUEST_TIME;
          $node->changed = REQUEST_TIME;
          $node->save();
          // url to redirect
       $path = "/node/".$node->id()."/edit";
      // use below if you have to redirect on your known url
      $url = Url::fromUserInput($path, ['query' => $path_param]);
      $form_state->setRedirectUrl($url);
    }
   }else{
    $message = \Drupal::messenger()->addMessage(t('Please Select Filter and Search.'), 'error');
    return $message;
  }
 }
}
