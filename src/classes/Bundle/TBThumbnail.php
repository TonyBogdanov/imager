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

class TBThumbnail extends TBCover
{
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
                        if('true' == $value) {
                            return true;
                        }
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
                    if(!is_bool($value) && (!is_string($value) || !is_file($value))) {
                        throw new \Exception('Value must either be false, or a path to a valid file');
                    }
                    return $value;
                },
            );

            $this->optionDefinitions    = array(
                'width'                 => array(
                    'description'       => 'Width of the thumbnail image (in pixels)',
                    'default'           => 80,
                    'filter'            => $filters['dimension'],
                    'validator'         => $validators['dimension'],
                ),
                'height'                => array(
                    'description'       => 'Height of the thumbnail image (in pixels)',
                    'default'           => 80,
                    'filter'            => $filters['dimension'],
                    'validator'         => $validators['dimension'],
                ),
                'background-color'      => array(
                    'description'       => 'Background color of the thumbnail image',
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
                'author-logo'           => array(
                    'description'       => 'Optional logo path of the item\'s author, set to false to disable or' .
                        ' true to use the default logo',
                    'default'           => true,
                    'filter'            => $filters['file'],
                    'validator'         => $validators['file'],
                ),
                'title'                 => array(
                    'description'       => 'Thumbnail title',
                    'default'           => '',
                    'validator'         => $validators['string'],
                ),
                'sub-title'             => array(
                    'description'       => 'Thumbnail sub-title',
                    'default'           => '',
                    'validator'         => $validators['string'],
                ),
            );
        }
        return $this->optionDefinitions;
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
        $this->sourceDir            = IMAGER_ABSPATH . 'bundles/thumbnail/';
        $this->targetDir            = realpath(dirname($environment['dest'])) . DIRECTORY_SEPARATOR;
        $this->targetFilename       = basename($environment['dest']);

        // Base dimensions (for ratio calculations)
        $baseWidth                  = 80;
        $baseHeight                 = 80;

        // Target dimensions
        $width                      = $this->getOption('width');
        $height                     = $this->getOption('height');
        $scale                      = sqrt(($width * $height) / ($baseWidth * $baseHeight));

        // Calculations
        $logoPadding                = 0.05 * $height;

        // Mountain
        $mountain                   = $this->command()
            ->create($width, $height)
            ->merge($this->command()
                    ->open($this->source('mountain.svg'))
                    ->resize($width)
                    ->colorify($this->getOption('mountain-color'))
                    ->save($this->temp('mountain.png')))
            ->align('left', 'bottom')
            ->save($this->temp('mountain.png'));

        // Logo
        $logo                       = $this->getOption('logo') ?
            $this->command()
                ->create($width, $height)
                ->merge($this->command()
                    ->open($this->source('logo-' . $this->getOption('logo') . '.svg'))
                    ->resize($width - $logoPadding, $height - $logoPadding)
                    ->save($this->temp('logo.png')))
                ->align('center', 'middle')
                ->save($this->temp('logo.png'))
            :
            $this->command()
                ->create($width, $height)
                ->save($this->temp('logo.png'));

        // Author logo
        $authorLogo                 = $this->getOption('author-logo');
        $authorLogo                 = true === $authorLogo ? $this->source('tb-logo.svg') : $authorLogo;
        $authorLogo                 = is_string($authorLogo) ?
            $this->command()
                ->open($authorLogo)
                ->resize($width * 0.125, $height * 0.125)
                ->save($this->temp('author-logo.png'))
            :
            $this->command()
                ->create($width * 0.125, $height * 0.125)
                ->save($this->temp('author-logo.png'));

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
                ->mask($this->command()
                    ->open($mountain)
                    ->makeMask($this->temp('logo-down-mask.png')))
                ->flush()
                ->colorify([255, 255, 255, 0.15])
                ->save($this->temp('logo-down.png')))
            ->flush()

            ->merge($authorLogo)
            ->align('right', 'bottom')
            ->offset($width * 0.0375, $height * 0.0375)
            ->flush()

            ->merge($this->command()
                ->background()
                ->fill('#fff')
                ->label(strtolower($this->getOption('title')), 14 * $scale, 'C:\Windows\Fonts\gra.ttf')
                ->save($this->temp('title.png')))
            ->align('center', 'middle')
            ->offset(0, -6 * $scale)
            ->flush()

            ->merge($this->command()
                ->background()
                ->fill('#fff')
                ->label(strtoupper($this->getOption('sub-title')), 6 * $scale, 'C:\Windows\Fonts\arialb.ttf')
                ->save($this->temp('sub-title.png')))
            ->align('center', 'middle')
            ->offset(0, 6 * $scale)
            ->flush()

            ->mask($this->command()
                ->open($this->source('mask.svg'))
                ->resize($width, $height)
                ->makeMask($this->temp('mask.png')))
            ->save($this->target());

        return $this;
    }
}