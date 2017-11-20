<?php
namespace TwentyCI\Authentication\Observer;

class CheckLoginPersistentObserver implements \Magento\Framework\Event\ObserverInterface
{

	protected $redirect;
    protected $helperBackend;

	/**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Backend\Helper\Data $helperBackend
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->helperBackend = $helperBackend;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $areaFrontname = $this->helperBackend->getAreaFrontName();

        $moduleName = $observer->getEvent()->getRequest()->getModuleName();
        $actionName = $observer->getEvent()->getRequest()->getActionName();
        $controller = $observer->getEvent()->getRequest()->getControllerName();
    
        $openActions = array(
            'create',
            'createpost',
            'login',
            'loginpost',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        if($moduleName != $areaFrontname){
            if ($controller == 'account' && in_array($actionName, $openActions)) {
                return $this; //if in allowed actions do nothing.
            }
            if(!$this->_customerSession->isLoggedIn()) {
                $this->redirect->redirect($observer->getControllerAction()->getResponse(), 'customer/account/login');
            }
        }else{
            return $this;
        }
        
    }
}