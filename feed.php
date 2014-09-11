<?PHP

    // CONFIGURATION SETTINGS

    $dropboxID = "123"; // Your Dropbox ID    
    date_default_timezone_set('America/New_York');// Change to your time zone. See http://php.net/manual/en/timezones.php for a list of supported time zones.

    $path = "feeds/later"; // Create this path in the 'Public' folder inside your dropbox.

    $keepVids = 10; // Number of videos you want to keep in the folder. If you have more videos than this in your Dropbox, the script will automatically remove the oldest X videos.

    echo "Generating podcast feed.\n";
 
    //CONSTRUCT RSS FEED HEADERS
    $output = '<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
    <channel>
        <title>Watch Later</title>
        <description>Some stuff dumped to watch later.</description>
        <link>http://carsten.io</link>
        <language>en-us</language>
        <itunes:explicit>no</itunes:explicit>
        <copyright>Do what you want...</copyright>
        <itunes:image href="https://dl.dropboxusercontent.com/u/".$dropboxID."/".$path."/later.png" />
        <itunes:category text="Video" />';
 
    // we'll keep all videos in an associative array 
    // that will allow us to delete old videos later 
    $videos = array(); 

    // iterate through videos to generate RSS feed
    $dir = new DirectoryIterator(dirname("."));
    foreach ($dir as $fileinfo) {
        
        if (!$fileinfo->isDot()) {
            $filename = $fileinfo->getFilename();
            
            if(substr($filename, -4) === ".mp4"){   

                $videos[filemtime($filename)] = $filename; // save for the clean up                
            
                $fileurl = rawurlencode($filename);
                $link = 'https://dl.dropboxusercontent.com/u/'.$dropboxID.'/'.$path.'/'.$fileurl;
                $output .= '
                <item>
                    <title>'.$filename.'</title>
                    <description>'.$filename.'</description>
                    <link>'.$filename.'</link>
                    <guid>'.$filename.'</guid>
                    <enclosure url="'.$link.'" length="'.filesize($filename).'" type="video/mp4" />
                    <pubDate>'.date ("r", filemtime($filename)).'</pubDate>
                </item>';
            }   
        }
    }
    
   $output .= '
   </channel>
</rss>';
 
    file_put_contents('later.xml', $output);

    echo "Checking for old videos to delete...\n";

    // CLEAN UP: IF THERE ARE MORE THAN X VIDEOS, REMOVE THE OLDEST Y

    // sort videos by timestamp in descending order
    krsort($videos);
    $count = 0;

    foreach ($videos as $timestamp => $filename) {
        $count++;
        if($count>$keepVids){ // delete everthing after the newest X videos
            if(unlink($filename)){
                echo($filename." deleted.\n");
            } else {
                echo("Deleting ".$filename." failed.\n");
            }            
        }        
    }
     
    
    echo "That's all, folks.\n";  
 
?>