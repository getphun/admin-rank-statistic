<?php
/**
 * Site rank controller
 * @package site-rank
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminRankStatistic\Controller;
use AdminRankStatistic\Model\SiteRank as SRank;
use AdminRankStatistic\Library\RankCounter as RCounter;

class RankController extends \AdminController
{

    private function _defaultParams(){
        return [
            'title'             => 'Site Rank',
            'nav_title'         => 'Statistic',
            'active_menu'       => 'statistic',
            'active_submenu'    => 'ranking'
        ];
    }
    
    public function indexAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->read_site_rank)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['ranks'] = [];
        $params['international'] = 0;
        $params['local'] = 0;
        
        $vendors = RCounter::$vendors;
        $params['vendors'] = $vendors;
        
        $vendor = $this->req->getQuery('vendor');
        if(!$vendor)
            $vendor = 'alexa';
        
        if(!isset($vendors[$vendor]))
            return $this->show404();
        
        $params['vendor'] = $vendor;
        
        if($this->req->getQuery('refresh') && $this->can_i->update_site_rank)
            RCounter::fetch($vendor);
        
        $params['title'].= ' - ' . $vendors[$vendor];
        
        $ranks = SRank::get([
            'vendor = :vendor',
            'bind'  => ['vendor' => $vendor]
        ], 30, false, 'created DESC');
        
        if($ranks){
            $last = $ranks[0];
            $params['international'] = $last->international;
            $params['local'] = $last->local;
            $params['ranks'] = $ranks;
        }
        
        return $this->respond('statistic/rank/index', $params);
    }
    
    public function updateAction(){
        // TODO
        // make sure the request is from our server
        
        $cache_name  = 'site-rank-last-vendor';
        
        $last_vendor = $this->cache->get($cache_name);
        if(!$last_vendor)
            $last_vendor = 'similarweb';
        
        $now_vendor   = null;
        $first_vendor = null;
        $found_vendor = false;
        foreach(RCounter::$vendors as $vendor => $label){
            if(!$first_vendor)
                $first_vendor = $vendor;
            if($last_vendor === $vendor)
                $found_vendor = true;
            elseif($found_vendor){
                $now_vendor = $vendor;
                break;
            }
        }
        
        if(!$now_vendor)
            $now_vendor = $first_vendor;
        
        $result = RCounter::fetch($now_vendor);
        
        $this->cache->save($cache_name, $now_vendor, (60*60));
        
        $resp = $result ? 'Success' : 'Fail';
        $resp.= ': ' . $now_vendor;
        
        echo $resp;
    }
}