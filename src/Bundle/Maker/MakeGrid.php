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

use Sylius\Component\Grid\Maker\Helper\GridHelperInterface;
use Sylius\Component\Grid\Maker\Helper\ResourceHelperInterface;
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
            ->addArgument('section', InputArgument::REQUIRED, 'Section of the grid (backend, frontend, admin, api)')
            ->addArgument('resource', InputArgument::REQUIRED, 'Resource alias of the grid')
        ;

        $inputConfig->setArgumentAsNonInteractive('resource');
        $inputConfig->setArgumentAsNonInteractive('section');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (!$input->getArgument('section')) {
            $section = $io->ask('Enter a section for you grid (backend, frontend, admin, api)', 'backend');

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

        $sortableFields = $this->filterSortableFields($fields);
        $sortingFieldNames = [];

        if (count($sortableFields) > 0) {
            $sortingFieldNames = $this->askForSortingFieldNames($io, $sortableFields);
        }

        $sortingFields = [];
        foreach ($sortingFieldNames as $fieldName) {
            $order = $this->askForSortingOrder($io, $fieldName);
            $sortingFields[$fieldName] = $order;
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

        $filters = [];
        $isFirstFilter = true;
        while (true) {
            $newFilter = $this->askForNextFilter($io, $isFirstFilter);
            $isFirstFilter = false;

            if (null === $newFilter) {
                break;
            }

            $filters = array_merge($filters, $newFilter);
        }

        $this->generateGridConfigFile($input, $io, $generator, $fields, $sortingFields, $actions, $filters);
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
            return $name ?: null;
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

    private function askForSortingFieldNames(ConsoleStyle $io, array $sortableFields)
    {
        $io->writeln('');

        $choices = array_combine($sortableFields, $sortableFields);

        $choiceQuestion = new ChoiceQuestion(
            'Enter the default sorting fields (coma-separated)',
            $choices
        );
        $choiceQuestion->setMultiselect(true);
        $choiceQuestion->setAutocompleterValues($choices);

        return $io->askQuestion($choiceQuestion);
    }

    private function askForSortingOrder(ConsoleStyle $io, string $fieldName)
    {
        $io->writeln('');

        $choiceQuestion = new ChoiceQuestion(
            sprintf('Enter the sorting order for %s', $fieldName),
            ['asc', 'desc'],
            0
        );

        return $io->askQuestion($choiceQuestion);
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

    private function askForNextFilter(ConsoleStyle $io, bool $isFirstFilter)
    {
        $io->writeln('');

        if ($isFirstFilter) {
            $questionText = 'New filter name (press <return> to stop adding filters)';
        } else {
            $questionText = 'Add another filter? Enter the filter name (or press <return> to stop adding fields)';
        }

        $filterName = $io->ask($questionText, null, function ($name): ?string {
            return $name ?: null;
        });

        if (!$filterName) {
            return null;
        }

        $filterData = [];

        $choiceQuestion = new ChoiceQuestion(
            'Enter the filter type',
            $this->gridHelper->getFilterIds()
        );
        $filterData['type'] = $io->askQuestion($choiceQuestion);

        $choiceQuestion = new ChoiceQuestion(
            'Enter the filter options (Leave blank if you have no options',
            ['field', 'fields']
        );

        $optionType = $io->askQuestion($choiceQuestion);

        $filterData['options'] = [];
        if ('fields' === $optionType) {
            $fieldsPath = $io->ask('Enter the fields path (coma-separated)');
            $fieldsPath = str_replace(', ', ',', $fieldsPath);
            $filterData['options'][$optionType] = explode(',', $fieldsPath);
        } elseif ('field' === $optionType) {
            $filterData['options'][$optionType] = $io->ask('Enter the field path');
        }

        $filterData['form_options'] = [];
        $isFirstFormOption = true;
        while (true) {
            $newFormOption = $this->askForNextFormOption($io, $isFirstFormOption);
            $isFirstFormOption = false;

            if (null === $newFormOption) {
                break;
            }

            $filterData['form_options'] = array_merge($filterData['form_options'], $newFormOption);
        }

        // Key is not necessary if there are no form options
        if (0 === count($filterData['form_options'])) {
            unset($filterData['form_options']);
        }

        return [
            $filterName => $filterData,
        ];
    }

    private function askForNextFormOption(ConsoleStyle $io, bool $isFirstFormOption)
    {
        $io->writeln('');

        if ($isFirstFormOption) {
            $questionText = 'New form option name (press <return> to stop adding form options)';
        } else {
            $questionText = 'Add another form option? Enter the form option name (or press <return> to stop adding form options)';
        }

        $formOptionName = $io->ask($questionText, null, function ($name): ?string {
            return $name ?: null;
        });

        if (!$formOptionName) {
            return null;
        }

        $formOptionValue = $io->ask('Enter the form option value');

        return [
            $formOptionName => $formOptionValue,
        ];
    }

    private function generateGridConfigFile(
        InputInterface $input,
        ConsoleStyle $io,
        Generator $generator,
        array $fields,
        array $sortingFields,
        array $actions,
        array $filters
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

        if (count($sortingFields) > 0) {
            $gridData['sorting'] = $sortingFields;
        }

        if (count($fields) > 0) {
            $gridData['fields'] = $fields;
        }

        if (count($filters) > 0) {
            $gridData['filters'] = $filters;
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

    private function filterSortableFields(array $fields): array
    {
        $sortableFields = [];
        foreach ($fields as $fieldName => $field) {
            if (array_key_exists('sortable', $field)) {
                $sortableFields[] = $fieldName;
            }
        }

        return $sortableFields;
    }
}
