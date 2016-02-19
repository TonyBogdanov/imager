<?php
/**
 * This file is part of the Imager package.
 *
 * (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Imager\Bundle;

class TBCover extends AbstractBundle
{
    /**
     * @var string
     */
    protected $sourceDir                = null;

    /**
     * @var string
     */
    protected $targetDir                = null;

    /**
     * @var string
     */
    protected $targetFilename           = null;

    /**
     * @var array
     */
    protected $temps                    = [];

    /**
     * @param $name
     * @param null $source
     * @return string
     */
    protected function temp($name, $source = null)
    {
        $temp                           = $this->target('--' . substr(md5($name), 0, 16) . '.' .
            pathinfo($name, PATHINFO_EXTENSION));

        if(isset($source) && !is_file($temp)) {
            @copy($source, $temp);
        }

        $this->temps[]                  = $temp;
        return $temp;
    }

    /**
     * @param $path
     * @return string
     */
    protected function source($path)
    {
        return $this->temp('source-' . basename($path), $this->sourceDir . $path);
    }

    /**
     * @param null $path
     * @return string
     */
    protected function target($path = null)
    {
        return $this->targetDir . (isset($path) ? $path : $this->targetFilename);
    }

    /**
     * @param $value
     * @return string
     */
    protected function geometry($value)
    {
        return 0 <= $value ? '+' . $value : $value;
    }

    /**
     * @param $version
     * @return string
     * @throws \Exception
     */
    protected function getVersionQR($version)
    {
        $parts                          = explode('.', $version);
        if(4 < count($parts)) {
            throw new \Exception('Only versions with up to 4 parts are supported');
        }

        $parts                          = array_map(function($value) {
            $value                      = (int) $value;

            if(0 > $value) {
                throw new \Exception('All version numbers must be positive');
            }
            if(31 < $value) {
                throw new \Exception('All version numbers must be less than 32');
            }

            return $value;
        }, $parts);

        $decimal                        = 0;
        foreach($parts as $part) {
            $decimal                    *= 32;
            $decimal                    += $part;
        }

        return str_pad(decbin($decimal), 20, '0', STR_PAD_LEFT);
    }

    /**
     * @return array
     */
    protected function getOptionDefinitions()
    {
        if(!isset($this->optionDefinitions)) {
            $filters                    = array(
                'color'                 => function($value) {
                    return '#' . strtolower(preg_replace('/[^0-9a-f]+/i', '', (string) $value));
                },
                'dimension'             => function($value) {
                    return (int) $value;
                },
                'file'                  => function($value) {
                    if(false === $value) {
                        return false;
                    } else if(is_string($value)) {
                        if('false' == $value) {
                            return false;
                        }

                        $realPath       = realpath($value);
                        if(false !== $realPath) {
                            return $realPath;
                        }
                    }
                    return $value;
                }
            );

            $validators                 = array(
                'color'                 => function($value) {
                    if(!preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/', $value)) {
                        throw new \Exception('Value must be a valid HEX color in one of the following formats:' .
                            ' #01e, #01ef, #0001ee or #0001eeff');
                    }
                    return $value;
                },
                'dimension'             => function($value) {
                    if(0 >= $value || 4096 < $value) {
                        throw new \Exception('Value must be between 1 and 4096');
                    }
                    return $value;
                },
                'string'                => function($value) {
                    if(!is_string($value)) {
                        throw new \Exception('Value must be a valid string');
                    }
                    return $value;
                },
                'file'                  => function($value) {
                    if(false !== $value && (!is_string($value) || !is_file($value))) {
                        throw new \Exception('Value must either be false, or a path to a valid file');
                    }
                    return $value;
                },
            );

            $this->optionDefinitions    = array(
                'width'                 => array(
                    'description'       => 'Width of the cover image (in pixels)',
                    'default'           => 590,
                    'filter'            => $filters['dimension'],
                    'validator'         => $validators['dimension'],
                ),
                'height'                => array(
                    'description'       => 'Height of the cover image (in pixels)',
                    'default'           => 300,
                    'filter'            => $filters['dimension'],
                    'validator'         => $validators['dimension'],
                ),
                'background-color'      => array(
                    'description'       => 'Background color of the cover image',
                    'default'           => '#1e2627',
                    'filter'            => $filters['color'],
                    'validator'         => $validators['color'],
                ),
                'mountain-color'        => array(
                    'description'       => 'Color of the mountain fragment',
                    'default'           => '#00cae9',
                    'filter'            => $filters['color'],
                    'validator'         => $validators['color'],
                ),
                'version-color'         => array(
                    'description'       => 'Color of the version number pad',
                    'default'           => '#00cae9',
                    'filter'            => $filters['color'],
                    'validator'         => $validators['color'],
                ),
                'logo'                  => array(
                    'description'       => 'Logo to use (can be "html", "wordpress", "polymer" or false)',
                    'default'           => 'html',
                    'validator'         => function($value) {
                        if(!in_array($value, array('html', 'wordpress', 'polymer', false), true)) {
                            throw new \Exception('Value must be one of "html", "wordpress", "polymer" or false');
                        }
                        return $value;
                    },
                ),
                'desktop-screenshot'    => array(
                    'description'       => 'Valid screenshot image path to be used as "how your item looks' .
                        ' on desktop"',
                    'default'           => false,
                    'filter'            => $filters['file'],
                    'validator'         => $validators['file'],
                ),
                'desktop-background'    => array(
                    'description'       => 'Desktop screenshot background color to fill any areas not covered' .
                        ' by the image',
                    'default'           => '#fff',
                    'filter'            => $filters['color'],
                    'validator'         => $validators['color'],
                ),
                'mobile-screenshot'     => array(
                    'description'       => 'Valid screenshot image path to be used as "how your item looks' .
                        ' on mobile"',
                    'default'           => false,
                    'filter'            => $filters['file'],
                    'validator'         => $validators['file'],
                ),
                'mobile-background'     => array(
                    'description'       => 'Mobile screenshot background color to fill any areas not covered' .
                        ' by the image',
                    'default'           => '#fff',
                    'filter'            => $filters['color'],
                    'validator'         => $validators['color'],
                ),
                'title'                 => array(
                    'description'       => 'Cover title',
                    'default'           => '',
                    'validator'         => $validators['string'],
                ),
                'sub-title'             => array(
                    'description'       => 'Cover sub-title',
                    'default'           => '',
                    'validator'         => $validators['string'],
                ),
                'footer'                => array(
                    'description'       => 'Cover footer',
                    'default'           => '',
                    'validator'         => $validators['string'],
                ),
                'version'               => array(
                    'description'       => 'Cover version',
                    'default'           => '1.0',
                    'validator'         => $validators['string'],
                ),
                'alert'                 => array(
                    'description'       => 'Cover alert (e.g. new feature alert)',
                    'default'           => '',
                    'validator'         => $validators['string'],
                ),
            );
        }
        return $this->optionDefinitions;
    }

    public function __destruct()
    {
        foreach(array_unique($this->temps) as $temp) {
            @unlink($temp);
        }
    }

    /**
     * @param array $environment
     * @return $this
     * @throws BundleOptionDoesNotExistException
     * @throws BundleOptionHasNoValueException
     * @throws \Exception
     */
    public function execute(array $environment = array())
    {
        if(false === realpath(dirname($environment['dest']))) {
            throw new \Exception('Path "' . dirname($environment['dest']) . '" must be a valid directory');
        }

        // Define paths
        $this->sourceDir            = IMAGER_ABSPATH . 'bundles/cover/';
        $this->targetDir            = realpath(dirname($environment['dest'])) . DIRECTORY_SEPARATOR;
        $this->targetFilename       = basename($environment['dest']);

        // Base dimensions (for ratio calculations)
        $baseWidth                  = 590;
        $baseHeight                 = 300;

        // Target dimensions
        $width                      = $this->getOption('width');
        $height                     = $this->getOption('height');
        $scale                      = sqrt(($width * $height) / ($baseWidth * $baseHeight));

        // Calculations
        $mountainOffset             = 50 * $height / $baseHeight;
        $logoPadding                = 0.05 * $height;
        $desktopWidth               = 340 * $width / $baseWidth;
        $desktopInnerWidth          = $desktopWidth * (3072 / 3379);
        $desktopPadding             = 5 * $width / $baseWidth;
        $desktopInnerPadding        = [$desktopWidth * 153 / 3379, $desktopWidth * 153 / 3379];
        $mobileWidth                = 165 * $width / $baseWidth;
        $mobileInnerWidth           = $mobileWidth * (970 / 1433);
        $mobilePadding              = [-20 * $width / $baseWidth, 100 * $width / $baseWidth];
        $mobileInnerPadding         = [$mobileWidth * 244 / 1433, $mobileWidth * 496 / 1433];
        $textWidth                  = ($width - $mobileWidth) / 2 - 72 * $scale;

        // Mountain
        $mountain                   = $this->command()
            ->create($width, $height)
            ->merge(
                $this->command()
                    ->open($this->source('mountain.svg'))
                    ->resize($width)
                    ->colorify($this->getOption('mountain-color'))
                    ->save($this->temp('mountain.png'))
            )->align('left', 'bottom')
            ->offset(0, $mountainOffset)
            ->flush()

            ->fill($this->getOption('mountain-color'))
            ->rectangle(0, $height - $mountainOffset, $width, $height)
            ->save($this->temp('mountain.png'));

        // Logo
        $logo                       = $this->getOption('logo') ?
            $this->command()
                ->create($width, $height)
                ->merge(
                    $this->command()
                        ->open($this->source('logo-' . $this->getOption('logo') . '.svg'))
                        ->resize($height - $logoPadding, $height - $logoPadding)
                        ->save($this->temp('logo.png'))
                )->align('left', 'middle')
                ->offset($logoPadding, 0)
                ->save($this->temp('logo.png'))
            :
            $this->command()
                ->create($width, $height)
                ->save($this->temp('logo.png'));

        // Desktop
        $desktop                    = $this->getOption('desktop-screenshot') ?
            $this->command()
                ->create($width, $height)
                ->merge($this->command()
                    ->create($desktopWidth, $height)
                    ->merge($this->command()
                        ->open($this->source('desktop-shadow.png'))
                        ->resize($desktopWidth)
                        ->save($this->temp('desktop-shadow.png')))
                    ->flush()
                    ->merge($this->command()
                        ->create($desktopWidth, $height)
                        ->merge($this->command()
                            ->open($this->getOption('desktop-screenshot'))
                            ->resize($desktopInnerWidth)
                            ->flush()
                            ->background($this->getOption('desktop-background'))
                            ->extent(null, $height)
                            ->save($this->temp('desktop-screenshot.png')))
                        ->offset($desktopInnerPadding[0], $desktopInnerPadding[1])
                        ->flush()
                        ->mask($this->command()
                            ->open($this->source('desktop-mask.png'))
                            ->resize($desktopWidth)
                            ->save($this->temp('desktop-mask.png')))
                        ->save($this->temp('desktop-screenshot-mask.png')))
                    ->save($this->temp('desktop-raw.png')))
                ->align('right', 'top')
                ->offset($desktopPadding, $desktopPadding)
                ->save($this->temp('desktop.png'))
            :
            $this->command()
                ->create($width, $height)
                ->save($this->temp('desktop.png'));

        // Mobile
        $mobile                     = $this->getOption('mobile-screenshot') ?
            $this->command()
                ->create($width, $height)
                ->merge($this->command()
                    ->create($mobileWidth, $height)
                    ->merge($this->command()
                        ->open($this->source('mobile-shadow.png'))
                        ->resize($mobileWidth)
                        ->save($this->temp('mobile-shadow.png')))
                    ->flush()
                    ->merge($this->command()
                        ->create($mobileWidth, $height)
                        ->merge($this->command()
                            ->open($this->getOption('mobile-screenshot'))
                            ->resize($mobileInnerWidth)
                            ->flush()
                            ->background($this->getOption('mobile-background'))
                            ->extent(null, $height)
                            ->save($this->temp('mobile-screenshot.png')))
                        ->offset($mobileInnerPadding[0], $mobileInnerPadding[1])
                        ->flush()
                        ->mask($this->command()
                            ->open($this->source('mobile-mask.png'))
                            ->resize($mobileWidth)
                            ->save($this->temp('mobile-mask.png')))
                        ->save($this->temp('mobile-screenshot-mask.png')))
                    ->flush()
                    ->merge($this->command()
                        ->open($this->source('mobile-screen.png'))
                        ->resize($mobileWidth)
                        ->save($this->temp('mobile-screen.png')))
                    ->save($this->temp('mobile-raw.png')))
                ->align('center', 'top')
                ->offset($mobilePadding[0], $mobilePadding[1])
                ->save($this->temp('mobile.png'))
            :
            $this->command()
                ->create($width, $height)
                ->save($this->temp('mobile.png'));

        // Version
        $this->command()
            ->background($this->getOption('version-color'))
            ->fill($this->getOption('background-color'))
            ->label($this->getOption('version'), 7 * $scale, 'C:\Windows\Fonts\verdanab.ttf')
            ->save($this->temp('version.png'));

        list($versionWidth,
            $versionHeight)         = getimagesize($this->temp('version.png'));

        $versionLeft                = $this->command()
            ->open($this->source('version-pad.svg'))
            ->resize(null, $versionHeight + 4 * $scale)
            ->colorify($this->getOption('version-color'))
            ->save($this->temp('version-left.png'));

        $versionRight               = $this->command()
            ->open($this->source('version-pad.svg'))
            ->resize(null, $versionHeight + 4 * $scale)
            ->colorify($this->getOption('version-color'))
            ->flop()
            ->save($this->temp('version-right.png'));

        // Version QR
        $versionQR                  = $this->command()->create(20, 1, '#fff')->fill('#000');
        for($i = 0, $s = $this->getVersionQR($this->getOption('version')); $i < 20; $i++) {
            if('1' == $s[$i]) {
                $versionQR->rectangle($i, 0, $i, 1);
            }
        }
        $versionQR                  = $versionQR->save($this->temp('version-qr.png'));

        // Alert
        $alert                      = 0 < strlen($this->getOption('alert')) ?
            $this->command()
                ->background('#f00')
                ->fill('#fff')
                ->label(strtoupper($this->getOption('alert')), 11 * $scale, 'C:\Windows\Fonts\gra.ttf')
                ->save($this->temp('alert.png'))
            :
            $this->command()
                ->create(1, 1)
                ->save($this->temp('alert.png'));

        list($alertWidth,
            $alertHeight)           = getimagesize($alert);

        $alert                      = 0 < strlen($this->getOption('alert')) ?
            $this->command()
                ->open($alert)
                ->background('#f00')
                ->extent($alertWidth + 12 * $scale, $alertHeight + 8 * $scale)
                ->align('center', 'middle')
                ->save($this->temp('alert.png'))
            : $alert;

        // Final composition
        $this->command()
            ->create($width, $height, $this->getOption('background-color'))
            ->flush()

            ->merge($mountain)
            ->flush()

            ->merge($this->command()
                ->open($logo)
                ->mask($this->command()
                    ->open($mountain)
                    ->makeMask($this->temp('logo-up-mask.png'), true))
                ->flush()
                ->colorify([255, 255, 255, 0.02])
                ->save($this->temp('logo-up.png')))
            ->flush()

            ->merge($this->command()
                ->open($logo)
                ->mask($mountain)
                ->flush()
                ->colorify([255, 255, 255, 0.15])
                ->save($this->temp('logo-down.png')))
            ->flush()

            ->merge($desktop)
            ->flush()

            ->merge($mobile)
            ->flush()

            ->merge($this->command()
                ->background()
                ->fill('#fff')
                ->text($this->getOption('title'), 26 * $scale, $textWidth, 'C:\Windows\Fonts\gra.ttf')
                ->save($this->temp('title.png')))
            ->align('left', 'top')
            ->offset(36 * $scale, 57 * $scale)
            ->flush()

            ->merge($this->command()
                ->background()
                ->fill('#777')
                ->text($this->getOption('sub-title'), 12 * $scale, $textWidth, 'C:\Windows\Fonts\gra.ttf')
                ->save($this->temp('sub-title.png')))
            ->align('left', 'middle')
            ->offset(36 * $scale, 0)
            ->flush()

            ->merge($this->command()
                ->background()
                ->fill('#fff')
                ->text(strtoupper($this->getOption('footer')), 7 * $scale, $textWidth, 'C:\Windows\Fonts\verdanab.ttf')
                ->save($this->temp('footer.png')))
            ->align('left', 'middle')
            ->offset(36 * $scale, $height / 2 - $mountainOffset * 0.6)
            ->flush()

            ->merge($this->command()
                ->create($versionWidth + 3 * $scale, $versionHeight + 4 * $scale, $this->getOption('version-color'))
                ->merge($this->temp('version.png'))
                ->align('center', 'middle')
                ->flush()

                ->extent($versionWidth + 3 * $scale + getimagesize($versionLeft)[0] + getimagesize($versionRight)[0],
                    $versionHeight + 4 * $scale)
                ->align('center')
                ->flush()

                ->merge($versionLeft)
                ->align('left', 'top')
                ->flush()

                ->merge($versionRight)
                ->align('right', 'top')
                ->save($this->temp('version-padded.png')))
            ->align('left', 'top')
            ->offset(36 * $scale, 95 * $scale)
            ->flush()

            ->merge($versionQR)
            ->align('left', 'bottom')
            ->flush()

            ->merge($alert)
            ->align('right', 'top')
            ->offset(0, 20 * $scale - getimagesize($alert)[1] / 2)

            ->save($this->target());

        return $this;
    }
}