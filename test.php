<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>test</title>
    </head>

    <body>

        <?php
        /*
          $url="http://tokyotosho.info/rss.php?filter=7";
          $xml = simplexml_load_file($url,'SimpleXMLElement',LIBXML_COMPACT);
          $include = implode("", file($url));
          //echo $include;
          $regs=array();
          $series=array(" "=>"Zero-Raws",""=>"Detective Conan");

          //print_r($xml);
          foreach ($xml->channel->item as $sortie)
          {
          foreach($series as $key => $val)
          {
          if (preg_match('/'.$val.'(.+)'.$key.'/', $sortie->title))
          //if (preg_match('/'.$key.'(.+)'.addslashes($val).'/', $sortie->title))
          {
          echo $sortie->title." : ";
          echo preg_replace('#(.+)<a href="(.+)">(.+)#', "$2",$sortie->description)."<br />\n";
          }
          }
          } */
        require_once("admin/conf.php"); //Commme d'ab
        require_once('includes/addSerieXml.php');
        unset($db);
        $db = ezDB::getInstance();
        $db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
        $series = simplexml_load_file('./xml/series.xml');
        $hosters = simplexml_load_file('./xml/hosters.xml');
        $quality = array('MQ', 'HD', 'FHD');
        foreach ($series->serie as $selected)
        {
            $folder = $selected->folder;
            $files = $selected->files;
            $query = $selected->query;
            $serie = $selected->nom;
            foreach (explode(',', $files) as $id)
            {
                $file = simplexml_load_file("./xml/$folder/$id.xml");
                $added = date('Y-m-d\TH:i:s', (string) $file->date);
                $lastDl = date('Y-m-d\TH:i:s', (string) $file->downloaded);
                foreach ($quality as $qual)
                    if (isset($file->$qual) && $file->$qual->dl > 0)
                    {
                        $rel = &$file->$qual;

                        $fileID = sprintf("%u", crc32(uniqid())).  crc16($rel->file);
                        $fileID =rand_uniqid($fileID, false, 9, 'gestdown');
                                $rel->file = (string) (isset($rel->file) ? $rel->file : '[AnT]' . str_replace(' ', '_', $serie) . '_' . ($id / 10 >= 1 ? $id : '0' . $id) . '_' . $qual . ($qual == 'MQ' ? '.avi' : '.mp4'));

                        $db->pQuery('INSERT INTO `mirror_files` (`fileID`, `name`, `downloads`, `lastDl`, `added`) VALUES
                                    (?, ?, ?, ?, ?);', array('ssiss', $fileID, $rel->file, $rel->dl, $lastDl, $added));
                        foreach ($hosters as $host)
                        {
                            if (isset($rel->{$host->short}) && !empty($rel->{$host->short}))
                            {
                                $db->pQuery('INSERT INTO `mirror_links` (`serieID`, `epNum`, `quality`, `hoster`, `link`, `fileID`)
                                    VALUES (?, ?, ?, ?, ?, ?);', array('sissss', $query, $id, strtolower($qual), $host->short, $rel->{$host->short}, $fileID));
                            }
                        }
                    }
            }
        }
        ?>
    </body>
</html>