<?php

namespace VideoPlayerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Multiplayer;
use VideoPlayerBundle\Youtube\Youtube;
use VideoPlayerBundle\YouTubeDownloader\Masih\YoutubeDownloader\YoutubeDownloader;


class VideoPlayerController extends Controller
{

    public function listplaylistAction()
    {
        //$user =$this->get('security.token_storage')->getToken()->getUser();
        $user=$_GET['pseudo'];

        $youtube = new Youtube(array('key' => 'AIzaSyCeHo3HfcxfHwgOTtqDr2yQPxkUoNOVeS8'));
        $video = $youtube->getVideoInfo('rie-hPVJ7Sw');

        $repository_youtube_infos = $this->getDoctrine()->getRepository('VideoPlayerBundle:PlayListYouTube')->findBy(['playlistusername' => ''.$user.'']);
        $repository_video_infos = $this->getDoctrine()->getRepository('VideoPlayerBundle:MainVideo')->findBy(['username' => ''.$user.'']);
        $nb_playlist=count($repository_youtube_infos);
        $nb_video=count($repository_video_infos);

        $Multiplayer = new Multiplayer\Multiplayer();
        
        $options = array(
	    'autoPlay' => true,
        'foregroundColor' => 'BADA55',
        'showInfos' => true,
        );

        $i=0;
        $tab_playlist_video_id=array();
        
        while($i<$nb_playlist)
        {
            $playlist_id=$repository_youtube_infos[$i]->getPlaylistid();
            $playlist_enabled=$repository_youtube_infos[$i]->getEnabled();
            $playlist=$youtube->getPlaylistItemsByPlaylistId($playlist_id);
        
            foreach($playlist as $item)
            {
                $array = json_decode(json_encode($item),true);
            
                $x=0;
                while($x<$nb_video)
                {
                    if($playlist_enabled && $repository_video_infos[$x]->getVideoId()==$array["snippet"]["resourceId"]["videoId"])
                    {
                        $tab_playlist_video_id[$playlist_id][]=$repository_video_infos[$x]->getVideoId();
                    } 
                    $x=$x+1;
                }

            }
           $i=$i+1;
        }

       $videos = $this->getDoctrine()->getRepository('VideoPlayerBundle:MainVideo')->findBy(['username' => ''.$user.'']);

       $video_embed_html_array=array();

       foreach($videos as $video)
       {
           if($video->getEnabled())
           {
              $video_embed_html_array[]=$Multiplayer->html($video->getVideoUrl(), $options);
           }
       } 


        return $this->render('VideoPlayerBundle:VideoPlayer:listplaylist.html.twig', array(
            'tab_embed_video' => $video_embed_html_array,
            'tab_playlist_video_id' => $tab_playlist_video_id,
            'pseudo' => $user
        ));
    }


    public function imagesvideosAction()
    {

     /* $youtube = new YoutubeDownloader('XoZuQBs3syI');

        $youtube->onProgress = function ($downloadedBytes, $fileSize, $index, $count) 
        {
            if ($count > 1) echo '[' . $index . ' of ' . $count . ' videos] ';
        
            if ($fileSize > 0)
		        echo "\r" . 'Downloaded ' . $downloadedBytes . ' of ' . $fileSize . ' bytes [%' . number_format($downloadedBytes * 100 / $fileSize, 2) . '].';
	        else
		        echo "\r" . 'Downloading...'; // File size is unknown, so just keep downloading
        };

        $youtube->download();

        $ffmpeg = $this->get('dubture_ffmpeg.ffmpeg');

        $video = $ffmpeg->open('/your/source/folder/input.avi');

        $video
        ->filters()
        ->resize(new Dimension(1280, 720), ResizeFilter::RESIZEMODE_INSET)
        ->synchronize();

        $video->save(new X264(), '/your/target/folder/video.mp4');*/


        return $this->render('VideoPlayerBundle:VideoPlayer:imagesvideos.html.twig', array(
            
        ));
    }


    public function infosblockAction()
    {


        return $this->render('VideoPlayerBundle:VideoPlayer:infosblock.html.twig', array(
            
        ));
    }

    public function introAction()
    {

        $repository_file_infos = $this->getDoctrine()->getRepository('FileManagerBundle:DownloadFileCounter')->findBy(['owner' => 'vertingo']);

        $i=0;
        $tab_file_infos=array();

        foreach($repository_file_infos as $file)
        {
            $tab_file_infos[$i]['url']=$file->getFilepath();
            $tab_file_infos[$i]['title']=$file->getFilename();
            $tab_file_infos[$i]['download_counter']=$file->getDownloadCounter();
            $tab_file_infos[$i]['mime_type']=$file->getMimeType();
            $i=$i+1;
        }

        //var_dump($tab_file_infos);
        
        return $this->render('VideoPlayerBundle:VideoPlayer:intro.html.twig', array(
            'tab_file_infos' => $tab_file_infos
        ));
    }


    public function slidersAction()
    {


        return $this->render('VideoPlayerBundle:VideoPlayer:sliders.html.twig', array(
            
        ));
    }


    public function socialblocksAction()
    {


        return $this->render('VideoPlayerBundle:VideoPlayer:socialblocks.html.twig', array(
            
        ));
    }
	
	
    public function myownvertingowebsiteAction()
    {


        return $this->render('VideoPlayerBundle:VideoPlayer:myownvertingowebsite.html.twig', array(
            
        ));
    }


    public function testimonialsAction()
    {


        return $this->render('VideoPlayerBundle:VideoPlayer:testimonials.html.twig', array(
            
        ));
    }



}
