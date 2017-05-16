<?php

namespace Drupal\hubspot\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Default controller for the hubspot module.
 */
class Controller extends ControllerBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Controller constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->getEditable('hubspot.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Gets response data and saves it in config.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Returns Hubspot Connection Response(api key values like access_token,
   *   refresh token, expire_in).
   */
  public function hubspotOauthConnect() {
    if (!empty($_GET['access_token']) && !empty($_GET['refresh_token']) && !empty($_GET['expires_in'])) {
      drupal_set_message($this->t('Successfully authenticated with Hubspot.'), 'status', FALSE);

      $this->config->set('hubspot_access_token', $_GET['access_token'])->save();
      $this->config->set('hubspot_refresh_token', $_GET['refresh_token'])->save();
      $this->config->set('hubspot_expires_in', ($_GET['expires_in'] + REQUEST_TIME))->save();
    }

    if (!empty($_GET['error']) && $_GET['error'] == "access_denied") {
      drupal_set_message($this->t('You denied the request for authentication with Hubspot. Please click the button again and
      choose the AUTHORIZE option.'), 'error', FALSE);
    }
    $redirect_url = Url::fromRoute('hubspot.admin_settings')->toString();
    $response = new RedirectResponse($redirect_url);
    $response->send();
    return $response;
  }

}
