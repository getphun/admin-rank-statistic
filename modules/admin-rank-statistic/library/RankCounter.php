<?php
/**
 * Site rank counter
 * @package admin-rank-statistic
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminRankStatistic\Library;
use AdminRankStatistic\Model\SiteRank as SRank;

class RankCounter
{
    static $vendors = [
        'alexa'         => 'Alexa',
        'similarweb'    => 'SimilarWeb'
    ];
    
    static function _fetch_alexa(){
        $uri = 'http://data.alexa.com/data?cli=10&url=' . \Phun::$dispatcher->config->host;
        
        $cu = curl_init($uri);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($cu);
        if(!$result)
            return false;
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        if(!array_key_exists('SD', $array))
            return false;
        
        $array = $array['SD'];
        
        $data = array(
            'international' => ['POPULARITY', '@attributes', 'TEXT'],
            'local' => ['COUNTRY', '@attributes', 'RANK']
        );
        
        $ranks = new \stdClass();
        foreach($data as $prop => $dat){
            $value = $array;
            foreach($dat as $pro){
                if(!array_key_exists($pro, $value))
                    break;
                $value = $value[$pro];
            }
            
            $ranks->$prop = (int)$value;
        }
        
        $row = SRank::get([
            'vendor = :vendor AND created >= :created',
            'bind' => ['vendor' => 'alexa', 'created' => date('Y-m-d 00:00:00')]
        ], false);
        
        if($row){
            return SRank::set($ranks, $row->id);
        }else{
            $ranks->vendor = 'alexa';
            return !!SRank::create($ranks);
        }
    }
    
    static function _fetch_similarweb(){
        $uri = 'https://www.similarweb.com/website/merahputih.com';// . \Phun::$dispatcher->config->host;
        
        $cu = curl_init($uri);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cu, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0');
        $result = curl_exec($cu);
        if(!$result)
            return false;
        
        preg_match_all('!<span class="rankingItem-value js-countable" data-value="([0-9,]+)">([0-9,]+)</span>!', $result, $match);
        if(!array_key_exists(1, $match) || !$match[1])
            return false;
        
        $match = $match[1];
        $ranks = (object)array(
            'international' => array_key_exists(0, $match) ? str_replace(',', '', $match[0]) : 0,
            'local' => array_key_exists(1, $match) ? str_replace(',', '', $match[1]) : 0
        );
        
        $row = SRank::get([
            'vendor = :vendor AND created >= :created',
            'bind' => ['vendor' => 'similarweb', 'created' => date('Y-m-d 00:00:00')]
        ], false);
        
        if($row){
            return SRank::set($ranks, $row->id);
        }else{
            $ranks->vendor = 'similarweb';
            return !!SRank::create($ranks);
        }
    }
    
    static function fetch($vendor){
        if(!isset(self::$vendors[$vendor]))
            return false;
        
        $method = '_fetch_' . $vendor;
        return self::$method();
    }
}