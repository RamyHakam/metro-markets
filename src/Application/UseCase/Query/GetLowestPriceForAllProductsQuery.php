<?php

namespace App\Application\UseCase\Query;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('sync')]
final  readonly class GetLowestPriceForAllProductsQuery
{

}
