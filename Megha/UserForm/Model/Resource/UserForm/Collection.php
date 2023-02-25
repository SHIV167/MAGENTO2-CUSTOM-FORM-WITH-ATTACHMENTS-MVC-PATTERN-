<?php

namespace Megha\UserForm\Model\Resource\UserForm;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'custom_user_collection';
    protected $_eventObject = 'userform_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Megha\UserForm\Model\UserForm',
            'Megha\UserForm\Model\Resource\UserForm'
        );
    }
}
