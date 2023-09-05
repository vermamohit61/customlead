<?php

namespace Drupal\parent_login_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\user\Entity\User;

/**
 * Provides a 'My Account' Block.
 *
 * @Block(
 *   id = "myaccount_form_block",
 *   admin_label = @Translation("My Account Block"),
 *   category = @Translation("My Account Block Category"),
 * )
 */
class MyAccountBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get current loggedin user id
    $user_id = \Drupal::currentUser()->id();
    $login_link = '';
    $register_link = '';
    // Get current theme
    $current_theme = \Drupal::service('theme.manager')->getActiveTheme()->getName();
    // Load user object
    $user = User::load($user_id);
    $result = array();
    // Prepare user login block html
    if ($user_id > 0 && $current_theme != 'adminimal_theme') {
      // Prepare dashboard link
      $profile_link = Link::fromTextAndUrl(t('My Profile'), Url::fromUri('internal:/user/'.$user_id.'/edit'))->toString();
      $logout_link = Link::fromTextAndUrl(t('LOG OUT'), Url::fromRoute('user.logout'))->toString();
      $result['login_status'] = 1;
      $result['user_f_name'] = $user->field_parent_name->value;
      $result['profile_link'] = $profile_link;
      $result['logout_link'] = $logout_link;
    } else {
      $result['login_status'] = '';
      $login_link = Link::fromTextAndUrl(t('Login/Register Now'), Url::fromRoute('parent_login_registration.registerform'))->toString();
      $result['register_link'] = $login_link;
    }
    return array(
      '#theme' => 'user_custom_login_block',
      '#data' => $result
    );
  }

}