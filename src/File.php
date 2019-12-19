<?php


namespace Baruchyan\BitrixExchange;


use Bitrix\Main\Application;
use Baruchyan\BitrixExchange\Exceptions\EmptyFilenameOrDirectory;

/**
 * Class File
 * @package Project\Service\Exchange
 */
class File
{
    /** @var string */
    protected $filedir;

    /** @var string */
    protected $path;

    /** @var string */
    protected $filename;

    /** @var \Bitrix\Main\Server  */
    protected $server;

    /** @var string */
    protected $message;


    /**
     * File constructor.
     * @param mixed $filename
     * @param mixed $filedir
     */
    public function __construct($filename = '', $filedir = '')
    {

        if(!empty($filename) && !empty($filedir)){
            $this->filename = $filename;
            $this->filedir = $filedir;

            $context = Application::getInstance()->getContext();
            $this->server = $context->getServer();

            $this->path = $this->server->getDocumentRoot().'/'.$this->filedir. '/' .$this->filename;
        }else{
            throw new EmptyFilenameOrDirectory('Отсутствует имя файла или директории');
        }

    }

    /**
     * Сохранение файла
     * @return bool
     */
    public function saveFile(): bool
    {

        if (function_exists("file_get_contents")) {
            $data = file_get_contents("php://input");

        } elseif (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
            $data = &$GLOBALS["HTTP_RAW_POST_DATA"];
        } else {
            $data = false;
        }

        if (empty($data)){
            $this->message = 'Ошибка чтения файла';
            return false;
        }

        CheckDirPath($this->path);

        $fp = fopen($this->path, "w");

        if (empty($fp)){
            $this->message = 'Ошибка записи файла';
            return false;
        }

        $result = fwrite($fp, $data);
        fclose($fp);

        return true;

    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Массив значений из xml
     * @return array
     */
    public function getFileXmlValues(): array
    {
        $objXML = new \CDataXML ();
        $objXML->LoadString(file_get_contents($this->path));

        return (!empty($objXML)) ? $objXML->GetArray() : [];
    }



}