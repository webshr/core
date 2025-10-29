<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Asset;

class Text_Asset extends Asset
{
    /**
     * Character encoding
     *
     * @var string
     */
    protected $charset;
    /**
     * Get character encoding.
     *
     * @param  string  $fallback  Fallback if charset cannot be determined
     */
    public function charset($fallback = 'UTF-8'): string
    {
        if ($this->charset) {
            return $this->charset;
        }

        if (preg_match('//u', $this->contents())) {
            return $this->charset = 'UTF-8';
        }

        if (function_exists('mb_detect_encoding')) {
            return $this->charset = mb_detect_encoding($this->contents()) ?: $fallback;
        }

        return $this->charset = $fallback;
    }

    /**
     * Get data URL of asset.
     *
     * @param  string  $mediatype  MIME content type
     * @param  string  $charset  Character encoding
     * @param  string  $urlencode  List of characters to be percent-encoded
     */
    public function data_url(?string $mediatype = null, ?string $charset = null, string $urlencode = '%\'"'): string
    {
        if ($this->data_url) {
            return $this->data_url;
        }

        if (! $mediatype) {
            $mediatype = $this->content_type();
        }

        if (! strstr($mediatype, 'charset')) {
            $mediatype .= ';charset=' . ($charset ?: $this->charset());
        }

        $percents = [];
        foreach (preg_split('//u', $urlencode, -1, PREG_SPLIT_NO_EMPTY) as $char) {
            $percents[$char] = rawurlencode($char);
        }

        $data = strtr($this->contents(), $percents);
        return $this->data_url = "data:{$mediatype},{$data}";
    }

    /**
     * Get data URL of asset.
     *
     * @param  string  $mediatype  MIME content type
     * @param  string  $charset  Character encoding
     * @param  string  $urlencode  List of characters to be percent-encoded
     */
    public function data_uri(?string $mediatype = null, ?string $charset = null, string $urlencode = '%\'"'): string
    {
        return $this->data_url($mediatype, $charset, $urlencode);
    }
}
