<?php

namespace Wilfreedi\AcMen\Test;

use Wilfreedi\AcMen\Facades\AcMen;

class AcMenServiceTest extends TestCase
{

    /** @test */
    public function it_can_access_telegram_service() {
        $response = AcMen::sendMessage('479493406', 'Hello from test!');
        $this->assertEquals(1, $response['success'], "Telegram Service Error: " . $response['message']);
    }

}
