<?php

namespace Pantheon\Terminus\UnitTests\Commands\Multidev;

use Pantheon\Terminus\Commands\Multidev\MergeFromDevCommand;

/**
 * Testing class for Pantheon\Terminus\Commands\Multidev\MergeFromDevCommand
 */
class MergeFromDevCommandTest extends MultidevCommandTest
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->command = new MergeFromDevCommand($this->getConfig());
        $this->command->setLogger($this->logger);
        $this->command->setSites($this->sites);
        $this->environment->method('mergeFromDev')->willReturn($this->workflow);
    }

    /**
     * Tests the multidev:merge-from-dev command
     */
    public function testMultidevDelete()
    {
        $this->environment->id = 'multipass';

        $this->logger->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo('notice'),
                $this->equalTo("Merged the dev environment into {env}."),
                $this->equalTo(['env' => $this->environment->id,])
            );
        $this->workflow->expects($this->once())
          ->method('wait');
        $this->environment->expects($this->once())
          ->method('mergeFromDev')
          ->with($this->equalTo(['updatedb' => false,]));
        $this->workflow->method('isSuccessful')->willReturn(true);

        $out = $this->command->mergeFromDev("site.{$this->environment->id}");
        $this->assertNull($out);
    }

    /**
     * Tests to ensure the multidev:merge-from-dev to ensure it passes the 'updatedb' option successfully
     */
    public function testMultidevDeleteWithBranch()
    {
        $this->environment->id = 'multipass';

        $this->logger->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo('notice'),
                $this->equalTo("Merged the dev environment into {env}."),
                $this->equalTo(['env' => $this->environment->id,])
            );
        $this->workflow->expects($this->once())
            ->method('wait');
        $this->environment->expects($this->once())
            ->method('mergeFromDev')
            ->with($this->equalTo(['updatedb' => true,]));
        $this->workflow->method('isSuccessful')->willReturn(true);

        $out = $this->command->mergeFromDev("site.{$this->environment->id}", ['updatedb' => true,]);
        $this->assertNull($out);
    }

    /**
     * Tests to ensure the multidev:merge-from-dev throws an error when the environment-creation operation fails
     *
     * @expectedException \Terminus\Exceptions\TerminusException
     * @expectedExceptionMessage The dev environment could not be merged into {env}.
     */
    public function testMultidevDeleteFailure()
    {
        $this->environment->id = 'multipass';

        $this->workflow->method('getMessage')->willReturn("The dev environment could not be merged into {env}.");
        $this->workflow->expects($this->once())
            ->method('wait');
        $this->environment->expects($this->once())
            ->method('mergeFromDev')
            ->with($this->equalTo(['updatedb' => false,]));
        $this->workflow->method('isSuccessful')->willReturn(false);

        $out = $this->command->mergeFromDev("site.{$this->environment->id}");
        $this->assertNull($out);
    }
}
