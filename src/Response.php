<?php


namespace Baruchyan\BitrixExchange;


class Response
{
    /** @var bool */
    protected $status = false;

    /** @var string */
    protected $message = '';

    /** @var array */
    protected $messageLines = [];

    /** @var array */
    protected $fields = [];

    /**
     * Response constructor.
     */
    public function __construct()
    {
        
    }

    /**
     * Установка сообщения
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Добавление строки в сообщение
     * @param string $message
     */
    public function addMessageLine(string $message): void
    {
        $this->messageLines[] = $message;
    }

    /**
     * Установка статуса true
     */
    public function setSuccessStatus(): void
    {
        $this->status = true;
    }

    /**
     * Установка статуса false
     */
    public function setFailureStatus(): void
    {
        $this->status = false;
    }

    /**
     *  Добавление поля к ответу
     * @param string $field
     * @param mixed $value
     */
    public function addResponseField(string $field, $value): void
    {
        $this->fields[$field] = $value;
    }

    /**
     * Преобразование в массив
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'message_lines' => $this->messageLines,
            'fields' => $this->fields
        ];
    }

}