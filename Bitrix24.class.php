<?php

namespace FreePBX\modules;

use Atevi\Classes\Dialplan;
use Atevi\Contexts\ContextCreator;
use Base\Config;
use Bitrix24\RestApi;
use Dialplan\DialplanHook;
use Exception;
use PDO;

require_once __DIR__ . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "autoload.php";

class Bitrix24 implements \BMO
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var RestApi
     */
    private $api;

    /**
     * @throws Exception
     */
    public function __construct($freepbx = null)
    {
        if ($freepbx == null) {
            throw new Exception("Not given a FreePBX Object");
        }

        $this->FreePBX = $freepbx;
        $this->db = $freepbx->Database;
    }

    public function install()
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `bitrix24_settings`(`key` varchar(100) NOT NULL, `value` varchar(255) default NULL, PRIMARY KEY (`key`))');
    }

    public function uninstall()
    {
        $this->db->query('DROP TABLE `bitrix24_settings`');
    }

    /**
     * Метод вызывается перед отображением страницы.
     * Не вызывается в ajax запросах.
     *
     * @param $page
     * @return void
     * @throws Exception
     */
    public function doConfigPageInit($page)
    {
        $this->init();

        $request = $_REQUEST;

        if (!isset($request["view"])) {
            return;
        }

        if ($request["view"] == 'settings' && $request["mode"] == "edit" && $request["edit"] == "Y") {

            foreach ($request["form"] as $key => $value) {
                $this->config->setValue($key, $value);
            }

            $data = [];

            foreach ($this->config->toArray() as $key => $value) {
                $data[] = [$key, $value];
            }

            // create the ?,? sequence for a single row
            $values = str_repeat('?,', count($data[0]) - 1) . '?';

            // construct the entire query
            $sql = "REPLACE INTO bitrix24_settings (`key`, `value`) VALUES " .
                // repeat the (?,?) sequence for each row
                str_repeat("($values),", count($data) - 1) . "($values)";

            $stmt = $this->db->prepare($sql);

            // execute with all values from $data
            $stmt->execute(array_merge(...$data));

            needreload();
            header("Location: /admin/config.php?display=bitrix24&view=settings&mode=edit");
        }
    }


    /**
     * Метод возвращает массив кнопок, которые будут показаны
     * в нижнем правом углу.
     *
     * @param $request
     * @return array|array[]
     */
    public function getActionBar($request): array
    {
        $buttons = [];

        if ($request['display'] == 'bitrix24' && $request["mode"] == "edit") {
            $buttons = [
                'delete' => [
                    'name' => 'delete',
                    'id' => 'delete',
                    'value' => _('Delete')
                ],
                'reset' => [
                    'name' => 'reset',
                    'id' => 'reset',
                    'value' => _('Reset')
                ],
                'submit' => [
                    'name' => 'submit',
                    'id' => 'submit',
                    'value' => _('Submit')
                ]
            ];

            if (empty($request['extdisplay'])) {
                unset($buttons['delete']);
            }
        }

        return $buttons;
    }

    /**
     * В данном методе показываем нужные страницы.
     * Чтобы разобраться, какую страницу показывать, необходимо проверять значение
     * глобальной переменной $_REQUEST["view"].
     *
     * @throws Exception
     */
    public function showPage()
    {
        $request = $_REQUEST;
        $view = $request["view"] ?? "main";

        return load_view($this->getViewPath($view), [
            "request" => $request,
            "config" => $this->config,
            "isBitrix24Connected" => $this->isBitrix24Connected()
        ]);
    }

    /**
     * В данный метод вызывается перед обработчиком.
     * Позволяет прервать запрос, настроить доступ к обработчику через
     * входящий параметр.
     *
     * @param $req
     * @param $setting
     * @return bool
     */
    public function ajaxRequest($req, &$setting): bool
    {
        if ($req == 'handler') {
            $setting = ["authenticate" => true, "allowremote" => false, "changesession" => false];
            return true;
        }

        return false;
    }

    /**
     * Обработчик ajax запроса. Вызывается без doConfigPageInit.
     *
     * @return false|string[]
     */
    public function ajaxHandler()
    {

        if ($_REQUEST['command'] == 'handler') {
            $this->initConfig();

            // test auth for
            if (isset($_REQUEST['auth']) &&
                isset($_REQUEST['auth']['access_token']) &&
                $_REQUEST['auth']['access_token'] == $this->config->getValue("AuthCodeOutgoingHook")) {
                var_dump("auth valid");
            }

            return array("my ajax handler");
        }

        return false;
    }

    public function getRightNav($request)
    {
    }

    public function backup()
    {
    }


    public function restore($backup)
    {
    }

    public static function myDialplanHooks()
    {
        return 999;
    }

    public function doDialplanHook(&$ext, $engine, $priority)
    {
        if ($engine != "asterisk") {
            return;
        }

        $hooks = [
            'Dialplan\\Impl\\Ext'
        ];

        /** @var DialplanHook $hook */
        foreach ($hooks as $hook) {
            $hook::execution($ext);
        }
    }

    /**
     * Собираем путь до файлов, которые хранят страницы модуля.
     *
     * @param $page
     * @return string
     */
    private function getViewPath($page): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $page . ".php";
    }

    /**
     * @throws Exception
     */
    private function init()
    {
        $this->initConfig();
        $this->initApi();
    }

    /**
     * Производим запрос к базе данных, после чего заполняем класс конфига сохранёнными значениями.
     * Возможно, тут лучше подошёл способ созранения данных в обычный файл.
     *
     * @return void
     */
    private function initConfig()
    {
        $this->config = new Config(["IncomingHookAddress", "AuthCodeOutgoingHook"]);

        $stmt = $this->db->query("SELECT * FROM bitrix24_settings");

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->config->setValue($row['key'], $row['value']);
        }
    }

    /**
     * @throws Exception
     */
    private function initApi()
    {
        $this->api = new RestApi([
            'HOOK_URL' => $this->config->getValue('IncomingHookAddress')
        ]);
    }

    /**
     * Метод проверяет подключение к Битрикс24. В данном случае используется небольшой хак, чтобы
     * удостоверится, что входящий хук имеет доступ к телефонии. Возможно, требуется кэширование.
     *
     * @return bool
     */
    private function isBitrix24Connected(): bool
    {
        $result = $this->api->call('method.get', ['name' => 'telephony.externalcall.register']);

        return (isset($result['result']) && $result['result']['isExisting'] && $result['result']['isAvailable']);
    }
}
