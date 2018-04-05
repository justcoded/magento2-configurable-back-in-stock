## Purpose

Default product alerts in Magento 2 work only for the whole configurable products. This module allows customers to select specific configurations to get notified about.

## Description

A configurable product has a “Can’t find your configuration?” link that opens a product alert popup and will be included at the end of `product.info.extrahint` container by default.

![alt tag](https://i.imgur.com/HI2GCjA.png)

Parameters in the product alert popup have dependency on the first attribute. Attribute order can be changed in the module settings.

![alt tag](https://i.imgur.com/hBDKHk4.png)

## Installation

This module is composer ready so you can install it via command (do not forget to add this repo to the `composer.json` before):

```sh
composer require justcoded/backinstock-configurable:*
```

## Usage

### General settings

Enable the module: 

`Stores -> Configuration -> JUSTCODED -> Back in Stock Configurable -> Enable`

Set the attribute order in the subscription popup:

`Stores -> Configuration -> JUSTCODED -> Back in Stock Configurable -> Attributes Sorting Order Inside of Subscribe Popup`

Set CMS block id of popup header:

`Stores -> Configuration -> JUSTCODED -> Back in Stock Configurable -> Header CMS Block Identifier of Subscribe Popup`

Enable product alert sending by cron (otherwise they are sent on product save process which may take longer):

`Stores -> Configuration -> JUSTCODED -> Back in Stock Configurable -> Send Notifications by Schedule`

If product alerts are sent by cron, cron expression to trigger the cron is set under :

`Stores -> Configuration -> JUSTCODED -> Back in Stock Configurable -> Send Notification Schedule`

## Email templates

If you want to customize email templates for product alerts, just create a new email template `back_in_stock_configurable_notification_email_template` or load a default template Back In Stock Configurable Notification and use it as an example.

## Compatibility

Fully tested with Magento 2.1.6

## Contact

Follow our blog at [http://justcoded.com/blog](http://justcoded.com/blog)

## Maintainers

- [Oleg Biriukov](<obirukov@justcoded.co>)

## License

The MIT License (MIT)

Copyright © 2017 JustCoded

Permission is hereby granted free of charge to any person obtaining a copy of this software and associated documentation files (the "Software") to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.