<?php


namespace Project\Exchanges;

use CDataXML;
use Baruchyan\BitrixExchange\BaseExchange;


class Message extends BaseExchange
{
    /** @var  array */
    private $messageIds;

    const DATE_FORMAT = 'd.m.Y H:i:s';
    const FILE_DIR = '1c_messages';

    /**
     * @return mixed|void
     */
    protected function fillResponseTypeMap(): void
    {
        $this->responseTypeMap = [
            'checkauth' => 'text',
            'query' => 'json',
            'file' => 'json',
            'import' => 'json'
        ];
    }

    /**
     * Mode Query
     */
    protected function modeQuery(): void
    {
        // получем сообщения
        $messages = $this->getMessages();

        // обновляем статус выгрузки
        if(!empty($this->messageIds)){
            $this->updateMessages();
        }

        $this->response->addResponseField('messages', $messages);
        $this->response->setSuccessStatus();

    }

    /**
     * Mode Import
     */
    protected function modeImport(): void
    {

        $this->initFile();

        if(!empty($this->file)){

            $data = $this->file->getFileXmlValues();
            // todo импорт сообщений
        }
    }

    /**
     * Получение сообщений
     * @return array
     */
    private function getMessages(): array
    {
        $messages = [];

        // todo получение сообщений и orm или иблока

        return $messages;
    }

    /**
     * Обновление сообщений, присвоени статуса выгрузки
     */
    private function updateMessages(): void
    {
        foreach ($this->messageIds as $messageId){
            // todo Update Message
        }
    }



}