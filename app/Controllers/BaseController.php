<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest; // Tetap impor untuk type hint di metode initController jika diperlukan
use CodeIgniter\HTTP\IncomingRequest; // Tetap impor untuk type hint di metode initController jika diperlukan
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any properties as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the incoming Request object.
     * Didefinisikan di kelas induk CodeIgniter\Controller, tidak perlu redeklarasi di sini.
     * protected IncomingRequest|CLIRequest $request; // BARIS INI DIHAPUS/DIKOMENTARI
     */

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    // Pastikan helpers form, url, dan text ada di sini atau di-autoload di app/Config/Autoload.php
    protected $helpers = ['form', 'url', 'text'];

    /**
     * Constructor.
     *
     * @param RequestInterface  $requestInterface
     * @param ResponseInterface $responseInterface
     * @param LoggerInterface   $loggerInterface
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        // helper($this->helpers); // Baris ini opsional jika helpers sudah di-autoload di $helpers property di atas
    }
}