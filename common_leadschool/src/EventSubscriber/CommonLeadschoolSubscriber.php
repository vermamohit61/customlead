<?php

namespace Drupal\common_leadschool\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;


/**
 * Redirect .html pages to corresponding Node page.
 */
class CommonLeadschoolSubscriber implements EventSubscriberInterface
{

/** @var int */
    private $redirectCode = 301;

/**
 * Redirect pattern based url
 * @param GetResponseEvent $event
 */
    public function customRedirection(GetResponseEvent $event)
    {

      $request = \Drupal::request();
      $requestUrl = $request->server->get('REQUEST_URI', null);
      \Drupal::request()->query->set('62', 'class9');
      //check role
      $roles = \Drupal::currentUser()->getRoles();
      $route_match = \Drupal::service('current_route_match');
      $node = $route_match->getParameter('node');
      $front_page = \Drupal::service('path.matcher')->isFrontPage();
      if($node && $node->bundle() == 'marketing_management' || $front_page == '1') {
        $route_match->getRouteObject()->setOption('_no_big_pipe', TRUE);
      }
      /**
      * Here i am redirecting the listing pages as per role.
      * Here you can implement your logic and search the URL in the DB
      * and redirect them on the respective page.
      */
      $current_path = \Drupal::service('path.current')->getPath();
      $user_id = \Drupal::currentUser()->id();
      $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      $username = $account->get('name')->value;
     
      if ((($current_path == '/user') || ($current_path == '/user/'.$user_id)) && !empty($user_id)) {        
        $this->redirect_to_page('/'.$username);
      }
      
      if (($requestUrl == '/') && (in_array('marketing_admin', $roles))) {
        $this->redirect_to_page('/admin/marketing-page');
      }
      if (($requestUrl == '/') && (in_array('content_admin', $roles))) {
        $this->redirect_to_page('/admin/banners-listing');
      }
      if (($requestUrl == '/') && (in_array('site_admin', $roles))) {
        $this->redirect_to_page('/admin-users-listing');
      }
     
    }
    
/**
 * set header
 * @param FilterResponseEvent $event
 */
    public function onRespond(FilterResponseEvent $event) {
      $response = $event->getResponse();
      $response->headers->set('Cache-Control', 'no-store, must-revalidate, post-check=0, pre-check=0');
    }

/**
 * Listen to kernel.request events and call customRedirection.
 * {@inheritdoc}
 * @return array Event names to listen to (key) and methods to call (value)
 */
    public static function getSubscribedEvents()
    {
      $events[KernelEvents::REQUEST][] = array('customRedirection');
      $events[KernelEvents::RESPONSE][] = ['onRespond'];
      return $events;
    }
/**
 * takes path as parameter and redirect .
 */

    public function redirect_to_page($path)
    {
      $response = new RedirectResponse($path, $this->redirectCode);
      $response->send();
      exit(0);
    }
}
