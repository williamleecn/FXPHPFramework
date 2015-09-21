<?php
namespace Web\Utils;
/**
 * Class XRequest
 */
final class XRequest
{

    public $request;
    public $isMagicQuotesOn = false;

    /**
     * @param $request array
     */
    public function __construct(&$request, $ForceRunMagicQuotes = false)
    {

        $this->isMagicQuotesOn = get_magic_quotes_gpc();

        if ($ForceRunMagicQuotes && !$this->isMagicQuotesOn) {

            $this->request = $this->RunMagicQuotes($request);

            $this->isMagicQuotesOn = true;
        }

        $this->request = &$request;
    }

    private function RunMagicQuotes(&$svar)
    {
        if (!get_magic_quotes_gpc()) {
            if (is_array($svar)) {
                foreach ($svar as $_k => $_v) $svar[$_k] = $this->RunMagicQuotes($_v);
            } else {
                $svar = addslashes($svar);
            }
        }
        return $svar;
    }

    /**
     * @param $str string
     * @return int
     */
    public function GetInt($str)
    {
        return intval($this->request[$str]);
    }

    public function GetNum($str)
    {
        $num = ($this->request[$str]);

        $num2 = preg_replace('/[^0-9]/', '', $num);

        return $num2;

    }

    public function TryGetInt($pname, $default = 0)
    {
        if ($this->HasKey($pname) && $this->IsNumber($pname)) {
            return $this->GetInt($pname);
        }
        return $default;
    }

    public function TryGetNum($pname, $default = 0)
    {
        if ($this->HasKey($pname) && $this->IsNumber($pname)) {
            return $this->GetNum($pname);
        }
        return $default;
    }


    public function TryGetString($pname, $default = '')
    {
        if ($this->HasKey($pname)) {
            return $this->Get($pname);
        }
        return $default;
    }


    /**
     * @param $str string
     * @return string
     */
    public function Get($str)
    {
        return $this->request[$str];
    }


    /**
     * @param $str string
     * @return bool
     */
    public function HasKey($str)
    {
        return isset($this->request[$str]);
    }

    /**
     * @param $str string
     * @return bool
     */
    public function arrInt($str)
    {
        $arr = $this->request[$str];
        $arr1 = explode(',', $arr);
        foreach ($arr1 as $key => $val) {
            if (!is_numeric($val)) {
                return false;
            };
        }
        return true;
    }

    /**
     * @param $str string
     * @return bool
     */
    public function IsEmpty($str)
    {
        return empty($this->request[$str]);
    }

    /**
     * @param $str string
     * @return bool
     */
    public function IsArray($str)
    {
        return is_array($this->request[$str]);
    }

    /**
     * @param $str string
     * @return bool
     */
    public function IsNumber($str)
    {
        return is_numeric($this->request[$str]);
    }

    /**
     * @param $str string
     * @return null|string|array
     */
    public function GetEscapeString($str)
    {
        if (!$this->HasKey($str)) return null;

        if ($this->IsArray($str)) {
            return null;
        }
        if (!$this->isMagicQuotesOn) {

            return addslashes($this->request[$str]);
        }
        return $this->request[$str];

    }


    public function GetUnEscapeString($str)
    {
        if (!$this->HasKey($str)) return null;

        if ($this->IsArray($str)) {
            return null;
        }
        if (!$this->isMagicQuotesOn) {

            return $this->request[$str];
        }
        return stripslashes($this->request[$str]);

    }

    /**
     * @return string
     */
    public function GetRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}