<?php

$app->mount('/api', new GW2Spidy\API\OldAPIControllerProvider());
$app->mount('/api/v0.9', new GW2Spidy\API\v090APIControllerProvider());
