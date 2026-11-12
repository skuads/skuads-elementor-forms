<?php

define('ABSPATH', __DIR__);

require_once __DIR__.'/../skuads-elementor-forms/includes/class-skuads-elementor-submission-identity.php';

if (SkuadsElementorSubmissionIdentity::submittedAt('2026-11-12T19:16:39+00:00') !== '2026-11-12 19:16:39') {
    fwrite(STDERR, 'Submission timestamps must be normalised to UTC.'.PHP_EOL);
    exit(1);
}

if (! preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', SkuadsElementorSubmissionIdentity::submittedAt())) {
    fwrite(STDERR, 'Live submissions must receive a stable UTC timestamp format.'.PHP_EOL);
    exit(1);
}

echo "Submission identity tests passed.\n";
