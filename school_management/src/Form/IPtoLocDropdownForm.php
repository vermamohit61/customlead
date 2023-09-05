<?php
/**
 * @file
 * Contains \Drupal\school_management\Form\IPtoLocDropdownForm.
 */
namespace Drupal\school_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class IPtoLocDropdownForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'ip_state_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state){
      $ip_response = json_decode(get_states_from_ip(), true);
      if (empty($ip_response['longitude'])) {
        $state_id = 0;
      }
      $state_id = (int) get_org_state_code($ip_response['region_iso_code']);
      $state_option = get_state_codes();
        foreach($state_option as $key => $state_values){
            $state_result[$key] = $state_values['name'];
        }
        $form['ip_state_select'] = [
        '#type' => 'select',
        //'#title' => $this->t('You are in'),
        '#options' => $state_result,
        '#default_value' => $state_id,
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }
}
