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

class TBPlaceholder extends TBCover
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
                }
            );

            $validators                 = array(
                'color'                 => function($value) {
                    if(!preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/', $value)) {
                        throw new \Exception('Value must be a valid HEX color in one of the following formats:' .
                            ' #01e, #01ef, #0001ee or #0001eeff');
                    }
                    return $value;
                }
            );

            $this->optionDefinitions    = array(
                'background-color'      => array(
                    'description'       => 'Background color of the generated placeholder image',
                    'default'           => '#ddd',
                    'filter'            => $filters['color'],
                    'validator'         => $validators['color'],
                ),
                'text-color'            => array(
                    'description'       => 'Color of the generated placeholder image text',
                    'default'           => '#999',
                    'filter'            => $filters['color'],
                    'validator'         => $validators['color'],
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
        // Dest should be pointed to the image to be placeholdized
        if(false === realpath($environment['dest'])) {
            throw new \Exception('Path "' . $environment['dest'] . '" must be a valid file');
        }
        $environment['dest']        = realpath($environment['dest']);

        // Get image dimensions
        $imageInfo                  = getimagesize($environment['dest']);
        if(false === $imageInfo) {
            throw new \Exception('Path "' . $environment['dest'] . '" must be a valid image');
        }
        list($width, $height)       = $imageInfo;

        // Calculate text scale
        $textScale                  = (int) round(sqrt($width * $height) / 10);

        // Define paths
        $this->targetDir            = realpath(dirname($environment['dest'])) . DIRECTORY_SEPARATOR;
        $this->targetFilename       = basename($environment['dest']);

        // Placeholdize
        $this->command()
            ->create($width, $height, $this->getOption('background-color'))
            ->merge($this->command()
                ->background()
                ->fill($this->getOption('text-color'))
                ->label($width . 'x' . $height, $textScale, 'C:\Windows\Fonts\gra.ttf')
                ->save($this->temp('text.png')))
            ->align('center', 'middle')
            ->save($this->target());

        return $this;
    }
}