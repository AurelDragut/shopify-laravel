<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentFeed extends Model
{
    protected $fillable = ['FeedSubmissionId', 'FeedType', 'SubmittedDate','FeedProcessingStatus'];
    protected $table = 'sent_feeds';
}
