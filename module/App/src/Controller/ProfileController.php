<?php

namespace App\Controller;

use App\Form\SaveResult;
use App\GoogleMaps;
use Imagine\Image\Box;
use Imagine\Image\Palette\CMYK;
use Imagine\Image\Point;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use App\Entity\User as UserEntity;
use App\Entity\UserContact as UserContactEntity;
use App\Entity\File as FileEntity;
use App\Entity\FileSize as FileSizeEntity;

class ProfileController extends BaseController
{

    const SECTION_MAIN     = 'main';
    const SECTION_CONTACTS = 'contacts';
    const SECTION_DEFAULT  = self::SECTION_MAIN;
    const SECTIONS_ALLOWED = [self::SECTION_DEFAULT, self::SECTION_CONTACTS];
    const SECTION_NAMES
                           = [
            self::SECTION_MAIN     => 'Basic info',
            self::SECTION_CONTACTS => 'Contact info',
        ];

    /**
     * Главная страница профиля
     *
     * @return mixed
     */
    public function indexAction()
    {
        if (!$this->zfbAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfbuser/authentication');
        }

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (empty($user->getSurname())) {
            return $this->redirect()->toRoute('profile', ['action' => 'edit', 'section' => 'main']);
        }

        return $this->forward()->dispatch(\ZfbUser\Controller\IndexController::class, ['action' => 'index']);
    }

    /**
     * Общий метод редактирование профиля
     *
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        if (!$this->zfbAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfbuser');
        }

        $section = $this->params()->fromRoute('section', self::SECTION_DEFAULT);
        if (!in_array($section, self::SECTIONS_ALLOWED)) {
            $section = self::SECTION_DEFAULT;
        }

        switch ($section) {
            case self::SECTION_CONTACTS:
                return $this->editContactsSection();
            break;
            case self::SECTION_MAIN:
            default:
                return $this->editMainSection();
            break;
        }
    }

    /**
     * Редактирование основной информации
     *
     * @return array|\Zend\Http\Response|ViewModel
     */
    protected function editMainSection()
    {
        /** @var UserEntity $user */
        $user = $this->getEntityManager()->find(UserEntity::class, $this->zfbAuthentication()->getIdentity()->getId());
        $form = $this->buildEntityForm(UserEntity::class, null, $user);

        if (!$this->getRequest()->isPost() && !empty($user->getAddress())) {
            // @TODO Сделать кеширование
            /** @var GoogleMaps $googleMap */
            $googleMap = $this->getEvent()->getApplication()->getServiceManager()->get('GoogleMaps');
            $geoRes    = $googleMap->reverseGeocode($user->getAddress());
            $form->get('addressRaw')->setValue($geoRes['results'][0]['formatted_address']);
        }

        $view = new ViewModel([
            'section' => self::SECTION_MAIN,
            'user'    => $user,
            'form'    => $form,
        ]);

        $prg = $this->prg($this->url()->fromRoute('profile', ['action' => 'edit', 'section' => self::SECTION_MAIN]), true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            return $prg;
        }
        if ($prg === false) {
            return $view;
        }
        //request is post
        $form       = $this->buildEntityForm(UserEntity::class, $prg);
        $saveResult = new SaveResult($form->isValid());
        if ($form->isValid()) {
            $data = $form->getData();
            $user->exchangeArray($data);
            $this->getEntityManager()->merge($user);
            $this->getEntityManager()->flush();

            $saveResult->setTitle('Changes saved.');
            $saveResult->setMessage('Your profile has been successfully updated.');
        } else {
            $saveResult->setTitle('Failed to save the changes.');
            $saveResult->setMessage('Check the correctness of the data entered.');
        }
        $view->setVariable('saveResult', $saveResult);

        $form->bind($user);
        $view->setVariable('form', $form);

        return $view;
    }

    /**
     * Редактирование контактной информации
     *
     * @return array|\Zend\Http\Response|ViewModel
     */
    protected function editContactsSection()
    {
        /** @var UserEntity $user */
        $user    = $this->getEntityManager()->find(UserEntity::class, $this->zfbAuthentication()->getIdentity()->getId());
        $contact = $user->getContact();
        if (is_null($contact)) {
            $contact = new UserContactEntity();
            $user->setContact($contact);
            $contact->setUser($user);
            $this->getEntityManager()->persist($contact);
        }
        $view = new ViewModel([
            'section' => self::SECTION_CONTACTS,
            'user'    => $user,
            'contact' => $contact,
            'form'    => $this->buildEntityForm(UserContactEntity::class, null, $user->getContact()),
        ]);

        $prg = $this->prg($this->url()->fromRoute('profile', ['action' => 'edit', 'section' => self::SECTION_CONTACTS]), true);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            return $prg;
        }
        if ($prg === false) {
            return $view;
        }
        //request is post
        $form       = $this->buildEntityForm(UserContactEntity::class, $prg);
        $saveResult = new SaveResult($form->isValid());
        if ($form->isValid()) {
            $data = $form->getData();
            $contact->exchangeArray($data);
            $this->getEntityManager()->merge($contact);
            $this->getEntityManager()->flush();

            $saveResult->setTitle('Changes saved.');
            $saveResult->setMessage('Your profile has been successfully updated.');
        } else {
            $saveResult->setTitle('Failed to save the changes.');
            $saveResult->setMessage('Check the correctness of the data entered.');
        }
        $view->setVariable('saveResult', $saveResult);

        if ($user->getContact()) {
            $form->bind($user->getContact());
        }
        $view->setVariable('form', $form);

        return $view;
    }

    /**
     * @return JsonModel
     */
    public function saveAvatarAction()
    {
        $json       = new JsonModel(['success' => false]);
        $avatarData = json_decode($this->params()->fromPost('avatar_data'), true);

        /** @var \BsbFlysystem\Service\FilesystemManager $fsm */
        $fsm = $this->getEvent()->getApplication()->getServiceManager()->get(\BsbFlysystem\Service\FilesystemManager::class);
        /** @var \League\Flysystem\Filesystem $fs */
        $fs = $fsm->get('uploads');
        $em = $this->getEntityManager();

        /** @var UserEntity $user */
        $user     = $this->zfbAuthentication()->getIdentity();
        $folder   = 'avatar' . DIRECTORY_SEPARATOR;
        $name     = 'avatar_' . $user->getId();
        $location = $folder . DIRECTORY_SEPARATOR . $name;

        try {
            $avatar = $user->getAvatar();
            if ($avatar) {
                if ($fs->has($avatar->getFolder() . $avatar->getName())) {
                    $fs->delete($avatar->getFolder() . $avatar->getName());
                }
                foreach ($avatar->getSizes() as $size) {
                    $em->remove($size);
                }
            } else {
                $avatar = new FileEntity();
            }

            $filter = new \BsbFlysystem\Filter\File\RenameUpload([
                'target'               => $location,
                'overwrite'            => true,
                'randomize'            => false,
                'use_upload_name'      => false,
                'use_upload_extension' => true,
                'filesystem'           => $fs
            ]);
            $file   = $filter->filter($_FILES['avatar_file']);

            $avatar->setFolder($folder)->setName(pathinfo($file['tmp_name'])['basename']);
            $user->setAvatar($avatar);
            $em->persist($avatar);

            $fullPath  = $fs->getAdapter()->applyPathPrefix($file['tmp_name']);
            $extension = pathinfo($fullPath)['extension'];
            $imagine   = new \Imagine\Imagick\Imagine();

            foreach (UserEntity::AVATAR_SIZES as $sizeKey => $size) {
                $sizeName = $name . "_{$size['w']}x{$size['h']}." . $extension;
                $sizePath = $fs->getAdapter()->applyPathPrefix($folder . $sizeName);
                $img      = $imagine->open($fullPath);
                $img->crop(new Point($avatarData['x'], $avatarData['y']), new Box($avatarData['width'], $avatarData['height']));
                $img->resize(new Box($size['w'], $size['h']));
                $img->save($sizePath);
                $fileSize = new FileSizeEntity();
                $fileSize->setWidth($size['w'])->setHeight($size['h'])->setFile($avatar)->setName($sizeName)->setAlias($sizeKey);
                $em->persist($fileSize);
            }

            $em->flush();

            $json->setVariable('success', true);
        } catch (\Exception $ex) {
            $json->setVariable('error', $ex->getMessage());
            if ($fs->has($location)) {
                $fs->delete($location);
            }
        }

        return $json;
    }
}
