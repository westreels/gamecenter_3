<?php

namespace App\Helpers\Queries;

use App\Models\Job;

class JobQuery extends Query
{
    protected $model = Job::class;

    protected $searchableColumns = [['payload']];

    protected $sortableColumns = [
        'id'            => 'id',
        'created_ago'   => 'created_at',
    ];
}
