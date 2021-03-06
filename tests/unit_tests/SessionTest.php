<?php

namespace Terminus\UnitTests;

use Terminus\Session;

/**
 * Testing class for Terminus\Session
 */
class SessionTest extends TerminusTest
{

  /**
   * @var Session
   */
    private $session;

    public function setUp()
    {
        parent::setUp();
        $this->session = new Session();
    }

    public function testConstruct()
    {
        $this->assertObjectHasAttribute('data', $this->session);
        $this->assertObjectHasAttribute('instance', $this->session);
    }

    public function testGet()
    {
        $this->setDummyCredentials();
        //Extant value
        $session = $this->session->get('session');
        $this->assertTrue(strpos($session, '0ffec038-4410-43d0-a404-46997f672d7a') === 0);

        //Invalid look-up
        $invalid = $this->session->get('invalid', null);
        $this->assertNull($invalid);
    }

    public function testGetData()
    {
        $data = Session::getData();
        $this->assertInternalType('object', $data);
        $this->assertObjectHasAttribute('session', $data);
    }

    public function testGetValue()
    {
        //Extant value
        $this->assertNotFalse($this->session->getValue('session'));
        $this->assertNotNull($this->session->getValue('session'));

        //Invalid look-up
        $invalid = $this->session->getValue('invalid');
        $this->assertFalse($invalid);
    }

    public function testInstance()
    {
        $session = Session::instance();
        $this->assertInstanceOf('Terminus\Session', $session);
    }

    public function testSet()
    {
        $message  = 'Astronomy compels the soul to look upward, and leads us from ';
        $message .= 'this world to another.';
        $this->session->set('Plato', $message);
        $this->assertEquals($message, $this->session->get('Plato'));
    }

    public function testSetData()
    {
        $this->assertFalse($this->session->setData([]));

        $proverb[] = 'Be humble for you are made of earth. ';
        $proverb[] = 'Be noble for you are made of stars.';
        $this->assertTrue(Session::setData($proverb));
        $data = (array)Session::getData();
        $this->assertEquals(array_pop($data), $proverb[1]);
        $this->setDummyCredentials();
    }

    public function testGetUser()
    {
        $user = Session::getUser();
        $this->assertEquals(
            $user->get('id'),
            '0ffec038-4410-43d0-a404-46997f672d7a'
        );
    }
}
