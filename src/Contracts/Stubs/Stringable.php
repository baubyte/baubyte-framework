<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 * @link https://github.com/symfony/polyfill-php80/blob/7a6ff3f1959bb01aefccb463a0f2cd3d3d2fd936/LICENSE
 */
if (\PHP_VERSION_ID < 80000 && !interface_exists('Stringable')) {
    interface Stringable {
        /**
         * @return string
         */
        public function __toString();
    }
}
