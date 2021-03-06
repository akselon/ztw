<?php

namespace AppBundle\Repository;

use AppBundle\Entity\UserBet;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function getSortedTipsters($sortedBy = 'count_of_currents_bets', $order = SORT_DESC, $filters = array()){
        $tipsters = $this->getFilterTipsters($filters);
        $tipstersStats = [];
        foreach($tipsters as $tipster){
            $stats = $this->getStatisticsInfoForTipster($tipster);
            $stats['id'] = $tipster->getId();
            $tipstersStats[] = $stats;
        }

        $helpToSort = array();
        foreach ($tipstersStats as $key => $stats){
            $helpToSort[$key] = $stats[$sortedBy];
        }

        array_multisort($helpToSort, intval($order), $tipstersStats);

        return $tipstersStats;
    }

    public function getStatisticsInfoForTipster($tipster){
        return array(
            'efficiency' => $this->getEfficiencyForTipster($tipster),
            'efficiency_last_3_month' => $this->getEfficiencyForTipster($tipster, 3),
            'efficiency_last_month' => $this->getEfficiencyForTipster($tipster, 1),
            'yield_value' => $this->getYield($tipster),
            'sold_single_bet' => count($tipster->getSoldSingleBets()),
            'sold_subscriptions' => count($tipster->getSoldSubscriptions()),
            'count_of_currents_bets' => $this->getBetsForTipster($tipster, true),
            'count_of_bets' => $this->getBetsForTipster($tipster),
            'subscription_cost' => (float) $tipster->getSubscriptionCost(),
        );
    }

    public function getEfficiencyForTipster($tipster, $countOfLastMonth = 10000){
        $bets = $this->getEntityManager()->getRepository(UserBet::class)->findWithFilterOptions(array(
            'user' => $tipster,
            'gameDateMin' => (new \DateTime())->modify("-".$countOfLastMonth." month"),
            'gameDateMax' => new \DateTime()
        ));
        $correctBets = 0;
        foreach($bets as $bet){
            if($bet->getStatus() == 1) $correctBets++;
        }
        return count($bets) > 0 ? intval(($correctBets/count($bets))*100) : 0;
    }

    public function getBetsForTipster($tipster, $onlyCurrent = false){
        if($onlyCurrent){
            $bets = $this->getEntityManager()->getRepository(UserBet::class)->findWithFilterOptions(array(
                'user' => $tipster,
                'gameDateMin' => new \DateTime(),
            ));
            return count($bets);
        }else{
            return ($tipster->getUsersBets()->count());
        }
    }

    public function getYield($tipster){
        $bets = $tipster->getUsersBets();
        $sumOfStakes = 0;
        $sumOfWinMoney = 0;
        foreach($bets as $bet){
            if($bet->getStatus() == 1){
                $sumOfStakes += $bet->getStake();
                $sumOfWinMoney += $bet->getStake()*$bet->getOdds();
            }else if($bet->getStatus() == -1){
                $sumOfStakes += $bet->getStake();
            }
        }
        return $sumOfStakes > 0 ? intval((($sumOfWinMoney - $sumOfStakes)/$sumOfStakes)*100) : 0;
    }

    public function getFilterTipsters($filters){
        $tipsters = $this->getFilterTipstersQuery($filters)->getQuery()->getResult();
        foreach ($tipsters as $key => $tipster){
            if($tipster->hasRole('ROLE_ADMIN')) unset($tipsters[$key]);
        }
        if(array_key_exists('league', $filters)){
            foreach ($tipsters as $key => $tipster){
                $leagues = $this->getEntityManager()->getRepository(UserBet::class)
                    ->getLeaguesWhereTipsterBet($tipster);
                if(!(array_key_exists($filters['league'], $leagues))){
                    unset($tipsters[$key]);
                }
            }
        }
        return $tipsters;
    }

    public function getFilterTipstersQuery($filters){
        $query = $this->createQueryBuilder('t');
        if(array_key_exists('minPrice', $filters)){
            $query->andWhere('t.subscriptionCost >= :minPrice')
                ->setParameter(':minPrice', $filters['minPrice']);
        }
        if(array_key_exists('maxPrice', $filters)){
            $query->andWhere('t.subscriptionCost <= :maxPrice')
                ->setParameter(':maxPrice', $filters['maxPrice']);
        }
        if(array_key_exists('login', $filters)){
            $query->andWhere('t.login LIKE :login')
                ->setParameter(':login', '%'.$filters['login'].'%');
        }
        return $query;
    }
}
