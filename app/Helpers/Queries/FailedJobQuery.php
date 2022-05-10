<?php

namespace App\Helpers\Queries;

use App\Models\FailedJob;

class FailedJobQuery extends Query
{
    protected $model = FailedJob::class;

    protected $searchableColumns = [['payload', 'exception']];

    protected $sortableColumns = [
        'id'            => 'id',
        'failed_ago'    => 'failed_at',
    ];
}
