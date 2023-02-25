<?php

namespace Megha\UserForm\Controller\Index;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Megha\UserForm\Model\UserFormFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var UserFormFactory
     */
    private $userFormFactory;
    /**
     * @var Curl
     */
    private $curl;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Context
     */
    private $context;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param UserFormFactory $userFormFactory
     * @param Curl $curl
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        UserFormFactory $userFormFactory,
        Curl $curl,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->userFormFactory = $userFormFactory;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
        $this->context = $context;
        $this->messageManager = $messageManager;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     * @throws Exception
     */
    public function execute()
    {
        $data           = $this->getRequest()->getParams();
        $page           = $this->pageFactory->create();
        $block          = $page->getLayout()->getBlock('userform');
        if (isset($data['saveuser'])) {
            if ($this->checkUser($data['email'])) {
                $this->setReturnData($block, $data);
                $this->messageManager->addError("This email already added, please try different one...!");
                return $page;
            } else {
                $res    = $this->saveUser();
                if ($res['status'] == 1) {
                    $this->_redirect('arun/result?id=' . $res['id']);
                } else {
                    $this->messageManager->addError($res['error']);
                    $this->setReturnData($block, $data);
                    return $page;
                }
            }
        } else {
            return $page;
        }
    }

    /**
     * @param $block
     * @param $data
     */
    public function setReturnData($block, $data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $block->setData($k, $v);
            }
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function checkUser($email)
    {
        $modelCollection = $this->userFormFactory->create()->getCollection()->addFieldToFilter('email', $email);
        if (count($modelCollection) > 0) {
            foreach ($modelCollection as $model) {
                if ($model->getData('id') != null) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function saveUser()
    {
        $return     = [];
        $data       = $this->getRequest()->getParams();
        $model      = $this->userFormFactory->create();
        $passdata   = [
            "first_name" => $data['first_name'],
            "last_name"  => $data['last_name'],
            "email"      => $data['email'],
            "mobile"     => $data['mobile'],
            "dob"        => $data['dob']
        ];
        $model->addData($passdata);
        try {
            $saveData                       = $model->save();
            if ($saveData) {
                $return['error'] 		    = "";
                $return['status'] 			= 1;
                $return['data']             = $passdata;
                $return['id'] 				= $model->getData('id');
            } else {
                $return['error'] 			= "Something went wrong, please try again..!";
                $return['status'] 			= 0;
                $return['id'] 				= 0;
                $return['data']             = [];
            }
        } catch (LocalizedException $e) {
            $return['error'] 			    = $e->getMessage();
            $return['status'] 			    = 0;
            $return['data']                 = [];
        }
        return $return;
    }

    /**
     * @param $data
     * @throws NoSuchEntityException
     */
    public function postCurl($data)
    {
        $params         = [
            "first_name" => $data['first_name'],
            "last_name"  => $data['last_name'],
            "email"      => $data['email'],
            "mobile"     => $data['mobile'],
            "dob"        => $data['dob']
        ];
        $base_url       = $this->storeManager->getStore()->getBaseUrl();
        try {
            $url        = $base_url . "rest/V1/UserFormManagement/saveUser";
            $this->curl->addHeader("Content-Type", "form-data");
            $this->curl->post($url, $params);
            $response   = $this->curl->getBody();
            print_r($response);
        } catch (Exception $e) {
            print_r($e);
        }
    }
}
