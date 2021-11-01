<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
            // 'product_id' => $this->id_product,
            // 'product_name' => $this->product_name,
            // 'product_price' => $this->product_price,
            // 'product_qty' => $this->product_quantity,
            // 'order_coupon' => $this->product_coupon,
            // 'order_fee' => $this->product_fee,
    }
}
