<?php

error_reporting(E_STRICT);

require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once __DIR__ . '/MockPDO.php';
require_once __DIR__ . '/MockPDOStatement.php';
require_once __DIR__ . '/MockDifferentPDOStatement.php';
require_once __DIR__ . '/MockDifferentPDO.php';

require_once __DIR__ . '/ModelsForTesting.php';

require_once __DIR__ . '/CouponingApi.php';
require_once __DIR__ . '/CouponingApiSpecialProducts.php';
require_once __DIR__ . '/CouponingApiUniversalProducts.php';
