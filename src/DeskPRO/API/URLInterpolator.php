<?php
namespace DeskPRO\API;

/**
 * Modifies URLs by adding query strings and interpolates {placeholders}.
 */
class URLInterpolator implements URLInterpolatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpolate($url, array $params)
    {
        foreach($params as $key => $value) {
            if (is_scalar($key) && is_scalar($value)) {
                if (preg_match('/{' . preg_quote($key) . '}/', $url, $matches)) {
                    $url = str_replace($matches[0], $value, $url);
                    unset($params[$key]);
                }
            }
        }
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
}