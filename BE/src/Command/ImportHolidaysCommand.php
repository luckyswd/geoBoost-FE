<?php

namespace App\Command;

use App\Entity\Holiday;
use App\Repository\HolidayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yasumi\Yasumi;

#[AsCommand(
    name: 'app:import-holidays',
    description: 'Получить праздники для всех стран из библиотеки Yasumi и сохранить их в базу данных',
)]
class ImportHolidaysCommand extends BaseCommand
{
    public function __construct(
        private HolidayRepository $holidayRepository,
        private EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $currentYear = (int)date('Y');
        $nextYear = $currentYear + 1;

        $years = [$currentYear, $nextYear];

        $countries = Yasumi::getProviders();

        $total = count($countries) * count($years);
        $io->progressStart($total);

        foreach ($years as $year) {
            foreach ($countries as $country) {
                $holidaysProvider = Yasumi::create($country, $year);
                $holidays = $holidaysProvider->getHolidays();

                foreach ($holidays as $holiday) {
                    $holidayEntity = $this->holidayRepository->findOneBy([
                        'name' => $holiday->getName(),
                        'year' => $year,
                        'country' => $country,
                    ]);

                    if (!$holidayEntity) {
                        $holidayEntity = new Holiday();
                    }

                    $holidayEntity
                        ->setName($holiday->getName())
                        ->setYear($year)
                        ->setType($holiday->getType())
                        ->setTranslations($holiday->translations)
                        ->setTimezone($holiday->getTimezone()->getName())
                        ->setHolidayDate($holiday)
                        ->setCountry($country);

                    $this->entityManager->persist($holidayEntity);
                }

                $io->progressAdvance();
            }
        }

        $this->entityManager->flush();
        $io->progressFinish();

        return Command::SUCCESS;
    }
}
