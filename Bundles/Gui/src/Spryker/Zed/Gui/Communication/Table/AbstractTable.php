<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Gui\Communication\Table;

use Generated\Shared\Transfer\DataTablesColumnTransfer;
use LogicException;
use PDO;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Propel;
use Spryker\Service\UtilSanitize\UtilSanitizeService;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Form\DeleteForm;
use Spryker\Zed\Kernel\Communication\Plugin\Pimple;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class AbstractTable
{
    public const TABLE_CLASS = 'gui-table-data';
    public const TABLE_CLASS_NO_SEARCH_SUFFIX = '-no-search';

    public const BUTTON_CLASS = 'class';
    public const BUTTON_HREF = 'href';
    public const BUTTON_DEFAULT_CLASS = 'btn-default';
    public const BUTTON_ICON = 'icon';
    public const PARAMETER_VALUE = 'value';
    public const SORT_BY_COLUMN = 'column';
    public const SORT_BY_DIRECTION = 'dir';
    public const URL_ANCHOR = '#';

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected $config;

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var int
     */
    protected $filtered = 0;

    /**
     * @var int
     */
    protected $defaultLimit = 10;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $defaultUrl = 'table';

    /**
     * @var string
     */
    protected $tableClass = self::TABLE_CLASS;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var string|null
     */
    protected $tableIdentifier;

    /**
     * @var \Generated\Shared\Transfer\DataTablesTransfer
     */
    protected $dataTablesTransfer;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    abstract protected function configure(TableConfiguration $config);

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    abstract protected function prepareData(TableConfiguration $config);

    /**
     * @return \Generated\Shared\Transfer\DataTablesTransfer
     */
    public function getDataTablesTransfer()
    {
        return $this->dataTablesTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\DataTablesTransfer $dataTablesTransfer
     *
     * @return void
     */
    public function setDataTablesTransfer($dataTablesTransfer)
    {
        $this->dataTablesTransfer = $dataTablesTransfer;
    }

    /**
     * @return $this
     */
    protected function init()
    {
        if (!$this->initialized) {
            $this->initialized = true;
            $this->request = $this->getRequest();
            $config = $this->newTableConfiguration();
            $config->setPageLength($this->getLimit());
            $config = $this->configure($config);
            $this->setConfiguration($config);
            $this->twig = $this->getTwig();

            if ($this->tableIdentifier === null) {
                $this->generateTableIdentifier();
            }
        }

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return (new Pimple())
            ->getApplication()['request'];
    }

    /**
     * @return void
     */
    public function disableSearch()
    {
        $this->tableClass .= self::TABLE_CLASS_NO_SEARCH_SUFFIX;
    }

    /**
     * @deprecated this method should not be needed.
     *
     * @param string $name
     *
     * @return string
     */
    public function buildAlias($name)
    {
        return str_replace(
            ['.', '(', ')'],
            '',
            $name
        );
    }

    /**
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function newTableConfiguration()
    {
        return new TableConfiguration();
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    public function setConfiguration(TableConfiguration $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function loadData(array $data)
    {
        $tableData = [];

        /** @var array|null $headers */
        $headers = $this->config->getHeader();
        $safeColumns = $this->config->getRawColumns();
        $extraColumns = $this->config->getExtraColumns();

        $isArray = is_array($headers);
        foreach ($data as $row) {
            $originalRow = $row;
            if ($isArray) {
                $row = array_intersect_key($row, $headers);

                $row = $this->reOrderByHeaders($headers, $row);
            }

            $row = $this->escapeColumns($row, $safeColumns);
            $row = array_values($row);

            if ($isArray) {
                $row = $this->addExtraColumns($row, $originalRow, $extraColumns);
            }

            $tableData[] = $row;
        }

        $this->setData($tableData);
    }

    /**
     * @param array $row
     * @param array $safeColumns
     *
     * @return mixed
     */
    protected function escapeColumns(array $row, array $safeColumns)
    {
        $callback = function (&$value, $key) use ($safeColumns) {
            if (!in_array($key, $safeColumns)) {
                $value = twig_escape_filter(new Environment(new FilesystemLoader()), $value);
            }

            return $value;
        };

        array_walk($row, $callback);

        return $row;
    }

    /**
     * @param array $headers
     * @param array $row
     *
     * @return array
     */
    protected function reOrderByHeaders(array $headers, array $row)
    {
        $result = [];

        foreach ($headers as $key => $value) {
            if (!array_key_exists($key, $row)) {
                continue;
            }
            $result[$key] = $row[$key];
        }

        return $result;
    }

    /**
     * @param array $row
     * @param array $originalRow
     * @param array $extraColumns
     *
     * @return array
     */
    protected function addExtraColumns(array $row, array $originalRow, array $extraColumns)
    {
        foreach ($extraColumns as $extraColumnName) {
            if (array_key_exists($extraColumnName, $row)) {
                continue;
            }
            $row[$extraColumnName] = $originalRow[$extraColumnName];
        }

        return $row;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getTableIdentifier()
    {
        if ($this->tableIdentifier === null) {
            $this->generateTableIdentifier();
        }

        return $this->tableIdentifier;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    protected function generateTableIdentifier($prefix = 'table-')
    {
        $this->tableIdentifier = $prefix . md5(static::class);

        return $this;
    }

    /**
     * @param string|null $tableIdentifier
     *
     * @return void
     */
    public function setTableIdentifier($tableIdentifier)
    {
        $this->tableIdentifier = $tableIdentifier;
    }

    /**
     * @throws \LogicException
     *
     * @return \Twig\Environment
     */
    private function getTwig()
    {
        /** @var \Twig\Environment|null $twig */
        $twig = (new Pimple())
            ->getApplication()['twig'];

        if ($twig === null) {
            throw new LogicException('Twig environment not set up.');
        }

        /** @var \Twig\Loader\ChainLoader $loaderChain */
        $loaderChain = $twig->getLoader();
        $loaderChain->addLoader(new FilesystemLoader(
            $this->getTwigPaths(),
            $this->getTwigRootPath()
        ));

        return $twig;
    }

    /**
     * @return string[]
     */
    protected function getTwigPaths()
    {
        return [
            __DIR__ . '/../../Presentation/Table/',
        ];
    }

    /**
     * @return string|null
     */
    protected function getTwigRootPath()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->request->query->get('start', 0);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    public function getOrders(TableConfiguration $config)
    {
        $defaultSorting = [$this->getDefaultSorting($config)];

        $orderParameter = $this->request->query->get('order');

        if (!is_array($orderParameter)) {
            return $defaultSorting;
        }

        $sorting = $this->createSortingParameters($orderParameter);

        if (empty($sorting)) {
            return $defaultSorting;
        }

        return $sorting;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function getDefaultSorting(TableConfiguration $config)
    {
        $sort = [
            self::SORT_BY_COLUMN => $config->getDefaultSortColumnIndex(),
            self::SORT_BY_DIRECTION => $config->getDefaultSortDirection(),
        ];

        $defaultSortField = $config->getDefaultSortField();
        if (!$defaultSortField) {
            return $sort;
        }

        $field = key($defaultSortField);
        $direction = $defaultSortField[$field];

        $availableFields = array_keys($config->getHeader());
        $index = array_search($field, $availableFields, true);
        if ($index === false) {
            return $sort;
        }

        $sort = [
            self::SORT_BY_COLUMN => $index,
            self::SORT_BY_DIRECTION => $direction,
        ];

        return $sort;
    }

    /**
     * @param array $orderParameter
     *
     * @return array
     */
    protected function createSortingParameters(array $orderParameter)
    {
        $sorting = [];
        foreach ($orderParameter as $sortingRules) {
            if (!is_array($sortingRules)) {
                continue;
            }
            $sorting[] = [
                self::SORT_BY_COLUMN => $this->getParameter($sortingRules, self::SORT_BY_COLUMN, '0'),
                self::SORT_BY_DIRECTION => $this->getParameter($sortingRules, self::SORT_BY_DIRECTION, 'asc'),
            ];
        }

        return $sorting;
    }

    /**
     * @param array $dataArray
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    protected function getParameter(array $dataArray, $key, $defaultValue)
    {
        if (array_key_exists($key, $dataArray)) {
            return $dataArray[$key];
        }

        return $defaultValue;
    }

    /**
     * @return mixed
     */
    public function getSearchTerm()
    {
        return $this->request->query->get('search', null);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        if (!$this->limit) {
            $this->limit = $this->request->query->getInt('length', $this->defaultLimit);
        }

        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->init();

        $twigVars = [
            'config' => $this->prepareConfig(),
        ];

        return $this->twig
            ->render('index.twig', $twigVars);
    }

    /**
     * @return array
     */
    public function prepareConfig()
    {
        $configArray = [
            'tableId' => $this->getTableIdentifier(),
            'class' => $this->tableClass,
            'url' => $this->defaultUrl,
            'baseUrl' => $this->baseUrl,
            'header' => [],
        ];

        if ($this->getConfiguration() instanceof TableConfiguration) {
            $configTableArray = [
                'url' => ($this->config->getUrl() === null) ? $this->defaultUrl : $this->config->getUrl(),
                'header' => $this->config->getHeader(),
                'footer' => $this->config->getFooter(),
                'order' => $this->getOrders($this->config),
                'searchable' => $this->config->getSearchable(),
                'sortable' => $this->config->getSortable(),
                'pageLength' => $this->config->getPageLength(),
                'processing' => $this->config->isProcessing(),
                'serverSide' => $this->config->isServerSide(),
                'stateSave' => $this->config->isStateSave(),
                'paging' => $this->config->isPaging(),
                'ordering' => $this->config->isOrdering(),
            ];

            $configArray = array_merge($configArray, $configTableArray);
        }

        return $configArray;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $criteria
     *
     * @return string
     */
    protected function getFirstAvailableColumnInQuery(ModelCriteria $criteria)
    {
        $tableMap = $criteria->getTableMap();
        $columns = array_keys($tableMap->getColumns());

        $firstColumnName = $tableMap->getColumn($columns[0])->getName();

        return $tableMap->getName() . '.' . $firstColumnName;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @param array $order
     *
     * @return string
     */
    protected function getOrderByColumn(ModelCriteria $query, TableConfiguration $config, array $order)
    {
        $columns = $this->getColumnsList($query, $config);

        if (isset($order[0]) && isset($order[0][self::SORT_BY_COLUMN]) && isset($columns[$order[0][self::SORT_BY_COLUMN]])) {
            $selectedColumn = $columns[$order[0][self::SORT_BY_COLUMN]];

            if (in_array($selectedColumn, $config->getSortable(), true)) {
                return $selectedColumn;
            }
        }

        return $this->getFirstAvailableColumnInQuery($query);
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function getColumnsList(ModelCriteria $query, TableConfiguration $config)
    {
        if ($config->getHeader()) {
            return array_keys($config->getHeader());
        }

        return array_keys($query->getTableMap()->getColumns());
    }

    /**
     * @todo CD-412 refactor this class to allow unspecified header columns and to add flexibility
     *
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @param bool $returnRawResults
     *
     * @return array|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function runQuery(ModelCriteria $query, TableConfiguration $config, $returnRawResults = false)
    {
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        $order = $this->getOrders($config);

        $orderColumn = $this->getOrderByColumn($query, $config, $order);

        $this->total = $this->countTotal($query);
        $query->orderBy($orderColumn, $order[0][self::SORT_BY_DIRECTION]);
        $searchTerm = $this->getSearchTerm();
        $searchValue = $searchTerm[static::PARAMETER_VALUE] ?? '';

        if (mb_strlen($searchValue) > 0) {
            $query->setIdentifierQuoting(true);

            $conditions = [];

            foreach ($config->getSearchable() as $value) {
                $filter = '';
                $driverName = Propel::getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME);
                if ($driverName === 'pgsql') {
                    $filter = '::TEXT';
                }

                $conditionParameter = '%' . mb_strtolower($searchValue) . '%';
                $condition = sprintf(
                    'LOWER(%s%s) LIKE %s',
                    $value,
                    $filter,
                    Propel::getConnection()->quote($conditionParameter)
                );

                $conditions[] = $condition;
            }

            $query = $this->applyConditions($query, $config, $conditions);

            $this->filtered = $query->count();
        } else {
            $this->filtered = $this->total;
        }

        if ($this->dataTablesTransfer !== null) {
            $searchColumns = $config->getSearchable();

            $this->addFilteringConditions($query, $searchColumns);
        }

        $query->offset($offset)
            ->limit($limit);

        $data = $query->find();

        if ($returnRawResults === true) {
            return $data;
        }

        return $data->toArray(null, false, TableMap::TYPE_COLNAME);
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     *
     * @return int
     */
    protected function countTotal(ModelCriteria $query): int
    {
        return $query->count();
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @param string[] $conditions
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function applyConditions(ModelCriteria $query, TableConfiguration $config, array $conditions): ModelCriteria
    {
        $gluedCondition = implode(
            sprintf(' %s ', Criteria::LOGICAL_OR),
            $conditions
        );

        $gluedCondition = '(' . $gluedCondition . ')';

        if ($config->getHasSearchableFieldsWithAggregateFunctions()) {
            return $query->having($gluedCondition);
        }

        return $query->where($gluedCondition);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function filterSearchValue($value)
    {
        $value = str_replace(['^', '$'], '', $value);
        $value = stripslashes($value);

        return $value;
    }

    /**
     * @return array
     */
    public function fetchData()
    {
        $this->init();

        $data = $this->prepareData($this->config);
        $this->loadData($data);
        $wrapperArray = [
            'draw' => $this->request->query->get('draw', 1),
            'recordsTotal' => $this->total,
            'recordsFiltered' => $this->filtered,
            'data' => $this->data,
        ];

        return $wrapperArray;
    }

    /**
     * Drop table name from key
     *
     * @param string $key
     *
     * @return string
     */
    public function cutTablePrefix($key)
    {
        $position = mb_strpos($key, '.');

        return ($position !== false) ? mb_substr($key, $position + 1) : $key;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function camelize($str)
    {
        return str_replace(' ', '', ucwords(mb_strtolower(str_replace('_', ' ', $str))));
    }

    /**
     * @param int $total
     *
     * @return void
     */
    protected function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @param int $filtered
     *
     * @return void
     */
    protected function setFiltered($filtered)
    {
        $this->filtered = $filtered;
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function generateCreateButton($url, $title, array $options = [])
    {
        $defaultOptions = [
            'class' => 'btn-create',
            'icon' => 'fa-plus',
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function generateEditButton($url, $title, array $options = [])
    {
        $defaultOptions = [
            'class' => 'btn-edit',
            'icon' => 'fa-pencil-square-o',
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function generateViewButton($url, $title, array $options = [])
    {
        $defaultOptions = [
            'class' => 'btn-view',
            'icon' => 'fa-caret-right',
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param string $title
     * @param string $url
     * @param bool $separated
     * @param array $options
     *
     * @return array
     */
    protected function createButtonGroupItem($title, $url, $separated = false, array $options = [])
    {
        return [
            'title' => $title,
            'url' => $url,
            'separated' => $separated,
            'options' => $options,
        ];
    }

    /**
     * @param array $buttonGroupItems
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function generateButtonGroup(array $buttonGroupItems, $title, array $options = [])
    {
        $defaultOptions = [
            'class' => 'btn-view',
            'icon' => 'fa-caret-right',
        ];

        return $this->generateButtonGroupHtml($buttonGroupItems, $title, $defaultOptions, $options);
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function generateRemoveButton($url, $title, array $options = [])
    {
        $formFactory = $this->getFormFactory();

        $options = [
            'fields' => $options,
            'action' => $url,
        ];

        $form = $formFactory->create(DeleteForm::class, [], $options);

        $options['form'] = $form->createView();
        $options['title'] = $title;

        return $this->twig->render('delete-form.twig', $options);
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return (new Pimple())->getApplication()['form.factory'];
    }

    /**
     * @param string|\Spryker\Service\UtilText\Model\Url\Url $url
     * @param string $title
     * @param array $defaultOptions
     * @param array $customOptions
     *
     * @return string
     */
    protected function generateButton($url, $title, array $defaultOptions, array $customOptions = [])
    {
        $buttonOptions = $this->generateButtonOptions($defaultOptions, $customOptions);

        $class = $this->getButtonClass($defaultOptions, $customOptions);
        $parameters = $this->getButtonParameters($buttonOptions);
        $icon = '';

        if (array_key_exists(self::BUTTON_ICON, $buttonOptions) === true && $buttonOptions[self::BUTTON_ICON] !== null) {
            $icon = '<i class="fa ' . $buttonOptions[self::BUTTON_ICON] . '"></i> ';
        }

        return $this->getTwig()->render('button.twig', [
            'url' => $this->buildUrl($url),
            'class' => $class,
            'title' => $title,
            'icon' => $icon,
            'parameters' => $parameters,
        ]);
    }

    /**
     * @param \Spryker\Service\UtilText\Model\Url\Url|string $url
     *
     * @return string
     */
    protected function buildUrl($url): string
    {
        if ($url === static::URL_ANCHOR) {
            return static::URL_ANCHOR;
        }

        if (is_string($url)) {
            $url = Url::parse($url);
        }

        return $url->build();
    }

    /**
     * @param string $title
     * @param string|null $class
     *
     * @return string
     */
    protected function generateLabel(string $title, ?string $class): string
    {
        return $this->getTwig()->render('label.twig', [
            'title' => $title,
            'class' => $class,
        ]);
    }

    /**
     * @param array $buttons
     * @param string $title
     * @param array $defaultOptions
     * @param array $customOptions
     *
     * @return string
     */
    protected function generateButtonGroupHtml(array $buttons, $title, array $defaultOptions, array $customOptions = [])
    {
        $buttonOptions = $this->generateButtonOptions($defaultOptions, $customOptions);
        $class = $this->getButtonClass($defaultOptions, $customOptions);
        $parameters = $this->getButtonParameters($buttonOptions);

        $icon = '';
        if (array_key_exists(self::BUTTON_ICON, $buttonOptions) === true && $buttonOptions[self::BUTTON_ICON] !== null) {
            $icon .= '<i class="fa ' . $buttonOptions[self::BUTTON_ICON] . '"></i> ';
        }

        return $this->generateButtonDropdownHtml($buttons, $title, $icon, $class, $parameters);
    }

    /**
     * @param array $buttons
     * @param string $title
     * @param string $icon
     * @param string $class
     * @param string $parameters
     *
     * @return string
     */
    protected function generateButtonDropdownHtml(array $buttons, $title, $icon, $class, $parameters)
    {
        $nestedButtons = [];

        foreach ($buttons as $button) {
            if (is_string($button['url'])) {
                $utilSanitizeService = new UtilSanitizeService();
                $url = $utilSanitizeService->escapeHtml($button['url']);
            } else {
                /** @var \Spryker\Service\UtilText\Model\Url\Url $buttonUrl */
                $buttonUrl = $button['url'];
                $url = $buttonUrl->buildEscaped();
            }

            $buttonParameters = '';
            if (isset($button['options'])) {
                $buttonParameters = $this->getButtonParameters($button['options']);
            }

            $nestedButtons[] = [
                'needDivider' => !empty($button['separated']),
                'url' => $url,
                'params' => $buttonParameters,
                'title' => $button['title'],
            ];
        }

        return $this->getTwig()->render('button-dropdown.twig', [
            'class' => $class,
            'parameters' => $parameters,
            'icon' => $icon,
            'title' => $title,
            'nestedButtons' => $nestedButtons,
        ]);
    }

    /**
     * @param array $defaultOptions
     * @param array $options
     *
     * @return string
     */
    protected function getButtonClass(array $defaultOptions, array $options = [])
    {
        $class = '';

        if (isset($defaultOptions[self::BUTTON_CLASS])) {
            $class .= ' ' . $defaultOptions[self::BUTTON_CLASS];
        }
        if (isset($options[self::BUTTON_CLASS])) {
            $class .= ' ' . $options[self::BUTTON_CLASS];
        }

        if (empty($class)) {
            return self::BUTTON_DEFAULT_CLASS;
        }

        return $class;
    }

    /**
     * @param array $buttonOptions
     *
     * @return string
     */
    protected function getButtonParameters(array $buttonOptions)
    {
        $parameters = '';
        foreach ($buttonOptions as $argument => $value) {
            if (in_array($argument, [self::BUTTON_CLASS, self::BUTTON_HREF, self::BUTTON_ICON])) {
                continue;
            }
            $parameters .= sprintf(' %s=\'%s\'', $argument, $value);
        }

        return $parameters;
    }

    /**
     * @param array $defaultOptions
     * @param array $options
     *
     * @return array
     */
    protected function generateButtonOptions(array $defaultOptions, array $options = [])
    {
        $buttonOptions = $defaultOptions;
        if (is_array($options)) {
            $buttonOptions = array_merge($defaultOptions, $options);
        }

        return $buttonOptions;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param array $searchColumns
     * @param \Generated\Shared\Transfer\DataTablesColumnTransfer $column
     *
     * @return void
     */
    protected function addQueryCondition(ModelCriteria $query, array $searchColumns, DataTablesColumnTransfer $column)
    {
        $search = $column->getSearch();
        if (preg_match('/created_at|updated_at/', $searchColumns[$column->getData()])) {
            $query->where(
                sprintf(
                    '(%s >= %s AND %s <= %s)',
                    $searchColumns[$column->getData()],
                    Propel::getConnection()->quote($this->filterSearchValue($search[self::PARAMETER_VALUE]) . ' 00:00:00'),
                    $searchColumns[$column->getData()],
                    Propel::getConnection()->quote($this->filterSearchValue($search[self::PARAMETER_VALUE]) . ' 23:59:59')
                )
            );

            return;
        }

        $value = $this->filterSearchValue($search[self::PARAMETER_VALUE]);
        if ($value === 'null') {
            return;
        }

        $query->where(sprintf(
            '%s = %s',
            $searchColumns[$column->getData()],
            Propel::getConnection()->quote($value)
        ));
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param array $searchColumns
     *
     * @return void
     */
    protected function addFilteringConditions(ModelCriteria $query, array $searchColumns)
    {
        foreach ($this->dataTablesTransfer->getColumns() as $column) {
            $search = $column->getSearch();
            if (empty($search[self::PARAMETER_VALUE])) {
                continue;
            }

            $this->addQueryCondition($query, $searchColumns, $column);
        }
    }
}
