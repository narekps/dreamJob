<?php

namespace App\View\Helper;

use App\Entity\User as UserEntity;
use App\Entity\File as FileEntity;
use Zend\View\Helper\AbstractHelper;

/**
 * Class UserAvatar
 * @package App\View\Helper
 */
class UserAvatar extends AbstractHelper
{

    const AVATAR_DEFAULTS
        = [
            UserEntity::GENDER_EMPTY  => 'avatar/avatar.png',
            UserEntity::GENDER_MALE   => 'avatar/avatar.png', // Мужской
            UserEntity::GENDER_FEMALE => 'avatar/avatar.png', // Женский
        ];

    public function __invoke(UserEntity $user, string $size = null)
    {
        return $this->getUserAvatar($user, $size);
    }

    protected function getUserAvatar(UserEntity $user, string $size = null)
    {
        $url    = '/file/show/';
        $avatar = $user->getAvatar();
        if (!is_null($avatar)) {
            $url .= $avatar->getId();
            if (!empty($size)) {
                $url .= '?size=' . $size;
            }
        } else {
            $url .= '0?default=' . self::AVATAR_DEFAULTS[$user->getGender()];
        }

        return $url;
    }
}
