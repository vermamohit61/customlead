<?php

namespace Drupal\parent_login_registration\Plugin\Block;

use Drupal\common_leadschool\Pagination;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'PaginationBlock' block.
 *
 * @Block(
 *  id = "pagination_block",
 *  admin_label = @Translation("Pagination block"),
 * )
 */
class PaginationBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        if (isset($_COOKIE['latName']) && isset($_COOKIE['longName'])) {
            $view = views_get_view_result('school_listing', 'page_1');
            global $base_url;
            $limit = 12;
            $lat = $_COOKIE['latName'];
            $long = $_COOKIE['longName'];
            $lat = round($lat, 7); //rounding off to 7 digits
            $long = round($long, 7);
            $select = "SELECT * FROM (SELECT *,(((acos(sin(($lat*pi()/180)) * sin((field_geo_location_lat*pi()/180))+cos(($lat*pi()/180)) * cos((field_geo_location_lat*pi()/180)) * cos((($long- field_geo_location_lng)* pi()/180))))*180/pi())*60*1.1515 ) as distance FROM leadschool_node__field_geo_location) as X";
            if (!empty(\Drupal::request()->query->get('distance'))) {
                $dis = \Drupal::request()->query->get('distance');
                $select .= ' HAVING distance < ' . $dis;
            }
            $select .= ' ORDER BY distance ASC';
            $query = \Drupal::database()->query($select);
            $query_result = $query->fetchAll();
            // get shorted school by distance
            $stored_school = [];
            foreach ($query_result as $key => $val) {
                $stored_school[] = $val->entity_id;
            }
            $school_list = [];
            foreach ($stored_school as $k => $id) {
                foreach ($view as $key => $results) {
                    if ($id == $results->_entity->get('nid')->value) {
                        $results->index = $k;
                        $school_list[] = $results;
                    }
                }
            }

            $distance = \Drupal::request()->query->get('distance');
            $search_text = \Drupal::request()->query->get('search');
            $grv = '';
            if (null !== \Drupal::request()->query->get('grade_available')) {
                $gradearr = \Drupal::request()->query->get('grade_available');
                $grv = '';
                foreach ($gradearr as $gk => $gv) {
                    $grv .= '&grade_available[]=' . $gv;
                }
            }
            $baseURL = $base_url . '/find-a-school/schools?search=' . $search_text . $grv . '&distance=' . $distance;
            $pagConfig = array(
                'baseURL' => $baseURL,
                'totalRows' => count($view),
                'perPage' => $limit,
                'firstLink' => 'First',
                'nextLink' => '&raquo;',
                'prevLink' => '&laquo;',
                'lastLink' => 'Last &raquo;',
                'showCount' => false,
                'numLinks' => 1,
            );
            $pagination = new Pagination($pagConfig);

            if (count($school_list) > $limit) {
                $paging = $pagination->createLinks();
            } else {
                $paging = '';
            }
        } else {
            $paging = '';
        }
        return array(
            '#markup' => $paging,
        );
    }
}
