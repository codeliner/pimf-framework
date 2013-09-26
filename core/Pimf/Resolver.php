<?php
/**
 * Pimf
 *
 * PHP Version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://krsteski.de/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to gjero@krsteski.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * Resolves the user requests to controller and action.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Resolver
{
  /**
   * @var string
   */
  protected $controllerFilePath;

  /**
   * @var string
   */
  protected $controllerClassName;

  /**
   * @var string
   */
  protected $controllerRepositoryPath;

  /**
   * @var Pimf_Request
   */
  protected $request;

  /**
   * @param Pimf_Request $request
   * @param string $controllerRepositoryPath
   * @param string $prefix
   */
  public function __construct(Pimf_Request $request, $controllerRepositoryPath = '/Controller', $prefix = 'Pimf_')
  {
    $controllerName = $request->fromGet()->get('controller');

    $conf = Pimf_Registry::get('conf');

    if (Pimf_Environment::isCli() && $conf['environment'] == 'production') {
      $controllerName = $request->fromCli()->get('controller');
    }

    if (!$controllerName) {
      $controllerName =  $conf['app']['default_controller'];
    }

    $this->controllerRepositoryPath = $controllerRepositoryPath;
    $this->request                  = $request;
    $this->controllerClassName      = $prefix . 'Controller_';
    $this->controllerFilePath       = $this->controllerRepositoryPath . '/' . ucfirst($controllerName) . '.php';
  }

  /**
   * @return Pimf_Controller_Abstract
   * @throws Pimf_Resolver_Exception If no controller specified or no controller found at the repository.
   */
  public function process()
  {
    if (!file_exists($this->controllerFilePath)) {
      throw new Pimf_Resolver_Exception(
        'no controller found at the repository path; ' . $this->controllerFilePath
      );
    }

    $path       = str_replace($this->controllerRepositoryPath, '', $this->controllerFilePath);
    $name       = str_replace('/', $this->controllerClassName, $path);
    $controller = str_replace('.php', '', $name);

    if (!class_exists($controller)) {
      throw new Pimf_Resolver_Exception(
        'can not load class "'.$controller.'" from the repository'
      );
    }

    return new $controller($this->request);
  }
}
