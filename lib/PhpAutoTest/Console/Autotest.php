<?php

namespace PhpAutoTest\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use FsWatcher\Watcher;
use PhpAutoTest\Popup;
use Sh\Sh;

class Autotest extends Command
{
    const POPUP_HEADER = 'gonzalo123/PhpAutoTest';

    const DEFAULT_DIRECTORY_TO_WATCH = 'tests';

    private $consoleOutput;
    private $soPopupActivated;
    private $directoryToWatch;
    private $sh;

    protected function configure()
    {
        $this->setName('start')
            ->setDescription('executes automatically phpunit every time you save a php file')
            ->addArgument('directory', InputArgument::OPTIONAL, 'Where are your tests?')
            ->addOption('disable-popups', 'dp', InputOption::VALUE_NONE, 'Disable popup messages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sh    = new Sh();
        $this->popup = new Popup(self::POPUP_HEADER);

        $this->consoleOutput = $output;

        $this->handlePopups($input);
        $this->directoryToWatch = $this->getDirectoryToWatchFromInput($input, $output);

        $this->consoleOutput->writeln('<question>Autotest is running ... (ctrl-c to finish)</question>');
        $this->consoleOutput->writeln("Watching directory: <info>{$this->directoryToWatch}</info>");

        $this->startWatcher();
    }

    protected function handlePopups($input)
    {
        $this->soPopupActivated = $input->getOption('disable-popups') == 1 ? false : true;
        if ($this->soPopupActivated) {
            $this->consoleOutput->writeln('<info>Operating system popups activated</info>');
        } else {
            $this->consoleOutput->writeln('<info>Operating system popups disabled</info>');
        }
    }

    protected function startWatcher()
    {
        $watcher = Watcher::factory($this->directoryToWatch);
        $watcher->registerExtensionToWatch('php');

        $watcher->onSave(
            function ($file) {
                $this->consoleOutput->writeln("File saved: <info>{$file}</info>");
                $this->onSaveCallback($file);
            }
        );

        $watcher->onDelete(
            function ($file) {
                $this->consoleOutput->writeln("File deleted: <info>{$file}</info>");
                $this->onDeleteCallback($file);
            }
        );

        $watcher->onCreate(
            function ($file) {
                $this->consoleOutput->writeln("File created: <info>{$file}</info>");
                $this->onCreateCallback($file);
            }
        );

        $watcher->start();
    }

    protected function onSaveCallback($file)
    {
        $syntaxCheckOutput = $this->sh->php("-l {$file}");
        if (strpos($syntaxCheckOutput, "No syntax errors") === false) {
            $this->consoleOutput->writeln('<error>Syntax errors detected</error>');
            list($status, $message) = array(Popup::STATUS_NOK, "Errors detected in {$file}");
        } else {
            $phpunitOutput = $this->sh->phpunit(array('--colors', $file));
            $this->consoleOutput->writeln($phpunitOutput);

            if ($this->phpunitOutputHasFailures($phpunitOutput)) {
                list($status, $message) = array(Popup::STATUS_OK, "File: {$file}\n\n phpunit without errors");
            } else {
                list($status, $message) = array(
                    Popup::STATUS_NOK,
                    "File: {$file}\n\n Failures detected within phpunit execution"
                );
                $this->consoleOutput->writeln('<error>FAILURES!</error>');
            }
        }
        if ($this->soPopupActivated) {
            $this->popup->show($message, $status);
        }
    }

    protected function phpunitOutputHasFailures($phpunitOutput)
    {
        return strpos($phpunitOutput, 'FAILURES!') === false;
    }

    protected function onDeleteCallback($file)
    {
        if ($this->soPopupActivated) {
            $message = "File: {$file} Deleted";
            $this->popup->show($message);
        }
    }

    protected function onCreateCallback($file)
    {
        if ($this->soPopupActivated) {
            $message = "File: {$file} Created";
            $this->popup->show($message);
        }
    }

    protected function getDirectoryToWatchFromInput($input)
    {
        $directoryToWatch = $input->getArgument('directory');

        if ($directoryToWatch == '') {
            $dialog           = $this->getHelperSet()->get('dialog');
            $directoryToWatch = $dialog->ask(
                $this->consoleOutput,
                '<question>Where are your tests? [tests]</question>  '
            );
            if (trim($directoryToWatch) == '') {
                $directoryToWatch = self::DEFAULT_DIRECTORY_TO_WATCH;
            }
            return $directoryToWatch;
        }

        if (!is_dir($directoryToWatch)) {
            throw new \Exception("directory {$directoryToWatch} does not exists");
        }
        return $directoryToWatch;
    }
}
