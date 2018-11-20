<?php

namespace AppBundle\Controller\Admin;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google_Service_Analytics;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoPlayerBundle\Entity\MainVideo;
use VideoPlayerBundle\Entity\Video;
use VideoPlayerBundle\Entity\YouTubeInfos;
use VideoPlayerBundle\Entity\PlayListYouTube;
use VideoPlayerBundle\Youtube\Youtube;
use VideoPlayerBundle\YouTubeDownloader\Masih\YoutubeDownloader\YoutubeDownloader;

/**
 * @Route("/admin/")
 *
 * @author Arthur Gribet <a.gribet@gmail.com>
 */
class DashboardController extends Controller
{
    /**
     * @Route("dashboard/", name="admin_dashboard",
     *      options={"sitemap" = {"priority" = 0.7, "changefreq" = "weekly" }})
     */
    public function indexAction()
    {

            $user =$this->get('security.token_storage')->getToken()->getUser();
            //$user= $this->container->get('security.context')->getToken()->getUser();
            $youtube = new Youtube(array('key' => 'AIzaSyCeHo3HfcxfHwgOTtqDr2yQPxkUoNOVeS8'));
            //$video = $youtube->getVideoInfo('rie-hPVJ7Sw');
            //$video2=$youtube->getChannelByName('Ghettsunsix', $optionalParams = false);

            $repository_youtube_infos = $this->getDoctrine()->getRepository('VideoPlayerBundle:YouTubeInfos')->findBy(['username' => ''.$user.'']);
            
            //$channel_id='UC2g_-ipVjit6ZlACPWG4JvA';
            if(!empty($repository_youtube_infos))
            {
            $channel_id=$repository_youtube_infos[0]->getIdyoutube();
            
            $em = $this->getDoctrine()->getManager();
            $videosplaylist=$youtube->getPlaylistsByChannelId($channel_id, $optionalParams = array());
            $array2 = json_decode(json_encode($videosplaylist),true);
            $lenght_array2=count($array2);
            $i=0;

        
            while($i<$lenght_array2)
            {
                $idplaylist=$array2[$i]['id'];
                $titleplaylist=$array2[$i]['snippet']['title'];
                $thumbnailplaylist=$array2[$i]['snippet']['thumbnails']['default']['url'];

                $repository_playlist_youtube = $this->getDoctrine()->getRepository('VideoPlayerBundle:PlayListYouTube')->findBy(['playlistid' => $idplaylist]);

                    if(!empty($repository_playlist_youtube))
                    {
                        $queryBuilder = $em->createQueryBuilder();
                        $queryBuilder->update(PlayListYouTube::class, 'u')
                        ->set('u.playlisttitle', ':playlisttitle')
                        ->set('u.playlistid', ':playlist_id')
                        ->set('u.playlistthumbnails', ':playlist_thumbnails')
                        ->set('u.playlistusername',':playlist_username')
                        ->where('u.playlistid = :playlist_id')
                        ->setParameter('playlisttitle',$titleplaylist)
                        ->setParameter('playlist_id',$idplaylist)
                        ->setParameter('playlist_thumbnails',$thumbnailplaylist)
                        ->setParameter('playlist_username',''.$user.'');
                        $query = $queryBuilder->getQuery();

                        $query->execute();
                        $em->flush();

                    }
                    else
                    {
                        $youtube_playlist_user = new PlayListYouTube();
    
                        $youtube_playlist_user->setPlaylistid($idplaylist);
                        $youtube_playlist_user->setPlaylisttitle($titleplaylist);
                        $youtube_playlist_user->setPlaylistthumbnails($thumbnailplaylist);
                        $youtube_playlist_user->setEnabled(1);
                        $youtube_playlist_user->setPlaylistusername($user);

                        $em->persist($youtube_playlist_user);
                        $em->flush();
                    }

                $i=$i+1;
            }
        
            $videos=$youtube->searchChannelVideos('',$channel_id, $maxResults = 50, $order = null);
            $array = json_decode(json_encode($videos),true);
            $lenght_array=count($array);
            $i=0;

        
            while($i<$lenght_array)
            {
                $video_id=$array[$i]['id']['videoId'];
                $video_title=$array[$i]['snippet']['title'];
                $video_description=$array[$i]['snippet']['description'];
                $video_thumbnails=$array[$i]['snippet']['thumbnails']['default']['url'];

                $repository_main_video = $this->getDoctrine()->getRepository('VideoPlayerBundle:MainVideo')->findBy(['videoId' => $video_id]);
          
                        if(!empty($repository_main_video))
                        {
                                $queryBuilder = $em->createQueryBuilder();
                                $queryBuilder->update(MainVideo::class, 'u')
                                ->set('u.title', ':title')
                                ->set('u.videoId', ':video_id')
                                ->set('u.videoServer', ':video_server')
                                ->set('u.thumb', ':thumb')
                                ->set('u.videoUrl', ':video_url')
                                ->set('u.username', ':username')
                                ->set('u.enabled', ':enabled')
                                ->set('u.description', ':description')
                                ->where('u.videoId = :video_id')
                                ->setParameter('title',$video_title)
                                ->setParameter('video_id',$video_id)
                                ->setParameter('video_server',1)
                                ->setParameter('thumb', $video_thumbnails)
                                ->setParameter('video_url','https://www.youtube.com/watch?v='.$video_id)
                                ->setParameter('username',''.$user.'')
                                ->setParameter('enabled',1)
                                ->setParameter('description',$video_description);
                                $query = $queryBuilder->getQuery();
    
                                $query->execute();
                                $em->flush();
                        }
                        else
                        {
                                $youtube_video_user = new MainVideo();
    
                                $youtube_video_user->setVideoId($video_id);
                                $youtube_video_user->setTitle($video_title);
                                $youtube_video_user->setVideoUrl('https://www.youtube.com/watch?v='.$video_id);
                                $youtube_video_user->setVideoServer(1);
                                $youtube_video_user->setDescription($video_description);
                                $youtube_video_user->setThumb($video_thumbnails);
                                $youtube_video_user->setEnabled(1);
                                $youtube_video_user->setUsername($user);

                                $em->persist($youtube_video_user);
                                $em->flush();
                        }

                $i=$i+1;
            }     
            
            }

        $access_token = $this->getService();

        return $this->render(':admin/dashboard:index.html.twig', [
                'ACCESS_TOKEN_FROM_SERVICE_ACCOUNT' => $access_token['access_token'],
            ]
        );
    }

    private function getService()
    {
        $key_file_location = $this->getParameter('kernel.root_dir').'/../src/AppBundle/Keyfile/test-4adb54f2f38d.json';
        if (file_exists($key_file_location)) {
            $serviceAccount = new ServiceAccountCredentials(Google_Service_Analytics::ANALYTICS_READONLY, $key_file_location);
            $serviceAccount->fetchAuthToken();

            return $serviceAccount->getLastReceivedToken();
        }

        return null;
    }

}
