<?php

namespace App\Repositories\Orders\WebService;
use App\Traits\Expedition\HasPickup;
use App\Traits\Payments\HasTransferencia;
use App\Repositories\Orders\WebServiceOrderRepository;

class PickupTransferenciaOrder extends WebServiceOrderRepository
{
    use HasPickup;
    use HasTransferencia;
}