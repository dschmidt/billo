<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ListsConfigParserTest extends TestCase
{
    public function testListsAndMembersAreParsed(): void
    {
        $parser = new ListsConfigParser( __DIR__ . '/fixtures/listsConfig_1.ini');

        $this->assertEquals(
            $parser->getLists(),
            ['test-list@domain.tld']
        );

        $this->assertEquals(
            $parser->getMembers('thislistdoesnotexist@domain.tld'),
            []
        );

        $this->assertEquals(
            $parser->getMembers('test-list@domain.tld'),
            [
                'user@some-domain.tld' => 'Foo',
                'someone@other-domain.de' => 'Bar',
            ]
        );

        $this->assertTrue(
            $parser->isMember('user@some-domain.tld', 'test-list@domain.tld')
        );

        $this->assertFalse(
            $parser->isMember('bla@some-domain.tld', 'test-list@domain.tld')
        );

        // TODO: implement case insensitive matching
        // $this->assertTrue(
        //     $parser->isMember('User@some-domain.tld', 'test-list@domain.tld')
        // );
    }
}
