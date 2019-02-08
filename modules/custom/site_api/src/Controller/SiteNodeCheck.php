<?php
/**
 * @file
 * custom menu to check siteapikey and node id (/nodecheck/{key}/{nid})
 */
namespace Drupal\site_api\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * Provides route responses for the Example module.
 */
class SiteNodeCheck extends ControllerBase {
  public function node_json($nid) {
    $output = array(
      'status' => false,
      'data' => '',
    );
    $node = \Drupal\node\Entity\Node::load($nid);
    if(!empty($node)) {
      // Make sure to enable Serialization module.
      $serializer = \Drupal::service('serializer');
      $data = $serializer->serialize($node, 'json', ['plugin_id' => 'entity']);
      $output['data'] = $data;
      $output['status'] = true;
    }
    else {
      $output['data'] = 'Please provide Node id in URL to see the result';
      $output['status'] = false;
    }
    return new JsonResponse($output);
  }

/**
  * Checks access for this controller.
  */
public function pageaccess($api_key, $nid) {
  $siteapikey = \Drupal::config('site.api')->get('siteapikey');
  $node = \Drupal\node\Entity\Node::load($nid);
  if (($api_key == $siteapikey) && (!empty($node) && $node->bundle() == 'page')) {  
    return AccessResult::allowed();
  }
  else {
    // Return 403 Access Denied page.
    return AccessResult::forbidden();
  }
}

}
