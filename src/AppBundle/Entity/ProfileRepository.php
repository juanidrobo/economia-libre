<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProfileRepository extends EntityRepository {

    private $numberOfResults = 5;

    function setNumberOfResults($number) {
        $this->numberOfResults = $number;
    }

    function getNumberOfResults() {
        return $this->numberOfResults;
    }

    function getCreatedPromises($userCode, $offset) {
        $sql = 'SELECT p.code,p.description, p.responsible,p.active,p.visible,p.date as datePromise,e.action,e.date as dateEvent, owner.code as ownerCode, owner.name as ownerName,owner.email as ownerEmail, owner.phone as ownerPhone,receiver.code as receiverCode, receiver.name as receiverName,receiver.email as receiverEmail, receiver.phone as receiverPhone  '
                . 'FROM `promise` as p LEFT JOIN `event` as e on p.code=e.promise '
                . 'inner join ( SELECT max(date) as max_date, code FROM event GROUP BY promise ) x on e.date=x.max_date '
                . 'left join `user` as owner on e.owner=owner.code '
                . 'left join `user` as receiver on e.receiver=receiver.code '
                . 'WHERE p.responsible=:userCode and p.active=true and p.visible=true GROUP By p.code ORDER BY dateEvent DESC LIMIT ' . $offset . ' , ' . $this->numberOfResults;
        $params = array(
            'userCode' => $userCode
        );

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }

    function getReceivedReviews($userCode, $offset) {
        $sql = 'SELECT u.code as reviewerCode, u.name as reviewerName, u.email as reviewerEmail, u.phone as reviewerPhone, p.code, p.description, p.responsible, r.review, r.user, r.date as dateReview '
                . 'FROM `promise` as p INNER JOIN `review` as r on p.code=r.promise '
                . 'INNER JOIN `user` as u on r.user=u.code '
                . 'WHERE p.responsible=:userCode ORDER BY dateReview DESC LIMIT ' . $offset . ' , ' . $this->numberOfResults;
        ;
        $params = array(
            'userCode' => $userCode,
        );

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }

    function getWrittenReviews($userCode, $offset) {
        $sql = 'SELECT u.code as responsibleCode, u.name as responsibleName, u.email as responsibleEmail, u.phone as reponsiblePhone, p.code, p.description, p.responsible, r.review, r.user, r.date as dateReview '
                . 'FROM `promise` as p INNER JOIN `review` as r on p.code=r.promise and r.user=:userCode '
                . 'INNER JOIN `user` as u on p.responsible=u.code '
                . 'ORDER BY dateReview DESC LIMIT ' . $offset . ' , ' . $this->numberOfResults;
        ;
        $params = array(
            'userCode' => $userCode,
        );

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }

}
