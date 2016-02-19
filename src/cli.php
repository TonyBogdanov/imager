<?php
/**
 * This file is part of the Imager package.
 *
 * (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

if('cli' != php_sapi_name()) {
    return;
}

define('IMAGER_ABSPATH', __DIR__ . DIRECTORY_SEPARATOR);

require_once(dirname(__FILE__) . '/autoload.php');

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Imager\Imager;
use Imager\Bundle\AbstractBundle;
use Imager\Bundle\InvalidBundleException;
use Imager\Bundle\BundleAlreadyRegisteredException;

$console    = new Application();

$console
    ->register('init')
    ->setDescription('Starts an interactive wizard for generating an imager.json file.')
    ->setCode(function(InputInterface $input, OutputInterface $output) {
        $question       = $this->getHelper('question');

        $output->writeln('You are about to generate an imager.json file');
        $output->writeln('for automated image generation.');
        $output->write(PHP_EOL);

        $output->writeln('Once you have the file you\'ll be able to call');
        $output->writeln('"php imager.phar generate imager.json".');
        $output->writeln('or just "php imager.phar generate" in the same directory.');
        $output->write(PHP_EOL);

        $imager         = new Imager();
        $filename       = 'imager.json';
        $json           = array(
            'run'       => array(),
            'bundles'   => array(),
            'options'   => array(),
        );

        if($question->ask($input, $output, new ConfirmationQuestion('Would you like to register any custom bundles' .
            PHP_EOL . 'aside from the built in ones? [y/N] ', false))) {
            $output->write(PHP_EOL);

            while(true) {
                $class  = $question->ask($input, $output, new Question('Bundle class name (enter to stop adding): '));
                if(empty($class)) {
                    break;
                }

                $path   = $question->ask($input, $output, new Question('Path to class file (skip if you have' .
                    ' an autoloader): '));

                try {
                    $imager->registerBundle($class, $path);
                    $canonical          = $imager->getBundle($class)->getCanonicalName();
                    $json['bundles'][$canonical] = $path;
                } catch(InvalidBundleException $e) {
                    $output->writeln(sprintf('Bundle <info>%s</info> is invalid, skipped.', $class));
                } catch(BundleAlreadyRegisteredException $e) {
                    $output->writeln(sprintf('Bundle <info>%s</info> is already registered, skipped.', $class));
                }

                $output->write(PHP_EOL);
            }
        }
        $output->write(PHP_EOL);

        /** @var AbstractBundle $bundle */
        foreach($imager->getBundles() as $bundle) {
            if($question->ask($input, $output, new ConfirmationQuestion(sprintf('Would you like to change the default' .
                ' options' . PHP_EOL . 'for bundle "%s"? [y/N] ', get_class($bundle)), false))) {
                $output->write(PHP_EOL);

                foreach($bundle->getOptionNames() as $name) {
                    $output->writeln('Set value for ' . $bundle->getCanonicalName() . '::' . $name);
                    $output->writeln(' - ' . $bundle->getOptionDescription($name));

                    if($bundle->hasOptionDefaultValue($name)) {
                        $default        = $bundle->getOptionDefaultValue($name);
                        $output->write(' - Default: (' . gettype($default) . ')');
                        if(is_scalar($default)) {
                            $output->writeln(' ' . var_export($default, true));
                        }
                    }

                    while(true !== ($error = $bundle->setOption($name, $question->ask($input, $output,
                            new Question(': ', $default))))) {
                        $output->write(PHP_EOL);
                        $output->writeln('Error: ' . $error);
                    }

                    $json['options'][$bundle->getCanonicalName()][$name] = $bundle->getOption($name);

                    $output->write(PHP_EOL);
                }
            }
        }

        if($question->ask($input, $output, new ConfirmationQuestion('Would you like to specify which bundles' .
            ' should be run? [Y/n] ', true))) {
            $output->write(PHP_EOL);

            while(true) {
                $class  = $question->ask($input, $output, new Question('Bundle class name (enter to stop adding): '));
                if(empty($class)) {
                    break;
                }

                if($imager->hasBundle($class)) {
                    $json['run'][] = $imager->getBundle($class)->getCanonicalName();
                    continue;
                }

                $output->writeln('No such bundle is registered.');
                $output->write(PHP_EOL);
            }
        }
        $output->write(PHP_EOL);

        if($question->ask($input, $output, new ConfirmationQuestion('Would you like to specify where to save' .
            PHP_EOL . 'the generated image (path will be' . PHP_EOL . 'relative to the directory from which' .
            ' the generate' . PHP_EOL . 'command is run)? [Y/n] ', true))) {
            $output->write(PHP_EOL);

            $json['dest'] = $question->ask($input, $output, new Question('Path: '));
        }
        $output->write(PHP_EOL);

        if($question->ask($input, $output, new ConfirmationQuestion('Would you like to specify the output format or' .
            PHP_EOL . 'let the script determine it automatically? [y/N] ', false))) {
            $output->write(PHP_EOL);

            $json['format'] = strtolower($question->ask($input, $output, new Question('Format: ')));
        }
        $output->write(PHP_EOL);

        foreach($json as $key => $value) {
            if(empty($value)) {
                unset($json[$key]);
            }
        }
        $result         = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if($question->ask($input, $output, new ConfirmationQuestion('Looks good? [Y/n]' . PHP_EOL . PHP_EOL .
            $result . PHP_EOL, true))) {
            if(@file_put_contents(realpath('.') . DIRECTORY_SEPARATOR . $filename, $result)) {
                $output->writeln('Done.');
            } else {
                $output->writeln(sprintf('Could not write to <info>%s</info>.'), realpath('.') . DIRECTORY_SEPARATOR .
                    $filename);
            }
        }
    });

$console
    ->register('generate')
    ->setDefinition(array(
        new InputArgument('json', InputArgument::OPTIONAL,
            'Path to a valid imager.json file. If left empty an imager.json file in the current working directory' .
            ' will be assumed.'),

        new InputOption('run', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Bundle names to be run.'),
        new InputOption('dest', 'd', InputOption::VALUE_REQUIRED, 'Destination path where to save the generated image.'),
        new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format to save the generated image in,' .
            ' if not specified, format will be extracted from destination path extension, if that fails, or an' .
            ' invalid format is supplied PNG will be assumed. Supported formats depend on your ImageMagick installation.'),
    ))
    ->setDescription('Performs an image generation based on the specified options (either as console options or' .
        ' extracted from an imager.json file).')
    ->setCode(function(InputInterface $input, OutputInterface $output) {
        $json           = $input->getArgument('json');
        $options        = $input->getOptions();

        foreach(array('run', 'dest', 'format') as $name) {
            if(empty($options[$name])) {
                unset($options[$name]);
            }
        }

        if(empty($json)) {
            $json       = 'imager.json';
        }
        $json           = realpath($json);

        if(false !== $json && is_file($json)) {
            $json       = @json_decode(file_get_contents($json), true);
            $options    = is_array($json) ? array_replace($json, $options) : $options;
        }

        $imager         = new Imager();

        if(isset($options['bundles']) && is_array($options['bundles']) && !empty($options['bundles'])) {
            foreach($options['bundles'] as $class => $path) {
                $imager->registerBundle($class, $path);
            }
        }

        if(!is_array($options['run']) || empty($options['run'])) {
            $output->writeln('Nothing to run.');
            return;
        }

        if(!isset($options['dest']) || empty($options['dest']) || !is_string($options['dest'])) {
            $output->writeln('Destination path isn\'t set.');
            return;
        }

        if(!isset($options['format']) || empty($options['format']) || !is_string($options['format'])) {
            $options['format'] = strtolower(pathinfo($options['dest'], PATHINFO_EXTENSION));
            if(empty($options['format'])) {
                $options['format'] = 'png';
            }
        }

        foreach($options['run'] as $class) {
            $imager->runBundle($class, isset($options['options']) && isset($options['options'][$class]) ?
                $options['options'][$class] : array(), array(
                'dest'      => $options['dest'],
                'format'    => $options['format'],
            ), $options['verbose']);
        }
    });

$console->run();