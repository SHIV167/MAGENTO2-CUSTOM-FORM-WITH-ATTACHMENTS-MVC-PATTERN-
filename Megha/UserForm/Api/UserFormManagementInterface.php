<?php


namespace Megha\UserForm\Api;


use Megha\UserForm\Api\Data\UserFormInterface;

interface UserFormManagementInterface
{

    /**
     * @return UserFormInterface
     */
    public function saveUser();

    /**
     * @return mixed
     */
    public function deleteUser();
}
