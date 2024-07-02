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
namespace Test\CustomerImport\Model;

/**
 * ImportJson Model Class
 */
class ImportJson
{
    /**
     * @var \Test\CustomerImport\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * Initialize dependencies
     *
     * @param \Test\CustomerImport\Helper\Data $helper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @return void
     */
    public function __construct(
        \Test\CustomerImport\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
    ) {
        $this->helper      = $helper;
        $this->jsonHelper  = $jsonHelper;
        $this->readFactory = $readFactory;
    }

    /**
     * Import JSON file records
     *
     * @param string $source
     * @return array
     */
    public function importCustomer($source = "")
    {
        $result = [
            "success" => false,
            "message" => ""
        ];
        try {
            if (!empty($source)) {
                $sourcePath = explode("/", $source ?? "");
                $sourcePathDirCount = count($sourcePath);
                $sourcePath = str_replace($sourcePath[$sourcePathDirCount-1], '', $source);
    
                $directoryRead = $this->readFactory->create($sourcePath);
                $jsonData = $directoryRead->readFile($source);

                $data = $this->jsonHelper->jsonDecode($jsonData);
                if (!empty($data)) {
                    $result = $this->helper->saveRecords($data);
                } else {
                    $result["message"] = __("Json Records are empty.");
                }
            }
        } catch (\Exception $e) {
            $this->helper->logMessage("importJson method Error: ".$e->getMessage(), 'error');
        }
       
        return $result;
    }
}
