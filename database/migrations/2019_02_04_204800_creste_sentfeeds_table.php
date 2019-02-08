<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CresteSentfeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_feeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('FeedSubmissionId');
            $table->string('FeedType');
            $table->timestamp('SubmittedDate');
            $table->string('FeedProcessingStatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
