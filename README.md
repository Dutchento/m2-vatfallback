# Vatfallback for Magento 2

Vatfallback module provides an API based validation and a fallback offline regex validation 
for the unstable VIES database used by Magento

Please find the [Magento 1 version here](https://github.com/sandermangel/rkvatfallback/).

## Supported services

- VIES check (this is the official endpoint but not the built-in Magento version)
- vatlayer.com check
- Regex fallback check for following countries; AT, BE, CZ, DE, CY, DK, EE, GR, ES, FI, FR, GB, HU, IE, IT, LT, LU, LV, MT, NL, PL, PT, SE, SI, SK
- Caching of previous results

## Features
1) A plugin that replaces the existing VAT check in Magento Customer implementing various services with means of fallback.

2) Use the console task:
`./bin/magento vat:validate NL NL133001477B01`

3) Use the API endpoint to get company data by VAT number
`http://domain.com/rest/V1/vat/companylookup/NL133001477B01`

4) Add a GraphQL endpoint by installing [elgentos/m2-vatfallback-graph-ql](https://github.com/elgentos/m2-vatfallback-graph-ql)

5) Caching service for VAT request (be sure to enable the VAT cache under Cache Management)

## Installation
``` shell
composer require dutchento/m2-vatfallback
bin/magento setup:upgrade
```

## Tested on

- Magento 2.2
- Magento 2.3
- Magento 2.4

## Changelog
[2.0.0] Add Official VIES as a service and don't use core Magento VIES request, added a caching service

[1.5.0] Better error handling if services are unavailable, no next service checking if 100% sure invalid result

[1.4.0] Refactor __constructors, decoupled validationservices from validator

[1.3.0] Regex validation now configurable via XML. Improved tests and regex validation

[1.2.0] added a timeout sys conf value for connecting to APIs

[1.1.1] fixed a logic error in calling fallback services including a false positives fix for the unofficial VIES endpoint by Laura Folco

[1.1.0] add an API endpoint for company data

[1.0.0] ported version of the Magento 1 module

## Requirements
- PHP >= 7.0
- GuzzleHTTP

## Disclaimer

Warning: Since all of the free VIES API's are slow and somewhat unreliable the checkout steps could become slow while checking.

## Authors

- Sander Mangel [Github](https://github.com/sandermangel) [Twitter](https://twitter.com/sandermangel)
- Jeroen Boersma [Github](https://github.com/jeroenboersma) [Twitter](https://twitter.com/srcoder)
- Timon de Groot [Github](https://github.com/tdgroot) [Twitter](https://twitter.com/TimonGreat)

### Authors M1 Version

- Sander Mangel [@sandermangel](https://twitter.com/sandermangel)
- Peter Jaap Blaakmeer [@peterjaap](https://twitter.com/peterjaap)
- Laura Folco [@lfolco](https://twitter.com/lfolco)
