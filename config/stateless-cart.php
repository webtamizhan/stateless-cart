<?php

return [

    /**
     * Here you can set the connection that the Stateless Cart System should use when
     * storing and restoring a cart.
     * Default table name was 'cart'
     */
    'database' => [
        'connection' => null,
        'table' => 'cart',
    ],

    /**
     * This defaults will be used for the formatted numbers if you don't
     * set them in the method call.
     */
    'format' => [
        'decimals' => 2,
        'decimal_point' => ',',
        'thousand_separator' => '.'
    ],


    /**
     * This default tax rate will be used when you make a class implement the
     * Taxable interface and use the HasTax trait.
     */

    'tax' => '0',

    /**
     * When this option is set to 'true' the cart will automatically
     * destroy all cart instances when the user logs out.
     */

    'destroy_on_logout' => false,

    /**
     * You can change the cart default session name
     */
    'cart_session_name' => 'stateless-cart'
];
