<?php

namespace App\Helpers\Queries;

use App\Helpers\Queries\Filters\PeriodFilter;
use App\Models\ChatMessage;

class ChatMessageQuery extends Query
{
    protected $model = ChatMessage::class;

    protected $with = ['room', 'user'];

    protected $filters = [[PeriodFilter::class]];

    protected $searchableColumns = [['id', 'message'], 'user' => ['name', 'email']];

    protected $sortableColumns = [
        'id'            => 'id',
        'created_ago'   => 'created_at',
    ];
}
