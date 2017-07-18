<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class HistoryRepository extends EntityRepository {

    private $numberOfResults = 5;

    function setNumberOfResults($number) {
        $this->numberOfResults = $number;
    }

    function getNumberOfResults() {
        return $this->numberOfResults;
    }

    function getCreatedPromises($userCode, $offset) {
        $sql = 'SELECT u_owner.code as ownerCode ,u_owner.name as ownerName, u_owner.email as ownerEmail, u_owner.phone as ownerPhone,u_receiver.code as receiverCode,u_receiver.name as receiverName, u_receiver.email as receiverEmail, u_receiver.phone as receiverPhone, p.code as promiseCode,p.description, p.responsible,p.active,p.visible,p.date as datePromise,e.action,e.date as dateEvent,e.owner,e.receiver,e.code as eventCode '
                . 'FROM `promise` as p LEFT JOIN `event` as e on p.code=e.promise '
                . 'inner join ( SELECT max(date) as max_date, code FROM event GROUP BY promise ) x on e.date=x.max_date '
                . 'left join `user` as u_receiver on e.receiver=u_receiver.code '
                . 'left join `user` as u_owner on e.owner=u_owner.code '
                . 'WHERE p.responsible=:userCode GROUP By p.code ORDER BY dateEvent DESC LIMIT ' . $offset . ' , ' . $this->numberOfResults;
        $params = array(
            'userCode' => $userCode
        );

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }

    function getOwnPromises($userCode, $offset) {
        $sql = 'SELECT u.code as responsibleCode, u.name,u.phone,u.email,p.code,p.description, p.responsible,p.active,p.date as datePromise,e.action,e.date as dateEvent,e.owner,e.receiver,e.code as eventCode '
                . 'FROM `promise` as p LEFT JOIN `event` as e on p.code=e.promise '
                . 'inner join ( SELECT max(date) as max_date, code FROM event GROUP BY promise ) x on e.date=x.max_date and ( (e.receiver=:userCode and (e.action!="claim" and e.action!="review") ) or  ( (e.action="claim" or e.action="review") and e.owner=:userCode )) '
                . 'left join `user` as u on p.responsible=u.code '
                . 'GROUP By p.code ORDER BY dateEvent DESC LIMIT ' . $offset . ' , ' . $this->numberOfResults;

        $params = array(
            'userCode' => $userCode,
        );

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }

    function getReceivedReviews($userCode, $offset) {
        $sql = 'SELECT  u.name,u.phone,u.email, p.code, p.description, p.responsible, p.date as datePromise, r.review, r.user as userCode, r.date as dateReview '
                . 'FROM `promise` as p INNER JOIN `review` as r on p.code=r.promise '
                . 'left join `user` as u on r.user=u.code '
                . 'WHERE p.responsible=:userCode ORDER BY dateReview DESC LIMIT ' . $offset . ' , ' . $this->numberOfResults;
        ;
        $params = array(
            'userCode' => $userCode,
        );

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }

    function getWrittenReviews($userCode, $offset) {
        $sql = 'SELECT  u.name, u.code as responsibleCode ,u.phone,u.email, p.code, p.description, p.responsible, r.review, r.user, r.date as dateReview '
                . 'FROM `promise` as p INNER JOIN `review` as r on p.code=r.promise and r.user=:userCode '
                . 'left join `user` as u on p.responsible=u.code '
                . 'ORDER BY dateReview DESC LIMIT ' . $offset . ' , ' . $this->numberOfResults;
        ;
        $params = array(
            'userCode' => $userCode,
        );

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }

}
