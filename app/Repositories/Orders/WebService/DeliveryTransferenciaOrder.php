<?php

namespace App\Repositories\Orders\WebService;
use App\Repositories\Orders\WebServiceOrderRepository;
use App\Traits\Expedition\HasDelivery;
use App\Traits\Payments\HasTransferencia;

class DeliveryTransferenciaOrder extends WebServiceOrderRepository
{
    use HasDelivery;
    use HasTransferencia;
}