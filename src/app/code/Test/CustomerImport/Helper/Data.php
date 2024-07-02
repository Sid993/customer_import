<?php
/**
 * Test.
 *
 * @category  Test
 * @package   Test_CustomerImport
 * @author    Saurav Kumar
 * @copyright Test
 * @license   https://example.com/license.html
 */
namespace Test\CustomerImport\Helper;

/**
 * CustomerImport Data Helper Class
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const DEFAULT_PWD = "Testcustomer123";

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $store;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Initialize dependencies
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Store\Model\StoreManagerInterface $store
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @return void
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->store              = $store;
        $this->logger             = $logger;
        $this->encryptor          = $encryptor;
        $this->jsonHelper         = $jsonHelper;
        $this->customerFactory    = $customerFactory;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * Save Customer Records
     *
     * @param array $records
     * @return array
     */
    public function saveRecords($records)
    {
        $message = "";
        $totalRecords = count($records);
        $savedRecords = 0;
        $flag = false;
        try {
            $storeId = $this->store->getStore()->getId();
            $websiteId = $this->store->getStore($storeId)->getWebsiteId();

            foreach ($records as $index => $info) {
                $result = $this->saveCustomerInfo($info, $storeId, $websiteId);
                $savedRecords = $result ? ++$savedRecords : $savedRecords;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logMessage("saveRecords method Error:".$e->getMessage(), 'error');
        }

        $flag = ($savedRecords > 0) ? true : false;

        if ($totalRecords == $savedRecords) {
            $message = __("All Records Saved");
        } else {
            $message = __("Few Records aren't saved");
        }
        return [
            "message" => $message,
            "success" => $flag
        ];
    }

    /**
     * Save Customer
     *
     * @param array $info
     * @param int $storeId
     * @param int $websiteId
     * @return bool
     */
    public function saveCustomerInfo($info, $storeId = 0, $websiteId = 1)
    {
        try {
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);

            # Adding data for new customer
            $customer->setEmail($info["emailaddress"] ?? "");
            $customer->setFirstname($info["fname"] ?? "");
            $customer->setLastname($info["lname"] ?? "");
            $hashedPassword =  $this->encryptor->getHash(self::DEFAULT_PWD, true);

            #Save Customer
            $this->customerRepository->save($customer, $hashedPassword);

            return true;
        } catch (\Exception $e) {
            $this->logMessage("saveCustomerInfo Info Record:".$this->jsonHelper->jsonEncode($info));
            $this->logMessage("saveCustomerInfo method Error:".$e->getMessage(), 'error');
        }
        return false;
    }

    /**
     * Print message in log file
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    public function logMessage($message, $type = "info")
    {
        if ($type == "error") {
            $this->logger->error($message);
        } elseif ($type == "debug") {
            $this->logger->debug($message);
        } else {
            $this->logger->info($message);
        }
    }
}
