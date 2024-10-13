<?php
namespace App\Command;

use App\Entity\Holiday;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Yasumi\Yasumi;

class ImportHolidaysCommand extends Command
{
    protected static string $defaultName = 'app:import-holidays';
    protected static string $defaultDescription = 'Получить праздники для всех стран из библиотеки Yasumi и сохранить их в базу данных';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setName(self::$defaultName)->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $startTime = microtime(true);
        $startDateTime = date('Y-m-d H:i:s');
        $io->section("Импорт праздников запущен: $startDateTime");
        $year = (int) date('Y');
        $countries = Yasumi::getProviders();

        $progressBar = new ProgressBar($output, count($countries));
        $progressBar->start();

        foreach ($countries as $country) {
            try {
                $holidaysProvider = Yasumi::create($country, $year);
                $holidays = $holidaysProvider->getHolidays();
                foreach ($holidays as $holiday) {
                    $existingHoliday = $this->entityManager->getRepository(Holiday::class)
                        ->findOneBy(['name' => $holiday->getName(), 'year' => $year]);

                    if ($existingHoliday) {
                        $existingHoliday->setType($holiday->getType())
                            ->setTranslations($holiday->translations)
                            ->setTimezone($holiday->getTimezone()->getName())
                            ->setHolidayDate($holiday)
                            ->setCountry($country);

                        $this->entityManager->persist($existingHoliday);
                    } else {
                        $holidayEntity = new Holiday();
                        $holidayEntity->setName($holiday->getName())
                            ->setYear($year)
                            ->setType($holiday->getType())
                            ->setTranslations($holiday->translations)
                            ->setTimezone($holiday->getTimezone()->getName())
                            ->setHolidayDate($holiday)
                            ->setCountry($country);

                        $this->entityManager->persist($holidayEntity);
                    }
                }

                $this->entityManager->flush();

                $progressBar->advance();

            } catch (\Exception $e) {
                $io->error("Ошибка при импорте праздников для $country: " . $e->getMessage());
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $output->writeln('');
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $io->section('Команда завершена.');
        $io->success(sprintf("Общее время выполнения: %.2f секунд.", $executionTime));

        return Command::SUCCESS;
    }
}