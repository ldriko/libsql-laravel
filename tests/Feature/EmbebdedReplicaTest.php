<?php

/*
NOTE:
    Before activated this tests, make sure you've libsql server or turso url provider
    is setup in TestCase.php file in getEnvironmentSetUp methods for Embedded Replica Connection
*/

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Libsql\Laravel\Tests\Fixtures\Models\Project;

beforeEach(function () {
    if (shouldSkipTests()) {
        $this->markTestSkipped('This test skipped by default because it need a running libsql server');
    }
    clearDirectory();
    sleep(2);
    DB::setDefaultConnection('otherdb2');
});

test('it can connect to a embedded replica', function () {
    DB::setDefaultConnection('otherdb2');
    $mode = DB::connection('otherdb2')->getConnectionMode();
    expect($mode)->toBe('remote_replica');
})->group('EmbeddedReplicaTest', 'FeatureTest');

test('it can get all rows from the projects table through the embedded replica', function () {
    DB::setDefaultConnection('otherdb2');
    Schema::dropAllTables();
    migrateTables('projects');

    $this->project1 = Project::make()->setConnection('otherdb2')->factory()->create();
    $this->project2 = Project::make()->setConnection('otherdb2')->factory()->create();
    $this->project3 = Project::make()->setConnection('otherdb2')->factory()->create();
    $projects = DB::connection('otherdb2')->table('projects')->get();
    expect($projects->count())->toBe(3);
    clearDirectory();
})->group('EmbeddedReplicaTest', 'FeatureTest');
