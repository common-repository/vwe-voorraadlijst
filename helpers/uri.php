<?phpclass Uri {    const PARAM_SPLIT_SIGN = "?";    const PARAM_SPLIT_VAR_SIGN = "&";    const ALL = true;    private $_uri;    private $_path;    private $_script_name;    private $_params = array();    /*     * Extract url and parameters     *      * @param url (if url not provided, the current url is used)     */    function __construct($url = null) {        if (null == $url) {            $this->_uri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];        } else {            $this->_uri = $url;        }        $this->_path = parse_url($this->_uri, PHP_URL_PATH);        if (strpos($this->_uri, Uri::PARAM_SPLIT_SIGN) > -1) {            $split = explode(Uri::PARAM_SPLIT_SIGN, $this->_uri);            $this->_uri = $split[0];            $this->_params = $this->_parse_query($split[1]);        }    }    /*     * Instance of self     *      * @return Uri     */    public static function getInstance($url = null) {        return new Uri($url);    }    public function get_uri() {        return $this->_uri;    }    public function get_path() {        return $this->_path;    }    public function get_script_name() {        if (null == $this->_script_name) {            $this->_script_name = basename($_SERVER['SCRIPT_NAME']);        }        return $this->_script_name;    }    public function get_params() {        return $this->_params;    }    /**     *     * @param type $_uri     * @return \Uri     */    public function set_uri($_uri) {        $this->_uri = $_uri;        return $this;    }    /**     *     * @param type $_params     * @return \Uri     */    public function set_params($_params) {        $this->_params = $_params;        return $this;    }    /*     * Build and return the url     *      * @return formatted uri     */    public function toString() {        $response = $this->_uri;        if (count($this->_params) > 0) {            $response .= '?'.http_build_query($this->_params);        }        return $response;    }    /*     * Add var to url     *      * @param $key var name     * @param $value var value     * @return Uri     */    public function addVar($key, $value) {        $array = array();        $array[$key] = $value;        $this->_params = array_merge($this->_params, $array);        return $this;    }    /*    * Add var to url that are parsed from a querystring    *    * @param $querystring    * @return Uri    */    public function parseQuerystring($querystring) {        $querystring = str_replace("?", "", $querystring);        foreach ($this->_parse_query($querystring) as $key => $value) {            $this->addVar($key, $value);        }        return $this;    }    /*     * Remove var from url     *      * @param $key var name     * @return Uri     */    public function removeVar($key = Uri::ALL) {        if ($key === Uri::ALL) {            $this->_params = array();        }        elseif (array_key_exists($key, $this->_params)) {            do {                unset($this->_params[$key]);            } while (array_key_exists($key, $this->_params));        }        return $this;    }    /*     * Remove all vars from querystring     *      * @return Uri     */    public function clear() {        return $this->removeVar(Uri::ALL);    }    /*     * Clear url SEO     *      * @param boolean include querystring params     * @return Uri     */    public function cleanAll($includeQueryString = true) {        $this->_uri = Uri::clean($this->_uri);        if ($includeQueryString && count($this->_params > 0)) {            foreach ($this->_params as $key => $value) {                $this->_params[Uri::clean($key)] = Uri::clean($value);            }        }        return $this;    }    /*     * Use this function to parse out the query array element from     * the output of parse_url().     *      * @param String query     * @return array params      */    private function _parse_query($var) {        $var  = html_entity_decode($var);        $var  = explode('&', $var);        $arr  = array();        foreach ($var as $val) {            $x = explode('=', $val);            $arr[$x[0]] = $x[1];        }        unset($val, $x, $var);        return $arr;    }    /*     * Determin if the current location equals the $uri     *      * @param String uri     * @param boolean include querystring when comparing     * @return boolean     */    public static function isCurrent($uri, $includeQueryString = false) {        if ($uri instanceof Uri) {            $uri = $uri->toString();        }        if ($includeQueryString) {            $current = Uri::getInstance()->toString();        } else {            $current = Uri::getInstance()->clear()->toString();        }        return $uri == $current;    }    /*     * Clean part of uri     *      * @param String part     * @return String cleaned     */    public static function clean($part) {        $response = strtolower($part);        $response = str_replace(Uri::PARAM_SPLIT_VAR_SIGN, _gettextFixed("en"), $response);        $response = str_replace(' ', '+', $response);        $response = preg_replace('/[^a-z0-9\-\/\.\:\+\_]+/', "-", $response);        $response = preg_replace('/[\-]+/', "-", $response);        return $response;    }}?>