<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Asset;

use Webshr\Core\Support\Contracts\Arrayable;
use Webshr\Core\Support\Contracts\Jsonable;

class Json_Asset extends Text_Asset implements Arrayable, Jsonable
{
    /**
     * {@inheritdoc}
     */
    public function to_json($options = \JSON_UNESCAPED_SLASHES)
    {
        return json_encode($this->to_array(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function to_array(): array
    {
        return (array) $this->decode(JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Decode JSON data.
     *
     * @param  int  $options
     * @param  int  $depth
     * @return array|null
     */
    public function decode($options = 0, $depth = 512)
    {
        return json_decode($this->contents(), null, $depth, $options);
    }
}
