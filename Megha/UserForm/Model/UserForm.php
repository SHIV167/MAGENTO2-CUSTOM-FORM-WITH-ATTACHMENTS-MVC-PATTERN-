<?php

namespace Megha\UserForm\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class UserForm extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'custom_user';

    protected $_cacheTag = 'custom_user';

    protected $_eventPrefix = 'custom_user';

    protected function _construct()
    {
        $this->_init('Megha\UserForm\Model\Resource\UserForm');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
