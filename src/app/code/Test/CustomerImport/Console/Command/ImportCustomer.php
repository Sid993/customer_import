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
namespace Test\CustomerImport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCustomer extends Command
{
    /**
     * @const string
     */
    public const PROFILE = 'profile';

    /**
     * @const string
     */
    public const SOURCE = 'source';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Test\CustomerImport\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var object
     */
    protected $fileType;

    /**
     * @var array
     */
    protected $profileTypes;

    /**
     * Initialize dependencies
     *
     * @param \Test\CustomerImport\Helper\Data $helper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $profileTypes
     * @return void
     */
    public function __construct(
        \Test\CustomerImport\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        $profileTypes = []
    ) {
        $this->helper        = $helper;
        $this->jsonHelper    = $jsonHelper;
        $this->profileTypes  = $profileTypes;
        $this->moduleManager = $moduleManager;
        
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::PROFILE,
                null,
                InputOption::VALUE_REQUIRED,
                InputOption::VALUE_OPTIONAL,
                'Profile'
            ),
            new InputOption(
                self::SOURCE,
                null,
                InputOption::VALUE_REQUIRED,
                InputOption::VALUE_OPTIONAL,
                'Source'
            )
        ];
        $this->setName('customerimport:import_customer')
            ->setDescription('Customer Import Custom Command')
            ->setDefinition($options);
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if ($this->moduleManager->isEnabled('Test_CustomerImport')) {
                
                $output->writeln('<info>Test_CustomerImport cmd execution started.</info>');

                $profileType = $input->getOption(self::PROFILE);
                
                if ($profileType == "") {
                    $output->writeln('<error>Invalid Profile Type. '.$profileType.'</error>');
                    return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
                }

                $profileTypes = $this->profileTypes['profile_type'] ?? [];
                $this->fileType = $profileTypes[$profileType] ?? "";

                $source = $input->getOption(self::SOURCE);

                $output->writeln('<info>Profile Type:</info>'.$profileType);

                if (!is_string($this->fileType) && !empty($source)) {
                    $output->writeln('<info>Processing Method:</info>'.$profileType);
                    $result = $this->fileType->importCustomer($source);
                    $output->writeln('<info>Result:</info>'.$this->jsonHelper->jsonEncode($result));
                } else {
                    $output->writeln('<error>Invalid Source '.$source.
                    ' OR profile.</error>');
                    return \Magento\Framework\Console\Cli::RETURN_FAILURE;
                }

                $output->writeln('<info>Test_CustomerImport cmd executed successfully.</info>');
            } else {
                $output->writeln('<error>Test_CustomerImport is disabled.</error>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        } 
        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
