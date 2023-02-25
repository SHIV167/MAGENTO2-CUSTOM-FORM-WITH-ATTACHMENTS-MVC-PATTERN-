<?php

namespace Megha\UserForm\Model\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Rest\Request;
use Megha\UserForm\Api\UserFormManagementInterface;
use Megha\UserForm\Model\UserFormFactory;

class UserFormManagement implements UserFormManagementInterface
{
    /**
     * @var UserFormFactory
     */
    private $userFormFactory;
    /**
     * @var Request
     */
    private $request;

    /**
     * UserFormManagement constructor.
     * @param UserFormFactory $userFormFactory
     * @param Request $request
     */
    public function __construct(
        UserFormFactory $userFormFactory,
        Request $request
    ) {
        $this->userFormFactory = $userFormFactory;
        $this->request = $request;
    }

    /**
     * @return \Megha\UserForm\Api\Data\UserFormInterface|void
     * @throws \Exception
     */
    public function saveUser()
    {
        $response   = [];
        $data       = $this->request->getRequestData();
        if (isset($data['id'])) {
            $model      = $this->userFormFactory->create()->load($data['id']);
        } else {
            $model      = $this->userFormFactory->create();
        }
        $passdata   = [
            "first_name" => $data['first_name'],
            "last_name"  => $data['last_name'],
            "email"      => $data['email'],
            "mobile"     => $data['mobile'],
            "dob"        => $data['dob']
        ];
        if ($this->checkUser($passdata['email']) && !isset($data['id'])) {
            $response['error'] 			    = "This email already added, please try different one...!";
            $response['status'] 			= 0;
            $response['id'] 				= 0;
        } else {
            $model->addData($passdata);
            try {
                $saveData                           = $model->save();
                if ($saveData) {
                    $response['error'] 			    = "";
                    $response['status'] 			= 1;
                    $response['id'] 				= $model->getData('id');
                    $response['data']               = $passdata;
                } else {
                    $response['error'] 			    = "Something went wronng, pleasee try again..!";
                    $response['status'] 			= 0;
                    $response['id'] 				= 0;
                    $response['data']               = [];
                }
            } catch (LocalizedException $e) {
                $response['error'] 			        = $e->getMessage();
                $response['status'] 			    = 0;
                $response['id'] 				    = 0;
                $response['data']                   = [];
            }
        }
        echo json_encode($response);
        exit();
    }

    /**
     * @param $email
     * @return bool
     */
    public function checkUser($email)
    {
        $modelCollection                        = $this->userFormFactory->create()->getCollection()->addFieldToFilter('email', $email)->getData();
        if (count($modelCollection)) {
            if (isset($modelCollection[0]['email'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return false|\Megha\UserForm\Api\Data\UserFormInterface|string|void
     * @throws \Exception
     */
    public function deleteUser()
    {
        $data                                   = $this->request->getRequestData();
        $model                                  = $this->userFormFactory->create()->load($data['id']);
        if ($model->getId() != null) {
            $model->delete();
            $response['msg'] 			        = "Record delete successfully..!";
            $response['status'] 			    = 1;
        } else {
            $response['msg'] 			        = "Record not found..!";
            $response['status'] 			    = 0;
        }
        echo json_encode($response);
        exit();
    }
}
