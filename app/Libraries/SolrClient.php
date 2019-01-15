<?php

namespace App\Libraries;

use App\Libraries\Solr;



class SolrClient
{
    private $solr;

    public function __construct()
    {
        $this->solr = new Solr('http://tomcat.wasair.com/solr/', 'goods', 'json');
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function ping() {
        $result = json_decode($this->_solr->ping(), true);

        if (isset($result['status'])
            && $result['status'] == 'OK') {
            return true;
        }

        return false;
    }
    /**
    *根据查询条件进行查询
     */
    public function query($q = '*:*', $fl = '*', $sort = '', $start = 0, $rows = 10)
    {
        $response = array();
        $response['status'] = 0;

        $this->solr->setQ($q);
        $this->solr->setFl($fl);
        $this->solr->setSort($sort);
        $this->solr->setStart($start);
        $this->solr->setRows($rows);
        $result = json_decode($this->solr->query(), true);

        if ($result
            && isset($result['responseHeader'])
            && isset($result['responseHeader']['status'])
            && $result['responseHeader']['status'] == 0
            && isset($result['response'])
            && isset($result['response']['docs'])) {
            $response['status'] = 1;
            $response['results'] = $result['response']['docs'];
            $response['count'] = $result['response']['numFound'];
        }

        return json_encode($response);
    }

    /**
    *复制一下原来的query方法。
     */
    public function countNum($q = '*:*', $fl = '*', $sort = '',$start,$rows)
    {
        $response = array();
        $response['status'] = 0;

        $this->solr->setQ($q);
        $this->solr->setFl($fl);
        $this->solr->setSort($sort);
        $this->solr->setStart($start);
        $this->solr->setRows($rows);
        $result = json_decode($this->solr->query(), true);

        if ($result
            && isset($result['responseHeader'])
            && isset($result['responseHeader']['status'])
            && $result['responseHeader']['status'] == 0
            && isset($result['response'])
            && isset($result['response']['docs'])) {
            $response['status'] = 1;
            $response['results'] = $result['response']['docs'];
        }

        return json_encode($response);
    }
}