<?
class OAuth
{
    var $access_token;
    var $user_id;
    var $email;
    var $error = false;

    protected $config = array(
        'APP_ID' => '', // ID приложения
        'APP_SECRET' => '', // Защищенный ключ
        'REDIRECT_URI' => '',
        'DISPLAY' => 'page', // page OR popup OR touch OR wap
        'SCOPE' => array(
            'notify', // Пользователь разрешил отправлять ему уведомления.
            'email',
            //'friends', // Доступ к друзьям.
            //'photos',// Доступ к фотографиям.
            //'audio',	// Доступ к аудиозаписям.
            //'video',	// Доступ к видеозаписям.
            //'docs',	// Доступ к документам.
            //'notes',	// Доступ заметкам пользователя.
            //'pages',	// Доступ к wiki-страницам.
            //'wall',	// Доступ к обычным и расширенным методам работы со стеной.
            //'groups',// Доступ к группам пользователя.
            //'ads',	// Доступ к расширенным методам работы с рекламным API.
            //'offline' // Доступ к API в любое время со стороннего сервера.
        ),
        'URI_METHOD' => 'https://api.vk.com/method/{METHOD_NAME}?{PARAMETERS}&access_token={ACCESS_TOKEN}',
        'URI_AUTH' => 'https://oauth.vk.com/authorize?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&scope={SCOPE}&display={DISPLAY}&response_type=code',
        'URI_ACCESS_TOKEN' => 'https://oauth.vk.com/access_token?client_id={CLIENT_ID}&client_secret={APP_SECRET}&code={CODE}&redirect_uri={REDIRECT_URI}'
    );

    function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Генерирует ссылку для перехода к авторизации
     * @return type String
     */
    public function get_link_login()
    {
        $array = array(
            '{CLIENT_ID}' => $this->config['APP_ID'],
            '{REDIRECT_URI}' => $this->config['REDIRECT_URI'],
            '{SCOPE}' => implode(',', $this->config['SCOPE']),
            '{DISPLAY}' => $this->config['DISPLAY']
        );
        return strtr($this->config['URI_AUTH'], $array);
    }

    /**
     * Получение ACCESS TOKEN для дальнейших выполнения запросов к API
     * @return type Boolean
     */

    public function login()
    {
        $uri = $_SERVER['QUERY_STRING'];
        parse_str($uri);
        if (!isset($error)) {
            $array = array(
                '{CLIENT_ID}' => $this->config['APP_ID'],
                '{APP_SECRET}' => $this->config['APP_SECRET'],
                '{REDIRECT_URI}' => $this->config['REDIRECT_URI'],
                '{CODE}' => $code
            );

            $url    = strtr($this->config['URI_ACCESS_TOKEN'], $array);
            $result = json_decode(file_get_contents($url));
            if (isset($result->error)) {
                $this->error = 'Ошибка получения Access Token Error: ' . $result->error . ' , Description: ' . $result->error_description;
                return false;
            } else {
              return (Array)$result;
            }
        } else {
            $this->error = 'Ошибка Error: ' . $error . ' , Description: ' . $error_description;
            return false;
        }
    }

    /**
     * Метод для обращения к API
     * Example
     * $vk = Vk::instance();
     * $result = $vk->api('getProfiles',array('uids'=> 'XXXXXX','fields'=>'first_name,last_name,nickname')); // XXXXXX - ID пользователя в контакте
     * @param type $method String
     * @param type $parametrs Array
     * @return type Object stdClass
     */
    function api($method = FALSE, $parametrs = array())
    {
        $array = array(
            '{METHOD_NAME}' => $method,
            '{PARAMETERS}' => http_build_query($parametrs),
            '{ACCESS_TOKEN}' => $this->access_token
        );
        $url   = strtr($this->config['URI_METHOD'], $array);
        $result = json_decode(file_get_contents($url), true);
        if (isset($result['response'])) {
            return array_shift($result['response']);
        } else {
            return $result['error'];
        }
    }
}
