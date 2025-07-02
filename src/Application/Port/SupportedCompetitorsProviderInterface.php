<?php

namespace App\Application\Port;

use App\Application\Enum\CompetitorTypeEnum;
use App\Domain\ValueObject\ProductId;

interface SupportedCompetitorsProviderInterface
{
    public function addCompetitor(ProductId $id, CompetitorTypeEnum $competitorType): void;
    public function removeCompetitor(ProductId $id, CompetitorTypeEnum $competitorType): void;
}
