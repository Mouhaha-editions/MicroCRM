<?php

namespace CoreBundle\Service;

class VersionService
{

    public static function get()
    {
        $commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));
        $version = trim(exec('git describe --abbrev=0 --tags'));

        $commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
        $commitDate->setTimezone(new \DateTimeZone('UTC'));

        return sprintf('%s-dev.%s (%s)', $version, $commitHash, $commitDate->format('Y-m-d H:i:s'));
    }

    public static function getProd()
    {
        $version = trim(exec('git describe --abbrev=0 --tags'));

        return sprintf('%s', $version);
    }


}