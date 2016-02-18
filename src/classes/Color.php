<?php
/**
 * This file is part of the Imager package.
 *
 * (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Imager;

class Color
{
    /**
     * @var float
     */
    protected $red;

    /**
     * @var float
     */
    protected $green;

    /**
     * @var float
     */
    protected $blue;

    /**
     * @var float
     */
    protected $alpha;

    /**
     * @var bool
     */
    protected $none;

    /**
     * @param $value
     * @return bool|Color
     * @throws \Exception
     */
    public static function parse($value)
    {
        switch(true) {
            case is_object($value) && $value instanceof self:
                return $value;

            case is_array($value):
                $color          = self::parseRGBArray($value);
                if(false !== $color) {
                    return $color;
                }

                throw new \Exception(sprintf('Unable to parse color from array: %s', var_export($value, true)));
                break;

            case is_string($value):
                if('none' == strtolower($value)) {
                    return new self(0, 0, 0, 0, true);
                }

                $color          = self::parseHEXString($value);
                if(false !== $color) {
                    return $color;
                }

                $color          = self::parseRGBString($value);
                if(false !== $color) {
                    return $color;
                }

                throw new \Exception(sprintf('Unable to parse color from string: %s', var_export($value, true)));
                break;

            default:
                throw new \Exception(sprintf('Unable to parse color from type: %s', gettype($value)));
        }
    }

    /**
     * @param array $value
     * @return bool|Color
     */
    public static function parseRGBArray(array $value)
    {
        $value                  = array_values($value);

        if(3 == count($value)) {
            return new self(
                max(0, min(1, $value[0])),
                max(0, min(1, $value[1])),
                max(0, min(1, $value[2]))
            );
        } else if(4 == count($value)) {
            return new self(
                max(0, min(1, $value[0])),
                max(0, min(1, $value[1])),
                max(0, min(1, $value[2])),
                max(0, min(1, $value[3]))
            );
        }

        return false;
    }

    /**
     * @param $value
     * @return bool|Color
     */
    public static function parseHEXString($value)
    {
        $value                  = preg_replace('/[^0-9a-f]+/', '', strtolower($value));

        if(3 == strlen($value)) {
            return new self(
                max(0, min(1, hexdec(substr($value, 0, 1) . substr($value, 0, 1)) / 255)),
                max(0, min(1, hexdec(substr($value, 1, 1) . substr($value, 1, 1)) / 255)),
                max(0, min(1, hexdec(substr($value, 2, 1) . substr($value, 2, 1)) / 255))
            );
        } else if(4 == strlen($value)) {
            return new self(
                max(0, min(1, hexdec(substr($value, 0, 1) . substr($value, 0, 1)) / 255)),
                max(0, min(1, hexdec(substr($value, 1, 1) . substr($value, 1, 1)) / 255)),
                max(0, min(1, hexdec(substr($value, 2, 1) . substr($value, 2, 1)) / 255)),
                max(0, min(1, hexdec(substr($value, 3, 1) . substr($value, 3, 1)) / 255))
            );
        } else if(6 == strlen($value)) {
            return new self(
                max(0, min(1, hexdec(substr($value, 0, 2)) / 255)),
                max(0, min(1, hexdec(substr($value, 2, 2)) / 255)),
                max(0, min(1, hexdec(substr($value, 4, 2)) / 255))
            );
        } else if(8 == strlen($value)) {
            return new self(
                max(0, min(1, hexdec(substr($value, 0, 2)) / 255)),
                max(0, min(1, hexdec(substr($value, 2, 2)) / 255)),
                max(0, min(1, hexdec(substr($value, 4, 2)) / 255)),
                max(0, min(1, hexdec(substr($value, 6, 2)) / 255))
            );
        }

        return false;
    }

    /**
     * @param $value
     * @return bool|Color
     */
    public static function parseRGBString($value)
    {
        $number                 = '(?=.)(?:[+-]?(?:[0-9]*)(?:\.([0-9]+))?)';

        if(preg_match('/^\h*rgb\h*\(\h*(?P<r>' . $number . ')\h*,\h*(?P<g>' . $number . ')\h*,\h*(?P<b>' .
            $number . ')\h*\)\h*$/i', $value, $match)) {
            return new self(
                max(0, min(1, (int) $match['r'] / 255)),
                max(0, min(1, (int) $match['g'] / 255)),
                max(0, min(1, (int) $match['b'] / 255))
            );
        } else if(preg_match('/^\h*rgba\h*\(\h*(?P<r>' . $number . ')\h*,\h*(?P<g>' . $number . ')\h*,\h*(?P<b>' .
            $number . ')\h*,\h*(?P<a>' . $number . ')\h*\)\h*$/i', $value, $match)) {
            return new self(
                max(0, min(1, (int) $match['r'] / 255)),
                max(0, min(1, (int) $match['g'] / 255)),
                max(0, min(1, (int) $match['b'] / 255)),
                max(0, min(1, (float) $match['a']))
            );
        }

        return false;
    }

    /**
     * Color constructor.
     * @param float $red
     * @param float $green
     * @param float $blue
     * @param float $alpha
     * @param bool $none
     */
    public function __construct($red, $green, $blue, $alpha = 1.0, $none = false)
    {
        $this->setRed($red);
        $this->setGreen($green);
        $this->setBlue($blue);
        $this->setAlpha($alpha);
        $this->setNone($none);
    }


    /**
     * @return array
     */
    public function getRGBArray()
    {
        return [$this->getRed(), $this->getGreen(), $this->getBlue()];
    }

    /**
     * @return array
     */
    public function getRGBAArray()
    {
        return [$this->getRed(), $this->getGreen(), $this->getBlue(), $this->getAlpha()];
    }

    /**
     * @return string
     */
    public function getRGBString()
    {
        return 'rgb(' . round($this->getRed() * 255) . ', ' . round($this->getGreen() * 255) . ', ' .
        round($this->getBlue() * 255) . ')';
    }

    /**
     * @return string
     */
    public function getRGBAString()
    {
        return 'rgb(' . round($this->getRed() * 255) . ', ' . round($this->getGreen() * 255) . ', ' .
        round($this->getBlue() * 255) . ', ' . $this->getAlpha() . ')';
    }

    /**
     * @return string
     */
    public function getHEXString()
    {
        return '#' . str_pad(dechex($this->getRed() * 255), 2, '0', STR_PAD_LEFT) .
        str_pad(dechex($this->getGreen() * 255), 2, '0', STR_PAD_LEFT) .
        str_pad(dechex($this->getBlue() * 255), 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return string
     */
    public function getHEXAString()
    {
        return '#' . str_pad(dechex($this->getRed() * 255), 2, '0', STR_PAD_LEFT) .
        str_pad(dechex($this->getGreen() * 255), 2, '0', STR_PAD_LEFT) .
        str_pad(dechex($this->getBlue() * 255), 2, '0', STR_PAD_LEFT) .
        str_pad(dechex(round($this->getAlpha() * 255)), 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return float
     */
    public function getRed()
    {
        return $this->red;
    }

    /**
     * @param float $red
     * @return Color
     */
    public function setRed($red)
    {
        $this->red = $red;
        return $this;
    }

    /**
     * @return float
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * @param float $green
     * @return Color
     */
    public function setGreen($green)
    {
        $this->green = $green;
        return $this;
    }

    /**
     * @return float
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * @param float $blue
     * @return Color
     */
    public function setBlue($blue)
    {
        $this->blue = $blue;
        return $this;
    }

    /**
     * @return float
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * @param float $alpha
     * @return Color
     */
    public function setAlpha($alpha)
    {
        $this->alpha = $alpha;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNone()
    {
        return $this->none;
    }

    /**
     * @param boolean $none
     * @return Color
     */
    public function setNone($none)
    {
        $this->none = $none;
        return $this;
    }
}