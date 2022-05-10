<?php

namespace App\Helpers\Queries;

use App\Helpers\Queries\Filters\Filter;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Query
{
    /**
     * @var string - model class name, should be set in the child class
     */
    protected $model;

    protected $request;
    protected $rowsCount;

    /**
     * Extra where clauses to be applied, e.g. [['account_id', '=', 1], ['balance', '>', 1000]]
     *
     * @var array
     */
    protected $whereClauses = [];

    /**
     * Custom search filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Relationships to eager load
     *
     * @var array
     */
    protected $with = [];

    /**
     * Regular or associative array of sortable columns, e.g.:
     *
     * ['id', 'name']
     * ['id' => 'trades.id', 'name' => 'assets.name']
     *
     * Override this variable in a child class if you like to restrict the sortable columns
     *
     * @var array
     */
    protected $sortableColumns = [];

    /**
     * Default sort column
     * @var string
     */
    protected $defaultOrderBy = 'id';

    /**
     * Default order direction (asc / desc)
     * @var string
     */
    protected $defaultOrderDirection = 'desc';

    /**
     * How many items per page is allowed to select
     * @var array
     */
    protected $itemsPerPageOptions = [5, 10, 25, 50];

    /**
     * Default number of items per page
     *
     * @var int
     */
    protected $defaultItemsPerPage = 10;

    /**
     * Columns that can be searched
     *
     * @var array
     */
    protected $searchableColumns = [];

    /**
     * Query constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (!$this->model && get_called_class() != __CLASS__) {
            throw new Exception('Model property should be set when instantiating a new Query class.');
        }

        $this->request = request();

        return $this;
    }

    /**
     * Create class instance by passing the model class as an argument
     *
     * @param string $model
     * @return Query
     * @throws Exception
     */
    public static function make(string $model): Query
    {
        return tap(new self(), function ($instance) use ($model) {
            $instance->model = $model;
        });
    }


    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get total rows count (required for pagination on the frontend side)
     *
     * @return int
     */
    public function getRowsCount(): int
    {
        if (is_null($this->rowsCount)) {
            $this->rowsCount = $this->calculateRowsCount();
        }

        return $this->rowsCount;
    }

    /**
     * Calculate rows count
     *
     * @return int
     */
    protected function calculateRowsCount(): int
    {
        return $this->getBaseBuilder()->count();
    }

    /**
     * Get number of rows to skip in the result recordset
     *
     * @return int
     */
    public function getRowsToSkip(): int
    {
        return ($this->getPage() - 1) * $this->getItemsPerPage();
    }

    /**
     * Get number of items displayed per page
     *
     * @return array|int|string|null
     */
    public function getItemsPerPage(): int
    {
        $itemsPerPage = $this->request->query('items_per_page');
        return in_array($itemsPerPage, $this->itemsPerPageOptions) ? $itemsPerPage : $this->defaultItemsPerPage;
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function getPage(): int
    {
        $page = (int) $this->request->query('page');
        return max(1, min($this->getPagesCount(), $page));
    }

    /**
     * Get sort column id
     *
     * @param Request $request
     * @return string
     */
    public function getOrderBy(): string
    {
        $orderBy = $this->request->query('sort_by') ?: $this->defaultOrderBy;

        return empty($this->sortableColumns)
            ? $this->defaultOrderBy
            : (array_key_exists($orderBy, $this->sortableColumns) // otherwise check that the column is present in sortableColumns array
                ? $this->sortableColumns[$orderBy]
                : (in_array($orderBy, $this->sortableColumns)
                    ? $orderBy
                    : $this->defaultOrderBy));
    }

    /**
     * Get order direction
     *
     * @return string
     */
    public function getOrderDirection(): string
    {
        $orderDirection = $this->request->query('sort_direction');

        return in_array($orderDirection, ['asc', 'desc'])
            ? $orderDirection
            : $this->defaultOrderDirection;
    }

    /**
     * Get search string
     *
     * @return string|null
     */
    public function getSearchString(): ?string
    {
        $search = $this->request->query('search');

        return strlen($search) <= 20 ? $search : substr($search, 0, 20);
    }

    /**
     * Add search clauses to the query builder
     *
     * @param Builder $query
     * @return Builder
     */
    public function search(Builder $query): Builder
    {
        $search = $this->getSearchString();

        $addSearchClause = function ($columns, $query) use ($search) {
            foreach ($columns as $i => $column) {
                $function = $i == 0 ? 'whereRaw' : 'orWhereRaw';

                [$clause, $args] = $column == 'id' || Str::endsWith($column, '.id')
                    ? [$column . ' = ?', [intval($search)]]
                    : ['LOWER(' . $column . ') LIKE ?', ['%' . strtolower($search) . '%']];

                $query->{$function}($clause, $args);
            }
        };

        // group all search criteria
        $query->where(function ($query) use ($addSearchClause) {
            collect($this->searchableColumns)->each(function ($columns, $relation) use ($query, $addSearchClause) {
                // join different search groups with OR
                $query->orWhere(function ($query) use ($relation, $addSearchClause, $columns) {
                    // add a relation if it's specified
                    if (is_string($relation)) {
                        $query->whereHas($relation, function ($query) use ($addSearchClause, $columns) { $addSearchClause($columns, $query); });
                    } else {
                        $addSearchClause($columns, $query);
                    }
                });
            });
        });

        return $query;
    }

    /**
     * Add where clauses to the query builder
     *
     * @param Builder $query
     * @return Builder
     */
    public function where(Builder $query): Builder
    {
        collect($this->whereClauses)->each(function ($clause) use ($query) {
            if (is_array($clause)) {
                $query->where(...$clause);
            // function
            } else {
                $query->where($clause);
            }
        });

        return $query;
    }

    /**
     * Get the base builder
     *
     * @return Builder
     */
    protected function getBaseBuilder(): Builder
    {
        return $this->model::query();
    }

    /**
     * Get query builder instance with pagination and order applied
     *
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        $builder = $this->getBaseBuilder();

        // apply query filters
        collect($this->filters)->each(function ($filters, $relation) use ($builder) {
            collect($filters)->each(function ($filterClass) use ($builder, $relation) {
                tap(new $filterClass(is_string($relation) ? $relation : NULL), function (Filter $filter) use ($builder) {
                    $builder->when($filter->getValue(), Closure::fromCallable([$filter, 'getQuery']));
                });
            });
        });

        return $builder
            ->when($this->hasWhereClauses(), [$this, 'where'])
            ->when($this->isSearchable(), [$this, 'search'])
            ->with($this->with)
            ->skip($this->getRowsToSkip())
            ->take($this->getItemsPerPage())
            ->orderByRaw($this->getOrderBy() . ' ' . $this->getOrderDirection());
    }

    /**
     * Get collection from the builder
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->getBuilder()->get();
    }

    public function hasWhereClauses(): bool
    {
        return !empty($this->whereClauses);
    }

    public function isSearchable(): bool
    {
        return $this->getSearchString() && !empty($this->searchableColumns);
    }

    public function addWhere($clauses): Query
    {
        $this->whereClauses[] = $clauses;

        return $this;
    }

    public function addFilters(array $filters): Query
    {
        $this->filters = $filters;

        return $this;
    }

    public function addOrderBy(array $sortableColumns): Query
    {
        $this->sortableColumns = $sortableColumns;

        return $this;
    }

    /**
     * Get total number of pages
     *
     * @return float
     */
    protected function getPagesCount(): int
    {
        return (int) ceil($this->getRowsCount() / $this->getItemsPerPage());
    }
}
