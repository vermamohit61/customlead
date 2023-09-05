<?php

namespace Drupal\school_management;

use Drupal\block\Entity\Block;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class DefaultService.
 *
 * @package Drupal\school_management
 */
class TwigExtension extends \Twig_Extension {

  /**
   * {@inheritdoc}
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return 'block_display';
  }

  /**
   * In this function we can declare the extension function.
   */
  public function getFunctions() {
    return array(
      new \Twig_SimpleFunction('get_facilities_data', array($this, 'get_facilities_data'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('get_node_path_alias', array($this, 'get_node_path_alias'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('get_stringlength', array($this, 'get_stringlength'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('get_gradeNames', array($this, 'get_gradeNames'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('get_galleryData', array($this, 'get_galleryData'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('get_distanceData', array($this, 'get_distanceData'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('get_school_type', array($this, 'get_school_type'), array('is_safe' => array('html'))),
    );
  }

  /*
   * This function is used to return icon and facilty name
   */

  public function get_facilities_data($node_id) {
    $facilities = [];
    if (!empty($node_id)) {
	  $query = \Drupal::database()->select('taxonomy_term_field_data', 'td');
      $query->leftjoin('taxonomy_term__field_facility_icon', 'fi', 'fi.entity_id = td.tid');
      $query->leftjoin('paragraph__field_infrastructure', 'pfi', 'pfi.field_infrastructure_target_id = td.tid');
      $query->leftjoin('paragraph__field_weight', 'pfw', 'pfw.entity_id = pfi.entity_id');
      $query->leftjoin('node__field_select_infrastructure', 'nfsi', 'nfsi.field_select_infrastructure_target_id = pfi.entity_id');
      $query->fields('td', array('name'));
      $query->fields('fi', array('field_facility_icon_uri'));
      $query->fields('fi', array('field_facility_icon_title'));
      $query->condition('nfsi.entity_id', $node_id);
      $query->condition('nfsi.bundle', 'school');
      $query->condition('pfi.bundle', 'infrastructure_grouping');
      $query->orderBy('pfw.field_weight_value', 'ASC');
      $query->range(0, 5);
      $result = $query->execute()->fetchAll();
      foreach ($result as $key => $object) {
        $facilities[$key]['name'] = $object->name;
        $facilities[$key]['class'] = $object->field_facility_icon_title;
        $facilities[$key]['url'] = $object->field_facility_icon_uri;
      }
    }
    return $facilities;
  }

  /*
   * This function is used to return path alias in school-listing and front page
   */
  public function get_node_path_alias($node_id) {
    $url_alias = [];
    $url_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $node_id);
    return $url_alias;
  }

  /*
   * This function is used to return string length
   */
  public function get_stringlength($str) {
    $strlenth = strlen($str);
    return $strlenth;
  }

  /*
   * This function is used to return inbound outbound grades
   */
  public function get_gradeNames($nid) {
    $machine_name = 'school';
    $query = \Drupal::database()->select('node__field_grades_available', 'ga');
    $query->join('taxonomy_term_field_data', 'trc', 'ga.field_grades_available_target_id = trc.tid');
    $query->fields('trc', ['name']);
    $query->condition('ga.bundle', $machine_name);
    $query->condition('ga.entity_id', $nid);
    $query->orderBy('trc.weight', 'ASC');
    $result = $query->execute()->fetchCol();
    $final_result[] = $result[0];
    $final_result[] = end($result);
    return $final_result;
  }

  /*
   * This function is used to return inbound outbound grades
   */
  public function get_galleryData($nid) {
    return get_school_images($nid);
  }

  /*
   * This function is used to return inbound outbound grades
   */
  public function get_distanceData($nid) {
    return round(get_distance($nid), 2);
  }

  /**
   * Loads the tree of a vocabulary.
   *
   * @param string $vocabulary
   *   Machine name
   *
   * @return array
   */
  function get_school_type($vid, $tid) {
    return get_api_ref_frm_tid($vid, $tid);
  }
}
