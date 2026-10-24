<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('nota_number')->nullable()->unique()->after('submission_number');

            $table->string('divisi_name')->nullable()->after('activity_name');

            $table->date('meeting_date')->nullable()->after('divisi_name');
            $table->string('circular_letter')->nullable()->after('meeting_date');
            $table->string('budget_code')->nullable()->after('circular_letter');

            $table->longText('background')->nullable()->after('description');
            $table->json('objectives')->nullable()->after('background');
            $table->json('benefits')->nullable()->after('objectives');
            $table->longText('target_participants')->nullable()->after('benefits');

            $table->string('event_day')->nullable()->after('target_participants');
            $table->date('event_date')->nullable()->after('event_day');
            $table->time('event_start_time')->nullable()->after('event_date');
            $table->time('event_end_time')->nullable()->after('event_start_time');
            $table->string('event_location')->nullable()->after('event_end_time');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique(['nota_number']);

            $table->dropColumn([
                'nota_number',
                'activity_theme',
                'divisi_name',
                'meeting_date',
                'circular_letter',
                'budget_code',
                'background',
                'objectives',
                'benefits',
                'target_participants',
                'event_day',
                'event_date',
                'event_start_time',
                'event_end_time',
                'event_location',
            ]);
        });
    }
};