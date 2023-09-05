<?php

namespace Drupal\school_management\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'School Map' block.
 *
 * @Block(
 *  id = "school_map",
 *  admin_label = @Translation("School Map"),
 * )
 */
class SchoolMap extends BlockBase {

  /**
  * {@inheritdoc}
  */
  public function build() {
    $db = \Drupal::database();
    $lat = round(\Drupal::request()->query->get('latitude'), 7); // Round off to 7 digits
    $long = round(\Drupal::request()->query->get('longitude'), 7);

    $select_db = "SELECT *, (((acos(sin(($lat*pi()/180)) * sin((field_geo_location_lat*pi()/180))+cos(($lat*pi()/180)) * cos((field_geo_location_lat*pi()/180)) * cos((($long- field_geo_location_lng)* pi()/180))))*180/pi())*60*1.1515*1.609344) as distance FROM leadschool_node__field_geo_location loc, leadschool_node_field_data n where n.status = 1 and loc.entity_id = n.nid";
    if (!empty(\Drupal::request()->query->get('distance'))) {
      $dis = \Drupal::request()->query->get('distance');
      $select_db .= ' HAVING distance < ' . $dis;
    }
    $select_db .= ' ORDER BY distance ASC';
    $select_db = $db->query($select_db);
    $school_list = $select_db->fetchAll();
    
    $school_location = '[';
    $school_name = '[';
    foreach ($school_list as $key => $value) {
      $school_location .= "['', " . $value->field_geo_location_lat . ', ' . $value->field_geo_location_lng . "],";
      $school_name .= "['<h4>" . $value->title . "</h4>'],";
    }
    $school_location .= ']';
    $school_name .= ']';

    $data = [
      'loc' => $school_location,
      'name' => $school_name
    ];

    return [
      '#theme' => 'school_map',
      '#data' => $data
    ];
  }

  /**
   * Disable the block cache
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
