<?php

namespace App\Services;


class OrderService {

    public static function events($event,$key)
    {
       $array =  [
            'order.created' => [
                'customer_name' => 'data.customer.first_name',
                'customer_phone' => 'data.customer.mobile',
                'order_id'      => 'data.id',
                'status'        => 'data.status.name',

            ],

            'order.updated' => [
                'customer_name' => 'data.customer.first_name',
                'customer_phone' => 'data.customer.mobile',
                'order_id'      => 'data.id',
                'status'        => 'data.status.name',
                
            ],
            
            'order.status.updated' => [
                'customer_name' => 'data.customer.first_name',
                'customer_phone' => 'data.customer.mobile',
                'order_id'      => 'data.order.id',
                'status'        => 'data.status',
        
            ],
        
        ];

        return $array[$event][$key];
    }
}