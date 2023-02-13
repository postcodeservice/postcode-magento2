## Installation
We strongly recommend that you use a Staging Environment for the installation, and to also make a backup of your environment.

### Installation using composer (recommended)
To install the extension login to your environment using SSH. Then navigate to the Magento 2 root directory and run the following commands in the same order as described:

Enable maintenance mode:
~~~~shell
php bin/magento maintenance:enable
~~~~

1. Install the extension:
~~~~shell
composer require tig/postcode-magento2
~~~~

2. Enable the Postcode Service Magento 2 extension
~~~~shell
php bin/magento module:enable TIG_Postcode
~~~~

3. Update the Magento 2 environment:
~~~~shell
php bin/magento setup:upgrade
~~~~

When your Magento environment is running in production mode, you also need to run the following comands:

4. Compile DI:
~~~~shell
php bin/magento setup:di:compile
~~~~

5. Deploy static content:
~~~~shell
php bin/magento setup:static-content:deploy
~~~~

6. Disable maintenance mode:
~~~~shell
php bin/magento maintenance:disable
~~~~

### Installation manually
1. Download the extension directly from [github](https://github.com/tig-nl/postcode-magento2) by clicking on *Code* and then *Download ZIP*.
2. Create the directory *app/code/TIG/Postcode* (Case-sensitive)
3. Extract the zip and upload the code into *app/code/TIG/Postcode*
4. Enable the Postcode Service Magento 2 extension
~~~~shell
php bin/magento module:enable TIG_Postcode
~~~~

5. Update the Magento 2 environment:
~~~~shell
php bin/magento setup:upgrade
~~~~

