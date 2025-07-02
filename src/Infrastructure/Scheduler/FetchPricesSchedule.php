<?php

namespace App\Infrastructure\Scheduler;

use App\Application\UseCase\Command\StartFetchCompetitorPricesMessage;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule(name: 'fetch_prices')]
final readonly class FetchPricesSchedule implements ScheduleProviderInterface
{
    public function __construct(
        #[Target('schedulerLogger')]
        private LoggerInterface $schedulerLogger)
    {
    }

    public function getSchedule(): Schedule
    {
        $this->schedulerLogger->info('FetchPricesSchedule initialized with 10 seconds interval.');
        return (new Schedule())->add(
            RecurringMessage::every('10 seconds ', new StartFetchCompetitorPricesMessage()),
        );
    }
}
