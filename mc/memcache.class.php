<? 
class mc_client
{
    var $memcache;
    var $prefix = '';
    var $compress = false;
    var $timeLife = 0;

    private $config = array(
        'host' => 'localhost',
        'port' => 11211,
        'compress' => false,
        'prefix' => '',
        'time_life' => 0
    );

    function __construct($config = array()){
        $this->config   = array_merge($this->config, $config);
        $this->memcache = new Memcache;
        if (!$this->memcache->connect($this->config['host'], $this->config['port'])) {
            $this->memcache = false;
            return false;
        }
        $this->prefix   = $this->config['prefix'];
        $this->time_life = $this->config['time_life'];
        return true;
    }

    protected function val($val){
        if (!$this->config['compress'])
            return $val;
        $this->compress = is_bool($val) || is_int($val) || is_float($val) ? false : MEMCACHE_COMPRESSED;
        return $val;
    }

    public function get($key){
        return $this->memcache->get($this->prefix . $key);
    }

    public function set($key, $val, $time = false){
        return $this->memcache->set($this->prefix . $key, $this->val($val), $this->compress, $time ? $time : $this->time_life);
    }

    public function add($key, $val, $time = false){
        return $this->memcache->add($this->prefix . $key, $this->val($val), $this->compress, $time ? $time : $this->time_life);
    }

    public function del($key){
        return $this->memcache->delete($this->prefix . $key);
    }

    public function flush(){
        if(!$c = @fsockopen($this->config['host'], $this->config['port'],$errno,$errstr,30)) {
            $this->memcache->flush();
            return false;
        }
        fwrite($c, "flush_all\r\n");
        $keep_trying = true;
        $line = "";
        while($keep_trying){
            $line .= fgets($c,4);
            if($line === false)	continue;
            if(substr($line, -strlen("\r\n")) == "\r\n") $keep_trying = false;
        }
        $line = strtolower(str_replace("\r\n", "", $line));
        if($line == 'ok') return true;
        return false;
    }

    public function __destruct(){
        $this->memcache->close();
    }

    public function increment($key, $val = 1, $time = false){
        return $this->memcache->increment($this->prefix . $key, $val, false, $time ? $time : $this->time_life);
    }

    public function decrement($key, $val = 1){
        return $this->memcache->decrement($key, $val);
    }
}
