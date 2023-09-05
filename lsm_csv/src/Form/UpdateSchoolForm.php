<?php

/**
 * @file
 * Contains \Drupal\lsm_csv\Form\UpdateSchoolForm.
 */

namespace Drupal\lsm_csv\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class CsvUploadForm.
 */
class UpdateSchoolForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csv_update_school_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $link_name = 'Download Sample CSV';
    $csv_link_options = array(
      'attributes' => array(
        'class' => array(
          'sample-file',
        ),
      ),
    );
    $file_url = Url::fromUri('internal:' . '/modules/custom/lsm_csv/csv/schools.csv');
    $file_url->setOptions($csv_link_options);
    $link = Link::fromTextAndUrl($link_name, $file_url)->toString();

    $form['upload_csv'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload CSV'),
      '#description' => $this->t('Upload csv file from here.'),
      '#weight' => '0',
      '#upload_location' => 's3://csv-import/total-selection-import/' . date('Y') . '/' . date('M').'',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#required' => TRUE,
    ];
    $form['description'] = array(
      '#markup' => '<p>Use this form to upload a CSV file Data</p>',
    );
    $form['sample_file'] = array(
      '#markup' => $link,
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Upload CSV'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = $form_state->getValue('upload_csv');
    $file = File::load($data[0]);
    if ($file) {
      $file->setPermanent();
      $file->save();
      $path = $file->getFileUri();
      if (file_exists($path) && is_readable($path)) {
        $fp = fopen($path, "r");
        $columns = fgetcsv($fp);
        $csv = array();
        while (!feof($fp)) {
          $row = fgetcsv($fp);
          if (is_array($row)) {
            $combined_row = array_combine($columns, $row);
            $combined_row['payment_details'] = array();
            foreach ($combined_row as $key => $value) {
              if (preg_match("/(pg)([\d+])(\s)(\w+)/", $key, $match)) {
                if ($match['1'] == 'pg' && !is_null($value)) {
                  $combined_row['payment_details'][$match['2']][$match['4']] = $value;
                }
              }
            }
            $csv[] = $combined_row;
          }
        }
        fclose($fp);
      }

      foreach ($csv as $key => $data) {
        $process = \Drupal\lsm_csv\TotalSelectionFile::content($data); // creating first price component
      }
      $batch = [
        'title' => t('Creating Total Selections....'),
        'operations' => [
            [
              [$data]
          ],
        ],
        'file' => '',
      ];
      batch_set($batch);
    }
  }

}
