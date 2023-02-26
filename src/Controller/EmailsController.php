<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Laminas\Diactoros\Stream;

/**
 * Emails Controller
 *
 * @property \App\Model\Table\EmailsTable $Emails
 */
class EmailsController extends AppController
{
    /**
     * Marca um email como lido
     *
     * @param string|null $id ID do email
     * @return \Cake\Http\Response
     */
    public function info(?string $id = null): Response
    {
        $email = $this->Emails->get($id);

        if (!$email->message_opened) {
            $email->message_opened = true;
            $email->opening_date = new FrozenTime();

            $this->Emails->saveOrFail($email);
        }

        $streamObject = new Stream('php://memory', 'r+');

        $img = imagecreatetruecolor(1, 1);
        imagesavealpha($img, true);
        $color = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $color);

        $stream = $streamObject->detach();
        imagepng($img, $stream);
        $streamObject->attach($stream, 'r');
        $streamObject->rewind();

        return $this->getResponse()
            ->withHeader('Content-type', 'image/png')
            ->withLength($streamObject->getSize())
            ->withBody($streamObject);
    }
}
