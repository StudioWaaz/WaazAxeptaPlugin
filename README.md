# Axepta plugin for Sylius by Studio Waaz  

This plugin is compliant with 3DSV2 protocol.  
After install this bundle, you need to configure following routes to accept POST requests :   
```
sylius_shop_order_thank_you:
    path: /{_locale}/order/thank-you
    methods: [GET, POST]
    defaults:
        _controller: sylius.controller.order:thankYouAction
        _sylius:
            template: "@SyliusShop/Order/thankYou.html.twig"

sylius_shop_order_show:
    path: /{_locale}/order/{tokenValue}
    methods: [GET, PUT, POST]
    defaults:
        _controller: sylius.controller.order:updateAction
        _sylius:
            template: "@SyliusShop/Order/show.html.twig"
            repository:
                method: findOneBy
                arguments: [tokenValue: $tokenValue]
            form:
                type: Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType
                options:
                    validation_groups: []
            redirect:
                route: sylius_shop_order_pay
                parameters:
                    tokenValue: resource.tokenValue
            flash: false
```