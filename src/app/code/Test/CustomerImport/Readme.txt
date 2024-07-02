#Installation

Magento2 Test CustomerImport module installation is very easy, please follow the steps for installation-

1. Unzip the respective extension zip and create Test(vendor) then CustomerImport(module) name folder inside your magento/app/code/ directory and then move all module's files into magento root directory Magento2/app/code/Test/CustomerImport/ folder.

Run Following Command via terminal
-----------------------------------
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy

2. Flush the cache and reindex all.

now module is properly installed

#User Guide

COMMAND: php bin/magento customerimport:import_customer --profile="<PROFILE-TYPE>" --source="<SOURCE-FILE-PATH>"

[A] To import customers via CSV file:

Example: php bin/magento customerimport:import_customer --profile="csv" --source="/home/users/saurav.kumar/Documents/sample.csv"

[B] To import customers via JSON file:

Example: php bin/magento customerimport:import_customer --profile="json" --source="/home/users/saurav.kumar/Documents/sample.json"

COMMAND For Unit Test: vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Test/CustomerImport/Test/Unit/
