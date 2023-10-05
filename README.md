# Postcode Service Magento 2 extension

[![Latest Stable Version](https://img.shields.io/github/v/release/postcodeservice/postcode-magento2?style=for-the-badge&color=227cff)](https://github.com/postcodeservice/postcode-magento2/releases/latest)
![TIG Postcode Service 2.4.7 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.7beta1-%2300cf00?style=for-the-badge)
![TIG Postcode Service 2.4.6 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.6-%2300cf00?style=for-the-badge)
![TIG Postcode Service 2.4.5 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.5-%2300cf00?style=for-the-badge)
![TIG Postcode Service 2.4.4 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.4-%2300cf00?style=for-the-badge)
[![Total Extension downloads](https://img.shields.io/packagist/dt/tig/postcode-magento2?style=for-the-badge&color=227cff)](https://packagist.org/packages/tig/postcode-magento2/stats)

## Introduction

With the Postcode Service you can auto complete a partly given address and check its validity.

This code base is an extension (plugin) for the Adobe Magento platform using the Postcode Service
API https://api.postcodeservice.com. The extension is available
and maintained free or charge. However, there is a fee
associated with using the Postcode Service, which is integrated with the extension. The
exact cost depends on the amount of usage. You can find detailed pricing information on the website
at https://postcodeservice.com/#compare-packages

3 Reasons for using the Postcode Service Adobe Magento extension:

* Superfast performance due to specific code and design optimizations for Adobe
  Magento webshops
* Accurate and reliable data, ahead of the market
* Secure: Independent of logistics companies

## Installation

We strongly recommend that you use a staging environment for the installation, and to also make a
backup of your environment.

### Installation using composer (Recommended)

To install the extension login to your environment using SSH.  
Then navigate to the Magento 2 root directory and run the following commands in the same order as
described:

Enable maintenance mode:

```shell
php bin/magento maintenance:enable
```

Install the extension:

```shell
composer require tig/postcode-magento2
```

Enable the Postcode Service Magento 2 extension

```shell
php bin/magento module:enable TIG_Postcode
```

Update the Magento 2 environment:

```shell
php bin/magento setup:upgrade
```

---

When your Magento environment is running in production mode, you also need to run the following
commands:

Compile DI:

```shell
php bin/magento setup:di:compile
```

Deploy static content:

```shell
php bin/magento setup:static-content:deploy
```

---

Disable maintenance mode:

```shell
php bin/magento maintenance:disable

```

## How to update the extension

To update the Postcode Service Extension run the following commands:

```shell
composer update tig/postcode-magento2
php bin/magento setup:upgrade
```

## Alternatively, install the extension manually

- Download the extension directly
  from [github](https://github.com/postcodeservice/postcode-magento2) by clicking on *Code* and then
  *Download ZIP*.
- Create the directory *app/code/TIG/Postcode* (Case-sensitive)
- Extract the zip and upload the code into *app/code/TIG/Postcode*
- Enable the Postcode Service Magento 2 extension

```shell
php bin/magento module:enable TIG_Postcode
```

- Update the Magento 2 environment

```shell
php bin/magento setup:upgrade
```

## Configuration

After completing the installation process, the extension should be readily available in the backend
of your Magento webshop. From there, you can configure the extension according to your requirements:

1. Go to Stores -> Configuration -> Sales -> Postcode Service

2. Open the Configuration tab

3. Change the Modus from "Off" to "Test" or "Live"

4. Insert your Client ID and Api key (Secure Code). When you selected “Test” at the Modus, you can
   find test credentials below the fields - keep in mind there is a daily limit -.\
   To sign up for a paid account and get your Client ID and Api key, simply
   visit https://postcodeservice.com and subscribe.

5. In the 'Countries' section, select the country for which you wish to enable the Postcode Service.
6. Flush your cache

## Visual representation of the Postcode Service Magento extension during the checkout process

### For Dutch postcodes

<img src="https://postcodeservice.com/wp-content/uploads/2022/08/postcodeservice-magento-2-nl.gif"
width="50%" alt="postcode_service_magento2-nl>

### For Belgium postcodes

<img src="https://postcodeservice.com/wp-content/uploads/2022/08/postcodeservice-magento-2-be.gif" width="50%" alt="postcode_service_magento2-be">

## Further documentation

You can find the underlying Postcode Service API documentation
here: https://developers.postcodeservice.com

## Frequently Asked Questions

### Q: Which third party extensions are supported and compatible?

A: While our extension is compatible with many third-party checkout extensions, we do not provide
support for third-party checkout extensions from OneStepCheckout.com, Amasty, and MagePlaza.
Although there are numerous webshops that successfully use these extensions in combination with our
Postcode Service extension, we cannot guarantee their compatibility due to potential issues arising
from custom implementations. As a result, we are unable to offer support for any issues that may
arise from their use.

## Requirements

Adobe Magento Open Source (Community edition) or Adobe Commerce (Enterprise version).

## Version support

We follow the release support lines dates from
Adobe https://experienceleague.adobe.com/docs/commerce-operations/release/versions.html for the
version support of this extension.

## Technical Support with the extension

See https://postcodeservice.com/support/

## Release history

See https://github.com/postcodeservice/postcode-magento2/releases

## About

The Postcode Service is a trademark of the Total Internet Group B.V.
