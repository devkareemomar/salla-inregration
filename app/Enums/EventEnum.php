<?php


namespace App\Enums;

use App\Enums\EnumUtils;

class EventEnum
{
    use EnumUtils;

    public static function events()
    {
        return [
            'order.created' => [
                'data.customer.first_name' => '{name}',
                'data.id'                  => '{order_id}'
            ],

            'order.updated' => [
                'data.customer.first_name' => '{name}',
                'data.id'      => '{order_id}'
            ],
            
            'order.status.updated' => [
                'data.order.customer.name' => '{name}',
                'data.order.id'      => '{order_id}',
                'data.status'        => '{status}',
                
            ],
            'abandoned.cart' => [
                'customer.name' => '{name}',
                'items'          => '{items_count}',
                'checkout_url'    => '{checkout_url}',
                
            ],
        
        ];
    }
}
