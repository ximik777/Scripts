<?
class MimeMail {
    var $mailer = 'ProjectName Mailer 1.0';

    var $_to = '';
    var $_to_name = '';
    var $_from = '';
    var $_from_name = '';
    var $_subject = '';
    var $_text = false;

    var $error = false;

    var $headers = array();
    var $headers_text = array();

    var $charset = 'utf-8';
    var $mime_version = 'MIME-Version: 1.0';

    var $data = '';
    var $embeded = array();
    var $attache = array();
    var $emailboundary = '';

    var $template = array();

    var $config = array('host' => '', 'port' => '25', 'user' => '', 'pass' => '', 'auth' => true);

    var $log = array();

    function __construct($config) {
        $this->config        = array_merge($this->config, $config);
        $this->mixed_boundary   = str_repeat('-', 12).substr(md5(time()-1),0,25);
        $this->alt_boundary     = str_repeat('-', 12).substr(md5(time()-2),0,25);
        $this->related_boundary = str_repeat('-', 12).substr(md5(time()-3),0,25);
    }

    function to($to = NULL, $to_name = NULL) {
        $this->_to      = $to;
        $this->_to_name = $to_name ? $to_name : false;
    }

    function from($from = NULL, $from_name = NULL) {
        $this->_from      = $from ?: $this->_from;
        $this->_from_name = $from_name ?: $this->_from_name;
    }

    function subject($subject = '') {
        $this->_subject = $subject != '' ? strip_tags(trim($subject)) : $this->_subject;
    }

    function text($text = '', $subject = '') {
        $this->_text = $text!=='' ? $text : false;
        $this->subject($subject);
    }

    function minify_html($buffer) {
        return  preg_replace(array(
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s'
        ), array(
            '>',
            '<',
            '\\1'
        ), $buffer);
    }

    function embed_images($matches){
        if(strstr($matches[1], 'cid:')) return $matches[0];
        return 'src="'.($this->embed($matches[1])).'"';
    }

    function html($url = '', $ei = false){
        if(!$content = file_get_contents($url))
            return false;
        $this->_text = $this->minify_html($content);

        if($ei){
            $this->_text = preg_replace_callback(
                '/src="([^"]*)"/',
                array($this, 'embed_images'),
                $this->_text);
        }

        return true;
    }

    function send() {
        $this->headers[] = 'To: ' . ($this->_to_name ? '=?' . $this->charset . '?B?' . base64_encode($this->_to_name) . '?=' : '') . ' <' . $this->_to . '>';
        $this->headers[] = 'From: ' . ($this->_from_name !== '' ? '=?' . $this->charset . '?B?' . base64_encode($this->_from_name) . '?=' : '') . ' <' . $this->_from . '>';
        $this->headers[] = 'Subject: ' . ($this->_subject !== '' ? '=?' . $this->charset . '?B?' . base64_encode($this->_subject) . '?=' : '');
        $this->headers[] = 'X-Mailer: ' . $this->mailer;
        $this->headers[] = $this->mime_version;

        if (count($this->attache) > 0) {
            $this->headers[] = "Content-Type: multipart/mixed;\r\n boundary=\"{$this->mixed_boundary}\"\r\n";
            $this->headers[] = "--{$this->mixed_boundary}";
        }

        if (count($this->embeded) > 0) {
            //$this->headers[] = "Content-Type: multipart/alternative;\r\n boundary=\"{$this->alt_boundary}\"\r\n";
            //$this->headers[] = "--{$this->alt_boundary}";
            //$this->headers[] = "Content-Type: text/plain; charset=" . $this->charset;
            //$this->headers[] = "Content-Transfer-Encoding: base64\r\n";
            //$this->headers[] = chunk_split(base64_encode('Hello word!'),76) . "\r\n";
            //$this->headers[] = "--{$this->alt_boundary}";
            $this->headers[] = "Content-Type: multipart/related;\r\n boundary=\"{$this->related_boundary}\"\r\n";
            $this->headers[] = "--{$this->related_boundary}";
        }

        $this->headers[] = "Content-Type: text/html; charset={$this->charset}";
        $this->headers[] = "Content-Transfer-Encoding: base64" . "\r\n";
        $this->headers[] = chunk_split(base64_encode($this->_text),76) . "\r\n";

        if (count($this->embeded) > 0) {
            $this->headers[] = implode('', $this->embeded);
            $this->headers[] = "--{$this->related_boundary}--\r\n";
           //$this->headers[] = "--{$this->alt_boundary}--\r\n";
        }

        if (count($this->attache) > 0) {
            $this->headers[] = implode('', $this->attache);
            $this->headers[] = "--{$this->mixed_boundary}--\r\n\r\n";
        }

        $this->headers[] = "\r\n.\r\n";
        $this->data = implode("\r\n", $this->headers);

        return $this->send_mail();
    }


    function mime_type($filename) {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }

    function attachment($file, $octet_stream = true) {
        if (!$file_data = file_get_contents($file))
            return false;

        $basename = basename($file);
        $attach   = "--{$this->mixed_boundary}\r\n";
        $attach  .= "Content-Type: " . ($octet_stream ? 'application/octet-stream' : ($this->mime_type($file))) . "; name=\"=?" . $this->charset . "?B?" . base64_encode($basename) . "?=\"\r\n";
        $attach  .= "Content-Transfer-Encoding: base64\r\n";
        $attach  .= "Content-Disposition: attachment; name=\"=?" . $this->charset . "?B?" . base64_encode($basename) . "?=\"\r\n\r\n";
        $attach  .= chunk_split(base64_encode($file_data), 76);
        $this->attache[] = $attach;

        return true;
    }


    function embed($file) {
        if (!$file_data = file_get_contents($file))
            return false;
        $content_id = md5(rand()).strstr($this->_from, '@');
        $mime_type = $this->mime_type($file);
        list($type, $ext) = explode('/', $mime_type);
        $name = (md5(rand())).".{$ext}";

        $embed  = "--{$this->related_boundary}\r\n";
        $embed .= "Content-Type: " . $mime_type . ";\r\n name=\"{$name}\"\r\n";
        $embed .= "Content-Transfer-Encoding: base64\r\n";
        $embed .= "Content-ID: <{$content_id}>\r\n";
        $embed .= "Content-Disposition: inline;\r\n filename=\"{$name}\"\r\n\r\n";
        $embed .= chunk_split(base64_encode($file_data), 76) . "\r\n";
        $this->embeded[] = $embed;
        return "cid:{$content_id}";
    }



    private function send_mail() {
        $cp = fsockopen($this->config['host'], $this->config['port']);
        if (!$cp) {
            $this->error  = 'Failed to even make a connection';
            return false;
        }

        $res = fgets($cp, 256);
        if (substr($res, 0, 3) != "220") {
            $this->error  = 'Failed to connect';
            return false;
        }

        fputs($cp, "HELO " . $_SERVER["HTTP_HOST"] . "\r\n");
        $res = fgets($cp, 256);
        if (substr($res, 0, 3) != "250") {
            $this->error  = 'Failed to Introduce';
            return false;
        }

        if ($this->config['auth']) {
            fputs($cp, "AUTH LOGIN\r\n");
            $res = fgets($cp, 256);
            if (substr($res, 0, 3) != "334") {
                $this->error  = 'Failed to Initiate Authentication';
                return false;
            }

            fputs($cp, base64_encode($this->config['user']) . "\r\n");
            $res = fgets($cp, 256);
            if (substr($res, 0, 3) != "334") {
                $this->error  = 'Failed to Provide Username for Authentication';
                return false;
            }

            fputs($cp, base64_encode($this->config['pass']) . "\r\n");
            $res = fgets($cp, 256);
            if (substr($res, 0, 3) != "235") {
                $this->error  = 'Failed to Authenticate password';
                return false;
            }
        }

        fputs($cp, "MAIL FROM: <" . $this->_from . ">\r\n");
        $res = fgets($cp, 256);
        if (substr($res, 0, 3) != "250") {
            $this->error  = 'MAIL FROM failed';
            return false;
        }

        fputs($cp, "RCPT TO: <" . $this->_to . ">\r\n");
        $res = fgets($cp, 256);
        if (substr($res, 0, 3) != "250") {
            $this->error  = 'RCPT TO failed';
            return false;
        }

        fputs($cp, "DATA\r\n");
        $res = fgets($cp, 256);
        if (substr($res, 0, 3) != "354") {
            $this->error  = 'DATA failed';
            return false;
        }

        fputs($cp, $this->data);

        $res = fgets($cp, 256);
        if (substr($res, 0, 3) != "250") {
            $this->error  = 'Message Body Failed:' . $res;
            return false;
        }

        fputs($cp, "QUIT\r\n");
        $res = fgets($cp, 256);
        if (substr($res, 0, 3) != "221") {
            $this->error  = 'QUIT failed';
            return false;
        }

        if (!fclose($cp)) {
            $this->error  = 'fsock not closed';
        }

        return true;
    }
}
