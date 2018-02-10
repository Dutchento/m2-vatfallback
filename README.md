# Vatfallback for Magento 2

Vatfallback module provides an extra API based validation and a fallback offline regex validation 
for the unstable VIES database used by Magento

Please find the [Magento 1 version here](https://github.com/sandermangel/rkvatfallback/).

## Supported services

- Built in Magento VIES check
- Custom VIES check (this is not the official endpoint but an internal one)
- vatlayer.com check
- Regex fallback check for following countries; AT, BE, CZ, DE, CY, DK, EE, GR, ES, FI, FR, GB, HU, IE, IT, LT, LU, LV, MT, NL, PL, PT, SE, SI, SK

## Features
1) A plugin wraps the existing Vat check in Magento Customer implementing various services as fallback.

2) Use the console task:
`./bin/magento vat:validate NL NL133001477B01`

3) Use the API endpoint to get company data by VAT number
`http://domain.com/rest/V1/vat/companylookup/NL133001477B01`

## Compatibility

- Magento 2.2 Community & Commerce

## Changelog
[1.2.0] added a timeout sys conf value for connecting to APIs

[1.1.1] fixed a logic error in calling fallback services including a false positives fix for the unofficial VIES endpoint by Laura Folco

[1.1.0] add an API endpoint for company data

[1.0.0] ported version of the Magento 1 module

## Requirements
- PHP 7
- GuzzleHTTP

## Disclaimer

Warning: Since all of the free VIES API's are slow and somewhat unreliable the checkout steps could become slow while checking.

## Authors

- Sander Mangel [Github](https://github.com/sandermangel) [Twitter](https://twitter.com/sandermangel)
- Timon de Groot [Github](https://github.com/tdgroot) [Twitter](https://twitter.com/TimonGreat)

### Authors M1 Version

- Sander Mangel [@sandermangel](https://twitter.com/sandermangel)
- Peter Jaap Blaakmeer [@peterjaap](https://twitter.com/peterjaap)
- Laura Folco [@lfolco](https://twitter.com/lfolco)
