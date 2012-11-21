<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use PhpAutoTest\Console\Autotest;

class ListCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithPopUpsActivated()
    {
        $application = new Application();
        $application->add(new Autotest());

        $command       = $application->find('start');
        $commandTester = new CommandTester($command);
        try {
            $commandTester->execute(
                array(
                    'command'   => $command->getName(),
                    'directory' => 'non/existent/dir',
                )
            );
        } catch (\Exception $e) {
            $this->assertRegExp('/Operating system popups activated/', $commandTester->getDisplay());
            $this->assertEquals('directory non/existent/dir does not exists', $e->getMessage());
        }
    }

    public function testExecuteWithPopUpsDisabled()
    {
        $application = new Application();
        $application->add(new Autotest());

        $command       = $application->find('start');
        $commandTester = new CommandTester($command);
        try {
            $commandTester->execute(
                array(
                    'command'   => $command->getName(),
                    '--disable-popups'   => true,
                    'directory' => 'non/existent/dir',
                )
            );
        } catch (\Exception $e) {
            $this->assertRegExp(
                '/Operating system popups disabled/',
                $commandTester->getDisplay()
            );
            $this->assertEquals('directory non/existent/dir does not exists', $e->getMessage());
        }
    }
}