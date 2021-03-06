<?php

namespace Pantheon\Terminus\UnitTests\Commands\Multidev;

use Pantheon\Terminus\Commands\Multidev\ListCommand;
use Pantheon\Terminus\UnitTests\Commands\CommandTestCase;

/**
 * Testing class for Pantheon\Terminus\Commands\Multidev\ListCommand
 */
class ListCommandTest extends CommandTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->command = new ListCommand($this->getConfig());
        $this->command->setLogger($this->logger);
        $this->command->setSites($this->sites);
    }

    /**
     * Tests the multidev:list command when there are multidev environments
     */
    public function testMultidevListEmpty()
    {
        $this->site->environments->method('multidev')->willReturn([]);

        $this->logger->expects($this->once())
            ->method('log')
            ->with($this->equalTo('warning'), $this->equalTo('You have no multidev environments.'));

        $out = $this->command->listMultidevs('site_name');
        $this->assertInstanceOf('Consolidation\OutputFormatters\StructuredData\RowsOfFields', $out);
        $this->assertEquals([], $out->getArrayCopy());
    }

    /**
     * Tests the multidev:list command when there are no multidev environments
     */
    public function testMultidevListNotEmpty()
    {
        $data = [
          'id' => 'testing',
          'created' => '1984/07/28 16:40',
          'domain' => 'domain',
          'on_server_development' => 'true',
          'locked' => 'false',
          'initialized' => 'true',
        ];

        $this->environment->method('serialize')
          ->willReturn($data);
        $this->site->environments->method('multidev')
          ->willReturn([$this->environment,]);
        $this->logger->expects($this->never())
            ->method($this->anything());

        $out = $this->command->listMultidevs('my_site');
        $this->assertInstanceOf('Consolidation\OutputFormatters\StructuredData\RowsOfFields', $out);

        $this->assertEquals([$data,], $out->getArrayCopy());
    }
}
