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
 * ImportCsv Model Class
 */
class ImportCsv
{
    /**
     * @var \Test\CustomerImport\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileSystem;

    /**
     * Initialize dependencies
     *
     * @param \Test\CustomerImport\Helper\Data $helper
     * @param \Magento\Framework\Filesystem\Driver\File $fileSystem
     * @return void
     */
    public function __construct(
        \Test\CustomerImport\Helper\Data $helper,
        \Magento\Framework\Filesystem\Driver\File $fileSystem
    ) {
        $this->helper     = $helper;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Import CSV file records
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
                $fileData = $this->fileSystem->fileOpen(
                    $source,
                    'r'
                );
                $rowData = [];

                $i = 0;
                while ($row = $this->fileSystem->fileGetCsv($fileData)) {
                    if ($i && (!empty($row[0]) && !empty($row[1]) && !empty($row[2]))) {
                        //Get CSV file row data
                        $rowData[] = [
                            'fname' => $row[0] ?? "",
                            'lname'  => $row[1] ?? "",
                            'emailaddress' => $row[2] ?? "",
                        ];
                    }
                    $i++;
                }

                if (!empty($rowData)) {
                    $result = $this->helper->saveRecords($rowData);
                } else {
                    $result["message"] = __("CSV Records are empty.");
                }
            }
        } catch (\Exception $e) {
            $this->helper->logMessage("importJson method Error: ".$e->getMessage(), 'error');
        }
        return $result;
    }
}
