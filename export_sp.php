<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$result = DB::select('SHOW CREATE PROCEDURE usp_get_data');
file_put_contents('sp_dump.sql', $result[0]->{'Create Procedure'});
echo "Dumped SP to sp_dump.sql\n";
