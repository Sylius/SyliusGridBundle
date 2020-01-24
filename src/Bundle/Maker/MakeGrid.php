<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Maker;

use Sylius\Component\Grid\Maker\Filter\Helper\GridHelperInterface;
use Sylius\Component\Grid\Maker\Filter\ResourceHelperInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

final class MakeGrid extends AbstractMaker
{
    public static $defaultActionTypes = [
        'main' => [
            'create' => 'create',
        ],
        'item' => [
            'update' => 'update',
            'delete' => 'delete',
        ],
        'bulk' => [
            'delete' => 'delete',
        ],
    ];

    /** @var string */
    private $projectDir;

    /** @var ResourceHelperInterface */
    private $resourceHelper;

    /** @var GridHelperInterface */
    private $gridHelper;

    /** @var Filesystem */
    private $fileSystem;

    public function __construct(string $projectDir, ResourceHelperInterface $resourceHelper, GridHelperInterface $gridHelper)
    {
        $this->projectDir = $projectDir;
        $this->resourceHelper = $resourceHelper;
        $this->gridHelper = $gridHelper;

        $this->fileSystem = new Filesystem();
    }

    public static function getCommandName(): string
    {
        return 'make:sylius-grid';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates a new grid')
            ->addArgument('section', InputArgument::REQUIRED, 'Section of the grid (backend or frontend)')
            ->addArgument('resource', InputArgument::REQUIRED, 'Resource alias of the grid')
        ;

        $inputConfig->setArgumentAsNonInteractive('resource');
        $inputConfig->setArgumentAsNonInteractive('section');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (!$input->getArgument('section')) {
            $question = new ChoiceQuestion(
                'Please select a section for your grid',
                ['backend', 'frontend'],
                0
            );

            $section = $io->askQuestion($question);

            $input->setArgument('section', $section);
        }

        if (!$input->getArgument('resource')) {
            $aliases = $this->resourceHelper->getResourcesAliases();
            $question = new ChoiceQuestion(
                'Please select a resource for your grid',
                array_combine($aliases, $aliases),
                0
            );
            $question->setAutocompleterValues($aliases);

            $resourceAlias = $io->askQuestion($question);

            $input->setArgument('resource', $resourceAlias);
        }

        if ($resourceAlias = $input->getArgument('resource')) {
            Assert::true($this->resourceHelper->isResourceAliasExist($resourceAlias), sprintf(
                    'Resource with alias %s not found',
                    $resourceAlias
                )
            );
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $fields = [];
        $isFirstField = true;
        while (true) {
            $newField = $this->askForNextField($io, $isFirstField);
            $isFirstField = false;

            if (null === $newField) {
                break;
            }

            $fields = array_merge($fields, $newField);
        }

        $actions = [
            'main' => [],
            'item' => [],
            'bulk' => [],
        ];

        foreach ($actions as $section => $data) {
            $hasSectionAction = $io->confirm(sprintf('Do you have %s actions?', $section), true);

            if ($hasSectionAction) {
                $newActions = $this->askForNextAction($io, $section);
                $actions[$section] = array_merge($actions[$section], $newActions);
            } else {
                unset($actions[$section]);
            }
        }

        $this->generateGridConfigFile($input, $io, $generator, $fields, $actions);
    }

    private function askForNextField(ConsoleStyle $io, bool $isFirstField)
    {
        $io->writeln('');

        if ($isFirstField) {
            $questionText = 'New field name (press <return> to stop adding fields)';
        } else {
            $questionText = 'Add another field? Enter the field name (or press <return> to stop adding fields)';
        }

        $fieldName = $io->ask($questionText, null, function ($name): ?string {
            // allow it to be empty
            if (!$name) {
                return $name;
            }

            return $name;
        });

        if (!$fieldName) {
            return null;
        }

        $fieldData = [];
        $fieldData['type'] = $io->choice('Enter the field type', ['string', 'datetime', 'twig'], 'string');
        $fieldData['label'] = $io->ask('Enter the field label', null);
        $isSortable = $io->confirm('Is the field sortable?', true);

        if ($isSortable) {
            $fieldData['sortable'] = $io->ask('Enter the sortable path (leave blank to set the default value)', null);
        }

        if ('twig' === $fieldData['type']) {
            $fieldData['options'] = [];
            $fieldData['options']['template'] = $io->ask('Enter the twig template path', null);
        }

        if ('datetime' === $fieldData['type']) {
            $fieldData['options'] = [];
            $fieldData['options']['format'] = $io->ask('Enter the date format', 'Y-m-d H:i:s');
        }

        return [
            $fieldName => $fieldData,
        ];
    }

    private function askForNextAction(ConsoleStyle $io, string $section)
    {
        $io->writeln('');

        $actionTypes = static::$defaultActionTypes[$section];

        if (count($actionTypes) > 1) {
            $choiceQuestion = new ChoiceQuestion(
                'Enter the action types (coma-separated)',
                static::$defaultActionTypes[$section],
                implode(', ', static::$defaultActionTypes[$section])
            );
            $choiceQuestion->setMultiselect(true);
            $choiceQuestion->setAutocompleterValues($actionTypes);
            $actionTypes = $io->askQuestion($choiceQuestion);
        }

        $data = [];
        foreach ($actionTypes as $type) {
            $data[$type] = [
                'type' => $type,
            ];
        }

        return $data;
    }

    private function generateGridConfigFile(
        InputInterface $input,
        ConsoleStyle $io,
        Generator $generator,
        array $fields,
        array $actions
    ): void {
        $section = $input->getArgument('section');
        $resourceAlias = $input->getArgument('resource');
        [$appName, $resourceName] = $this->resourceHelper->splitResourceAlias($resourceAlias);
        $gridId = sprintf('%s_%s_%s', $appName, $section, $resourceName);

        $gridConfigDir = $this->getGridConfigDir($resourceAlias, $section);
        $modelClass = $this->resourceHelper->getResourceModelFromAlias($resourceAlias);

        $gridData = [
            'driver' => [
                'name' => 'doctrine/orm',
                'options' => [
                    'class' => '"%'.$modelClass.'%"',
                ],
            ],
        ];

        if (count($fields) > 0) {
            $gridData['fields'] = $fields;
        }

        if (count($actions) > 0) {
            $gridData['actions'] = $actions;
        }

        $data = [
            'sylius_grid' => [
                'grids' => [
                    $gridId => $gridData,
                ],
            ],
        ];

        $yaml = Yaml::dump($data, 10, 4, Yaml::DUMP_NULL_AS_TILDE);

        $this->fileSystem->dumpFile($gridConfigDir, $yaml);
    }

    private function getGridConfigDir(string $resourceAlias, string $section)
    {
        $resource = $this->resourceHelper->getResourceNameFromAlias($resourceAlias);
        $filename = $resource.'.yaml';

        return sprintf('%s/%s/%s', $this->projectDir.'/config/packages/grids', $section, $filename);
    }
}
