<?php

namespace Megha\UserForm\Controller\Result;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Megha\UserForm\Model\UserFormFactory;

class Index extends Action
{
    /**
     * @var Context
     */
    private $context;
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var UserFormFactory
     */
    private $userFormFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param UserFormFactory $userFormFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        UserFormFactory $userFormFactory
    ) {
        parent::__construct($context);
        $this->context          = $context;
        $this->pageFactory      = $pageFactory;
        $this->userFormFactory  = $userFormFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $data                   = $this->getRequest()->getParams();
        $page                   = $this->pageFactory->create();
        $block                  = $page->getLayout()->getBlock('userdetail');
        if (isset($data['id'])) {
            $modeldata          = $this->userFormFactory->create()->getCollection()->addFieldToFilter('id', $data['id'])->getData();
            if (count($modeldata)>0) {
                foreach ($modeldata[0] as $k => $v) {
                    $block->setData($k, $v);
                }
            }
        }
        return $page;
    }
}
