# Free Delivery

This module allows to manage free delivery for all delivery modules. 

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is FreeDelivery.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/free-delivery-module:~1.0
```

## Usage

You can manage this module in the module configuration in back-office. You can then set a minimum price for each module and each delivery area. If the customer cart price is above this price, the delivery price will be automatically set to 0. You can also choose if the taxes are taken into account or not in the price comparison. 
