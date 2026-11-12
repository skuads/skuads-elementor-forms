<?php

defined('ABSPATH') or exit('Restricted access!');

if (! class_exists('SkuadsElementorSubmissionIdentity')) {
    class SkuadsElementorSubmissionIdentity
    {
        public static function submittedAt($value = null)
        {
            if (empty($value)) {
                return gmdate('Y-m-d H:i:s');
            }

            $timestamp = strtotime((string) $value);

            return $timestamp === false
                ? (string) $value
                : gmdate('Y-m-d H:i:s', $timestamp);
        }
    }
}
