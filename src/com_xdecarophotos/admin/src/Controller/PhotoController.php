<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;

final class PhotoController extends FormController
{
    public function upload(): void
    {
        Session::checkToken('post') or jexit('JINVALID_TOKEN');
        $user = $this->app->getIdentity();
        if (!$user->authorise('core.create','com_xdecarophotos')) throw new \RuntimeException('Not authorised.',403);
        // The model/service layer receives the upload; callers never choose physical paths.
    }
}
