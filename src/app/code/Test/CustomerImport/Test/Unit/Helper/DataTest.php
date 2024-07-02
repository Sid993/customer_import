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
namespace Test\CustomerImport\Test\Unit\Helper;

use Psr\Log\LoggerInterface;
use Test\CustomerImport\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Context|MockObject
     */
    protected $context;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory|MockObject
     */
    protected $customerFactory;

    /**
     * @var \Psr\Log\LoggerInterface|MockObject
     */
    protected $logger;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $store;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface|MockObject
     */
    protected $encryptor;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface|MockObject
     */
    protected $customer;

    /**
     * @var \Magento\Framework\Json\Helper\Data|MockObject
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|MockObject
     */
    protected $customerRepository;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->mockContext();
        $this->mockData();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMockForAbstractClass();

        $this->encryptor = $this->getMockBuilder(EncryptorInterface::class)
            ->getMockForAbstractClass();

        $this->jsonHelper = $this->getMockBuilder(\Magento\Framework\Json\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerRepository = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->store = $this->getMockForAbstractClass(StoreManagerInterface::class);

        /* Mock Class Object With Constructor Args*/
        $this->helper = new Data(
            $this->logger,
            $this->context,
            $this->jsonHelper,
            $this->store,
            $this->encryptor,
            $this->customerFactory,
            $this->customerRepository
        );
    }

    protected function mockContext()
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockData()
    {
        $this->customerFactory = $this->getMockBuilder(CustomerInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
    }

    /**
     * Test saveCustomerInfo function
     *
     * @param array $info
     * @param int   $storeId
     * @param int   $websiteId
     * @dataProvider getCustomerData
     */
    public function testSaveCustomerInfo($info, $storeId, $websiteId)
    {
        $this->customer = $this->getMockBuilder(
            \Magento\Customer\Api\Data\CustomerInterface::class
        )->getMockForAbstractClass();

        $this->customerFactory->expects(
            $this->any()
        )->method(
            'create'
        )->willReturn(
            $this->customer
        );

        $this->assertTrue($this->helper->saveCustomerInfo($info, $storeId, $websiteId));
    }

    /**
     * Test saveCustomerInfo function
     *
     * @param array $data
     * @param mixed $message
     * @param bool $success
     * @dataProvider getSaveRecordsData
     */
    public function testSaveRecords($data, $message, $success)
    {
        $result = [
            "message" => $message,
            "success" => $success
        ];

        $storeInterface = $this->getMockBuilder(
            \Magento\Store\Api\Data\StoreInterface::class
        )->getMockForAbstractClass();

        $this->store->expects(
            $this->any()
        )->method(
            'getStore'
        )->willReturn(
            $storeInterface
        );

        $this->customer = $this->getMockBuilder(
            \Magento\Customer\Api\Data\CustomerInterface::class
        )->getMockForAbstractClass();

        $this->customerFactory->expects(
            $this->any()
        )->method(
            'create'
        )->willReturn(
            $this->customer
        );
        
        $this->assertEquals($this->helper->saveRecords($data), $result);
    }

    /**
     * Data provider for testSaveCustomerInfo
     * @return array
     */
    public function getCustomerData()
    {
        $result = [
            [
                "info" => [
                    "emailaddress" => "sauravsidharth993@gmail.com",
                    "fname" => "Saurav",
                    "lname" => "Kumar"
                ],
                "storeId" => 0,
                "websiteId" => 1
            ],
            [
                "info" => [
                    "emailaddress" => "",
                    "fname" => "Saurav",
                    "lname" => "Kumar"
                ],
                "storeId" => 0,
                "websiteId" => 1
            ]
        ];

        return $result;
    }

    /**
     * Data provider for getSaveRecordsData
     * @return array
     */
    public function getSaveRecordsData()
    {
        $result = [
            [
                [
                    "info" => [
                        "emailaddress" => "sauravsidharth993@gmail.com",
                        "fname" => "Saurav",
                        "lname" => "Kumar"
                    ]
                ],
                "message" => __("All Records Saved"),
                "success" => 1
            ]
        ];

        return $result;
    }
}
