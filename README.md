<p align="center"><img src="https://postcodeservice.com/wp-content/uploads/2021/01/postcode-service-logo__Logo-color.svg" width="300px" /></p>

# Postcode Service Magento 2
[![Latest Stable Version](https://img.shields.io/github/v/release/tig-nl/postcode-magento2?style=for-the-badge&color=227cff)](https://github.com/tig-nl/postcode-magento2/releases/latest)
![TIG Postcode Service 2.3.7 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.3.7-%2300cf00?style=for-the-badge)
![TIG Postcode Service 2.4.5 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.5-%2300cf00?style=for-the-badge)
[![Total Extension downloads](https://img.shields.io/packagist/dt/tig/postcode-magento2?style=for-the-badge&color=227cff)](https://packagist.org/packages/tig-nl/postcode-magento2/stats)
![Build Status](https://img.shields.io/travis/tig-nl/postcode-magento2/master?style=for-the-badge)


## Requirements
- Magento version 2.4.5, 2.4.4, 2.3.7 or 2.3.6
- PHP 7.3+

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

## Update
To update the Postcode Service Extension run the following commands:
~~~~shell
composer update tig/postcode-magento2
php bin/magento setup:upgrade
~~~~

## Examples

### Dutch Postcode Service within the Magento 2 checkout
![Postcode Service Magento 2 Checkout NL](https://postcodeservice.com/wp-content/uploads/2022/08/postcodeservice-magento-2-nl.gif "Postcode Service Magento 2 Checkout NL")

### Belgium Postcode Service within the Magento 2 checkout
![Postcode Service Magento 2 Checkout BE](https://postcodeservice.com/wp-content/uploads/2022/08/postcodeservice-magento-2-be.gif "Postcode Service Magento 2 Checkout NL")

## Documentation
For further installation guidance
https://developers.postcodeservice.com/
