<?php

namespace Baruchyan\BitrixExchange;


use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
/**
 * Class BaseExchange
 * @package Project\Service\Exchange
 */
abstract class BaseExchange{

    /** @var \Bitrix\Main\HttpRequest  */
    protected $request;

    /** @var  \Bitrix\Main\Server */
    protected $server;

    /** @var string */
    protected $mode;

    /** @var Response */
    protected $response;

    /** @var CUser */
    protected $user;

    /** @var int */
    protected $userId;

    /** @var array */
    protected $responseTypeMap;

    /** @var File */
    protected $file;

    /** @var bool */
    protected $devMode;

    /**
     * todo mode init
     * todo удаление старых файлов
     * todo json_decode в file
     * todo возврат ответа в xml
     */

    /**
     * Base constructor.
     */
    public function __construct()
    {
        $this->initParams();
        $this->fillResponseTypeMap();
        $this->processing();
    }

    /**
     * Заполнение карты ответов для разных mode
     * @return mixed
     */
    abstract protected function fillResponseTypeMap(): void;

    /**
     * Инициализация параметров
     */
    protected function initParams(): void
    {
        $context = Application::getInstance()->getContext();
        $this->request = $context->getRequest();
        $this->server = $context->getServer();

        $this->response = new Response();

        global $USER;
        $this->user = $USER;
        $this->userId = $this->user->GetID();

        $this->devMode = ($this->request->get('test_mode') == 'Y');

    }

    /**
     * Инициализация файла
     */
    protected function initFile(): void
    {

        try{
            $this->file = new File($this->request->get('filename'), Option::get('main', 'upload_dir') . '/'. static::FILE_DIR);
        }catch (\Exception $exception){
            $this->response->setMessage($exception->getMessage());
        }

    }

    /**
     * Выполнение действия
     */
    protected function processing(): void
    {
        $this->mode = $this->request->get('mode');

        $methodName = $this->getModeMethodName();

        if(empty($methodName)){
            $this->response->setMessage('Неизвестная команда');
            return;
        }

        $this->$methodName();

    }

    /**
     * Получение имени метода Mode
     * @return string
     */
    protected function getModeMethodName(): string
    {
        $methodName = 'mode'.ucfirst($this->mode);

        return (method_exists($this, $methodName)) ? $methodName : '';

    }

    /**
     * ModeInit
     */
    protected function modeInit(): void
    {

    }

    /**
     * Mode Checkauth
     */
    protected function modeCheckauth(): void
    {
        if($this->user->IsAuthorized()){

            $this->response->setSuccessStatus();
            $this->response->addMessageLine(\session_name());
            $this->response->addMessageLine(\session_id());

        }else{

            $this->response->setMessage('Доступ запрещен');

        }
    }

    /**
     * Mode File
     */
    protected function modeFile(): void
    {

        $this->initFile();

        if(empty($this->file))
            return;

        if($this->file->saveFile()){

            $this->response->setSuccessStatus();

        }else{

            $this->response->setMessage($this->file->getMessage());

        }

        // удаляем старые файлы
        $deleteOldFiles = true;
        if(constant('static::NO_REMOVE_OLD_FILES')){
           if(static::NO_REMOVE_OLD_FILES)
               $deleteOldFiles = false;
        }

        if($deleteOldFiles){

            // todo добавить удаление старых файлов
        }



    }

    /**
     * геттер $response
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response->toArray();
    }


    /**
     * Вывод ответа в формате
     * todo перенести в response
     */
    public function printFormatResponse()
    {
        $type = $this->responseTypeMap[$this->mode];
        global $APPLICATION;

        $APPLICATION->RestartBuffer();
        header("Pragma: no-cache");

        switch($type){

            // текстовый ответ
            case 'text':
                $response = $this->response->toArray();

                echo (($response['status']) ? "success" : "failure") ."\n";

                if(!empty($response['message']))
                    echo $response['message'] ."\n";

                if(!empty($response['message_lines'])){
                    foreach ($response['message_lines'] as $line){
                        echo $line ."\n";
                    }
                }

                break;

            // ответ xml
            case 'xml':
                header("Content-Type: application/xml");
//                header("Content-Length: " . strlen($contents));
            // генерация xml будет происходить в классе обмена: сделать проверку существования метода
                break;

            // ответ в json
            case 'json':
                header("Content-Type: application/json");
                echo json_encode($this->response->toArray());
                break;

            default:
                break;
        }
    }

}