# Bitrix Exchange Package

Пакет для работы с не стандартными обменами с 1С.
Для работы с обменом, больше не надо подключать стандартный компонент обмена или создавать его копию.

## Установка
```sh
composer require baruchyan/bitrix-exchange
```

## Принцип работы

Рассмотрим пример организации обмена, с условием, что для каждого отдельного отбмена 1с обращается к файлу exchange.php на сайте, с дополнительными параметрами: 
 
http://site.ru/exchange.php?type=message&mode=init
>type - тип обмена (в нашем случае message - обмен сообщениями)
>mode - шаг обмена (init, checkauth, file, import, query)
>В большей части слачаев шаги init, checkauth и file выполняют одни и теже действия, поэтому их стандартная реализация помещена в класс BaseExchange
>filename - имя файла, который передает 1с
>test_mode  - при передаче значения Y инициализируется редим разработчика. Видимых и очевидных измененеий при включении данного режима не будет. Данный режим используется в классах обменов для отладки (по условию if($this->devMode)) 

Создадим класс обмена MessageExchange, который наследует Baruchyan\BitrixExchange\BaseExchange
(пример данного файла находится в папке example). Здесь будут описаны отделые фрагменты
 
```php
const FILE_DIR = '1c_messages'; 
```
каталог, в который будут сохраняться файлы от 1с

```php
protected function fillResponseTypeMap(): void
{
    $this->responseTypeMap = [
        'checkauth' => 'text',
        'query' => 'json',
        'file' => 'json',
        'import' => 'json'
    ];
}
```
Карта ответов. В данном случае, для разных шагов обмена ответы будут возращаться в разном формате


```php
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
```
Описание шага query - отдаем 1с сообщения с сайта

Если требуется не стандартная обработка для какого-то шага, описанного в базовом классе, Вы можете переопределить данный метод в своем классе обмена и сиспользовать свою логику

```php
protected function modeCheckauth(): void
{
    // ...
}
```
## Как использовать 
На странице exchange.php, к которому обращается 1с, вместо подключения стандартны комплонентов, вставим код
```php
$exchange = new \Project\Exchanges\Message();
$exchange->printFormatResponse();
```
Для тестирования можно получить просто объект ответа, без вывода в формате
```php 
$response = $exchange->getResponse();
```
