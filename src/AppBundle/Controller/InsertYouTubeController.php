<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoPlayerBundle\Entity\YouTubeInfos;

class InsertYouTubeController extends Controller
{
    public function insertidAction()
    {
        $id_youtube=$_POST['id_youtube'];

        if(isset($id_youtube))
        {
               $id_youtube=$_POST['id_youtube'];
               $title_youtube=$_POST['title_youtube'];
               $subscriber_count=$_POST['subscriber_count'];
               $view_count=$_POST['view_count'];
               $video_count=$_POST['video_count'];
               $channel_description=$_POST['channel_description'];
               $channel_customUrl=$_POST['channel_customUrl'];
               $user=$_POST['pseudo'];

                $repository_youtube_infos_count = $this->getDoctrine()->getRepository('VideoPlayerBundle:YouTubeInfos');

                $qb = $repository_youtube_infos_count->createQueryBuilder('u');
                $qb->where('u.idyoutube=:idyoutube');
                $qb->setParameter('idyoutube', $id_youtube);
                $youtube_user_count = $qb->getQuery()->getResult();
                $youtube_user_count = count($youtube_user_count);

                    if($youtube_user_count==1)
                    {
                        $em = $this->getDoctrine()->getManager();
                        $queryBuilder = $em->createQueryBuilder();
                        $queryBuilder->update(YouTubeInfos::class, 'u')
                        ->set('u.subscribercount', ':subscribercount')
                        ->set('u.viewcount', ':viewcount')
                        ->set('u.videocount', ':videocount')
                        ->set('u.channeldescription', ':channeldescription')
                        ->set('u.customurl', ':customurl')
                        ->set('u.username', ':username')
                        ->where('u.idyoutube = :idyoutube')
                        ->setParameter('subscribercount',$subscriber_count)
                        ->setParameter('viewcount',  $view_count)
                        ->setParameter('videocount',  $video_count)
                        ->setParameter('channeldescription',  $channel_description)
                        ->setParameter('customurl',  $channel_customUrl)
                        ->setParameter('username',  $user)
                        ->setParameter('idyoutube',  $id_youtube);
                        $query = $queryBuilder->getQuery();

                        $query->execute();
                        $em->flush();
                    }
                    else
                    {
                        $em = $this->getDoctrine()->getManager();
                        $youtube_user = new YouTubeInfos();

                        $youtube_user->setIdyoutube($id_youtube);
                        $youtube_user->setTitleyoutube($title_youtube);
                        $youtube_user->setSubscribercount($subscriber_count);
                        $youtube_user->setViewcount($view_count);
                        $youtube_user->setVideocount($video_count);
                        $youtube_user->setChanneldescription($channel_description);
                        $youtube_user->setCustomurl("https://youtube.com/".$channel_customUrl);
                        $youtube_user->setUsername($user);

                        $em->persist($youtube_user);
                        $em->flush();
                    }

        }

        return $this->render('AppBundle:InsertYouTube:insertid.html.twig', array(
            // ...
        ));
    }

}
