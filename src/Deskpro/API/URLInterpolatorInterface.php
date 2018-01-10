<?php
namespace Deskpro\API;

/**
 * Modifies URLs by adding query strings and interpolates {placeholders}.
 */
interface URLInterpolatorInterface
{
    /**
     * Replaces {placeholders} in the given URL with param values, and adds a query string
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public function interpolate($url, array $params);
}