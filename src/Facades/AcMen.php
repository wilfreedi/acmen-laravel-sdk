<?php

namespace Wilfreedi\AcMen\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static \Wilfreedi\AcMen\Services\AcMenService queue()
 * @method static \Wilfreedi\AcMen\Channels\TelegramChannel telegram()
 * @method static \Wilfreedi\AcMen\Channels\VkChannel vk()
 * @method static \Wilfreedi\AcMen\Channels\EmailChannel email()
 * @method static \Wilfreedi\AcMen\Contracts\ChannelContract channel(string $name)
 * @method static array sendMessage($chatId, $message, $topicId = null)
 * @method static array sendVkMessage($peerId, $message, $fromId = null, $randomId = null)
 * @method static array sendEmail(array $to, array $toHidden = [], ?string $email = null, ?string $name = null, ?string $subject = null, ?string $message = null, ?string $attach = null)
 *
 */

class AcMen extends Facade
{
    protected static function getFacadeAccessor(): string {
        return 'acmen';
    }
}
