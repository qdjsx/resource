<?php

namespace App\Libraries;


class Solr
{
    private $host;
    private $core;
    private $wt;
    private $q;
    private $fl;
    private $sort;
    private $start;
    private $rows;
    public static $connect_timeout = 3;

    public static $read_timeout = 3;

    public function __construct($host, $core, $wt = 'json')
    {
        $this->host = $host;
        $this->core = $core;
        $this->wt = $wt;
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function ping()
    {
        $url = $this->host . $this->core . '/admin/ping?wt=' . $this->wt;
        return $this->request($url);
    }

    public function query()
    {
        $url = $this->host . $this->core . '/select?wt=' . $this->wt . '&q=' . urlencode($this->q);
        if ($this->fl) {
            $url .= '&fl=' . urlencode($this->fl);
        }
        if ($this->sort) {
            $url .= '&sort=' . urlencode($this->sort);
        }
        if ($this->start) {
            $url .= '&start=' . $this->start;
        }
        if ($this->rows) {
            $url .= '&rows=' . $this->rows;
        }

        return $this->request($url);
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getCoree()
    {
        return $this->core;
    }

    public function setCore($core)
    {
        $this->core = $core;
    }

    public function getWt()
    {
        return $this->wt;
    }

    public function setWt($wt)
    {
        $this->wt = $wt;
    }

    public function getQ()
    {
        return $this->q;
    }

    public function setQ($q)
    {
        $this->q = $q;
    }

    public function getFl()
    {
        return $this->fl;
    }

    public function setFl($fl)
    {
        $this->fl = $fl;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    public function request($url, $post_fields = '')
    {
        return $this->requests($url, $post_fields);
    }


    public  function requests($url, $post_fields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (self::$read_timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$read_timeout);
        }
        if (self::$connect_timeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connect_timeout);
        }
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if ($post_fields) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}




