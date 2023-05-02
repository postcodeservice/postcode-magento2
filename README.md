# Postcode Service Magento 2 extension
[![Latest Stable Version](https://img.shields.io/github/v/release/postcodeservice/postcode-magento2?style=for-the-badge&color=227cff)](https://github.com/postcodeservice/postcode-magento2/releases/latest)
![TIG Postcode Service 2.4.6 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.6-%2300cf00?style=for-the-badge)
![TIG Postcode Service 2.4.5 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.5-%2300cf00?style=for-the-badge)
![TIG Postcode Service 2.4.4 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.4-%2300cf00?style=for-the-badge)
[![Total Extension downloads](https://img.shields.io/packagist/dt/tig/postcode-magento2?style=for-the-badge&color=227cff)](https://packagist.org/packages/tig/postcode-magento2/stats)

## Installation
We strongly recommend that you use a staging environment for the installation, and to also make a backup of your environment.

### Installation using composer (Recommended)
To install the extension login to your environment using SSH.  
Then navigate to the Magento 2 root directory and run the following commands in the same order as described:

Enable maintenance mode:
~~~~shell
php bin/magento maintenance:enable
~~~~

Install the extension:
~~~~shell
composer require tig/postcode-magento2
~~~~

Enable the Postcode Service Magento 2 extension
~~~~shell
php bin/magento module:enable TIG_Postcode
~~~~

Update the Magento 2 environment:
~~~~shell
php bin/magento setup:upgrade
~~~~

When your Magento environment is running in production mode, you also need to run the following commands:

Compile DI:
~~~~shell
php bin/magento setup:di:compile
~~~~

Deploy static content:
~~~~shell
php bin/magento setup:static-content:deploy
~~~~

Disable maintenance mode:
~~~~shell
php bin/magento maintenance:disable
~~~~

## Update the extension
To update the Postcode Service Extension run the following commands:
~~~~shell
composer update tig/postcode-magento2
php bin/magento setup:upgrade
~~~~

## Alternatively, install the extension manually
1. Download the extension directly from [github](https://github.com/postcodeservice/postcode-magento2) by clicking on *Code* and then *Download ZIP*.
2. Create the directory *app/code/TIG/Postcode* (Case-sensitive)
3. Extract the zip and upload the code into *app/code/TIG/Postcode*
4. Enable the Postcode Service Magento 2 extension
~~~~shell
php bin/magento module:enable TIG_Postcode
~~~~

## Configuration
When the installation of the extension is finished, it should be available within the backend of the webshop where the plugin can be configured.
1. Go to Stores -> Configuration -> Sales -> Postcode Service NL/BE.
2. Open the Configuration tab.
3. Change the Modus from “Off” to “Test” or “Live”. In case you do not have a live (paid) account yet, you find test credentials at https://developers.postcodeservice.com (keep in mind there is a daily limit). You can subscribe to a paid account via https://postcodeservice.com.
4. Insert the Client ID and Api key (when you selected “Test” at the previous step, you can find test credentials below the fields).
5. Save the configuration.
6. Flush your cache.

## Examples
### Dutch Postcode Service within the Magento 2 checkout
![Postcode Service Magento 2 Checkout NL](https://postcodeservice.com/wp-content/uploads/2022/08/postcodeservice-magento-2-nl.gif "Postcode Service Magento 2 Checkout NL")

### Belgium Postcode Service within the Magento 2 checkout
![Postcode Service Magento 2 Checkout BE](https://postcodeservice.com/wp-content/uploads/2022/08/postcodeservice-magento-2-be.gif "Postcode Service Magento 2 Checkout NL")

## Further documentation
You can find the underlying Postcode Service API documentation here: https://developers.postcodeservice.com

## Frequently Asked Questions
Q: What are the costs for using the Postcode Service?\
A: The Adobe Magento extension can be used free of charge. The fee for using the Postcode Service can be found at our website https://postcodeservice.com      
\
Q: Which third party extensions are supported and compatible?\
A: At this time, we do not support third-party checkout extensions, including OneStepCheckout.com, Amasty, and MagePlaza. While we understand the importance of these third party extensions for some webshops, we cannot guarantee their compatibility with our extension and therefore cannot provide support for any issues that may arise as a result of their use. We have received reports from some users about compatibility issues with OneStepCheckout and MagePlaza, but this experience may vary from agency to agency, depending on the custom implementations. However, we are constantly working to improve our extension's functionality and compatibility, and we may revisit this decision in the future.

## Requirements
Adobe Magento Open Source (Community edition) or Adobe Commerce (Enterprise version).

## Version support
We follow the release support lines dates from Adobe https://experienceleague.adobe.com/docs/commerce-operations/release/versions.html for the version support of this extension.

## Release history
See https://github.com/postcodeservice/postcode-magento2/releases
