<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$qualArray = array("MQ", "HD", "FHD");

function crc16($data) {
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++)
    {
        $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
        $x ^= $x >> 4;
        $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
    }
    return $crc;
}

function rand_uniqid($in, $to_num = false, $pad_up = false, $passKey = null) {
    $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($passKey !== null)
    {
        // Although this function's purpose is to just make the
        // ID short - and not so much secure,
        // you can optionally supply a password to make it harder
        // to calculate the corresponding numeric ID

        for ($n = 0; $n < strlen($index); $n++)
        {
            $i[] = substr($index, $n, 1);
        }

        $passhash = hash('sha256', $passKey);
        $passhash = (strlen($passhash) < strlen($index)) ? hash('sha512', $passKey) : $passhash;

        for ($n = 0; $n < strlen($index); $n++)
        {
            $p[] = substr($passhash, $n, 1);
        }

        array_multisort($p, SORT_DESC, $i);
        $index = implode($i);
    }

    $base = strlen($index);

    if ($to_num)
    {
        // Digital number  <<--  alphabet letter code
        $in = strrev($in);
        $out = 0;
        $len = strlen($in) - 1;
        for ($t = 0; $t <= $len; $t++)
        {
            $bcpow = bcpow($base, $len - $t);
            $out = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
        }

        if (is_numeric($pad_up))
        {
            $pad_up--;
            if ($pad_up > 0)
            {
                $out -= pow($base, $pad_up);
            }
        }
        $out = sprintf('%F', $out);
        $out = substr($out, 0, strpos($out, '.'));
    } else
    {
        // Digital number  -->>  alphabet letter code
        if (is_numeric($pad_up))
        {
            $pad_up--;
            if ($pad_up > 0)
            {
                $in += pow($base, $pad_up);
            }
        }

        $out = "";
        for ($t = floor(log($in, $base)); $t >= 0; $t--)
        {
            $bcp = bcpow($base, $t);
            $a = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in = $in - ($a * $bcp);
        }
        $out = strrev($out); // reverse
    }

    return $out;
}

function openXML($filename) {
    // Ouverture du fichier
    chmod($filename, 0777);
    $memoryfile = new DOMDocument();
    $memoryfile->preserveWhiteSpace = false;
    $memoryfile->formatOutput = true;
    $memoryfile->load($filename);

    //On retourne le fichier
    return $memoryfile;
}

function saveSimpleXML($xml, $path) {
    $dom_sxe = dom_import_simplexml($xml);
    $memoryfile = new DOMDocument();
    $memoryfile->preserveWhiteSpace = false;
    $memoryfile->formatOutput = true;
    $dom_sxe = $memoryfile->importNode($dom_sxe, true);
    $dom_sxe = $memoryfile->appendChild($dom_sxe);
    $ok = $memoryfile->save($path);
    chmod($path, 0777);
    return $ok;
}

function addSerieXML($nom, $add=false) {
    $result = new Result();
    $filename = '../xml/series.xml';
    $xml = simplexml_import_dom(openXML($filename));
    $serie = $xml->addChild('serie');
    $serie->addChild('nom', $nom);
    if ($add === false)
        $serie->addChild('files', '');
    else
        $serie->addChild('files', "$add");
    $serie->addChild('totaldl', 0);
    $folder = rand_uniqid(crc16($nom) * mt_rand(1, 5), false);
    $serie->addChild('folder', $folder);
    $serie->addChild('query', $folder);
    saveSimpleXML($xml, $filename);
    mkdir('../xml/' . $folder);
    chmod("../xml/$folder", 0777);
    $result->folder = $folder;
    $result->query = $folder;
    return $result;
}

function parsingMyuploads($url, $txt=false) {
    global $qualArray;
    if ($txt === false)
    {
        $file = @file($url);
        if ($file === false)
            return false;
        $include = implode("", $file);
    }
    else
        $include=$txt;
    if (empty($include))
        return false;
    $hosters = simplexml_load_file('../xml/hosters.xml');
    $tab = array();
    $tab = explode("\n", $include);
    $files = array();
    $result = new Result();
    $hoster = array();
    $link = array();
    for ($i = 0; $i < count($tab) - 1; $i+=2)
    {

        if (preg_match('/\[(.+)\](.+)_([0-9]*|\d*\.\d{1}?\d*)_(HD|MQ|FHD)(.+|)\.(mp4|avi|mkv)/', $tab[$i], $matches))
        {
            $saveFile = $tab[$i];

            $result->serie = $matches[2];
            $result->ep = $matches[3];

            $result->file = trim($tab[$i]);
            $i+=2;
            foreach ($hosters as $data)
            {
                if (strpos($tab[$i], (string) $data->name))
                {
                    $hoster[] = (string) $data->short;
                    $link[] = trim($tab[$i]);
                    break;
                }
            }
            if (!isset($tab[$i + 2]) || $tab[$i + 2] != $saveFile)
            {
                $result->hoster = $hoster;
                $result->link = $link;
                $hoster = array();
                $link = array();
                if (!isset($files[$result->file]))
                    $files[$result->file] = new Result();
                $files[$result->file]->add($result, false);
                $result = new Result();
            }
        }
    }
    return $files;
}

function delEpisodeXml($folder, $ep) {
    $path = '../xml/series.xml';
    $xml = simplexml_import_dom(openXML($path));
    $newFiles = '';
    foreach ($xml as $serie)
    {
        if ($serie->folder == $folder)
        {
            $files = explode(',', $serie->files);

            for ($i = 0; $i < count($files); $i++)
            {
                if ($files[$i] != $ep)
                {
                    $newFiles.="$files[$i],";
                }
            }
            $newFiles = substr($newFiles, 0, -1);
            $serie->files = $newFiles;
            break;
        }
    }
    saveSimpleXML($xml, $path);
    return unlink("../xml/$folder/$ep.xml");
}

function autoModifXml($path, Result $info) {
    $xml = simplexml_import_dom(openXML($path));
    $xml->downloaded = time();
    global $qualArray;
    foreach ($qualArray as $quality)
    {
        if (!$info->$quality->empty)
        {
            foreach ($info->$quality->get_vars() as $host => $link)
            {
                if ($host != 'empty')
                {
                    $xml->$quality->$host = $link;
                }
            }
        }
    }
    return saveSimpleXML($xml, $path);
}

function autoAddEpisode(Result $serie, Result $episode, SimpleXMLElement $xml) {
    $path = '../xml/series.xml';
    $id = (int) $episode->ep;
    if ($serie->found)
    {
        $serie->xml->files.=",$id";
        saveSimpleXML($xml, $path);
    }

    $save = "../xml/$serie->folder/$id.xml";
    $filename = '../xml/template.xml';
    $xml = simplexml_import_dom(openXML($filename));
    $xml->date = time();
    $xml->id = $id;
    saveSimpleXML($xml, $save);
    return autoModifXml($save, $episode);
}

function autoXml($url, $txt=false) {
    global $sql_serveur, $sql_login, $sql_pass, $sql_bdd;
    $db = ezDB::getInstance();
    $db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
    $files = array();
    $files = parsingMyuploads($url, $txt);
    if ($files === false)
        return false;
    $links = new Result();
    $result = array();
    $ok = false;
    $type;
    foreach ($files as $file)
    {

        if ($db->pQuery('SELECT f.fileID FROM mirror_files f WHERE `name`=?', array('s', $file->file)))
        {
            $fId = $db->getVar();
            $i = 0;
            $mirrorLinks = array();
            $links = $file->link;
            
            foreach ($db->getResults('SELECT hoster,`link` FROM mirror_links WHERE `fileID`="' . $fId . '"') as $mir)
                $mirrorLinks[$mir->hoster] = $mir->link;

            foreach ($file->hoster as $host)
            {
                $ok = true;
                try
                {
                    if (!isset($mirrorLinks[$host]) || (isset($mirrorLinks[$host]) && $mirrorLinks[$host] != $links[$i]))
                        $db->pQuery('INSERT INTO `mirror_links` (`serieID`, `epNum`, `quality`, `hoster`, `link`, `fileID`)
                                    VALUES (NULL, NULL, NULL, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE link=?;', array('ssss', $host, $links[$i], $fId, $links[$i]));
                } catch (Exception $exc)
                {
                    echo $exc->getTraceAsString();
                    $ok = false;
                }
                $i++;
            }
            $type = 'Màj';
        } else
        {
            $ok = true;
            $fId = sprintf("%u", crc32(uniqid())) . crc16($file->file);
            $fId = rand_uniqid($fId, false, 9, 'gestdown');
            try
            {
                $db->pQuery('INSERT INTO `mirror_files` (`fileID`, `name`, `added`) VALUES
                                    (?, ?, ?);', array('sss', $fId, $file->file, date('Y-m-d\TH:i:s')));
            } catch (Exception $exc)
            {
                echo $exc;
                $ok = false;
            }
            if ($ok)
            {
                $i = 0;
                foreach ($file->hoster as $host)
                {
                    $links = $file->link;
                    try
                    {
                        $db->pQuery('INSERT INTO `mirror_links` (`serieID`, `epNum`, `quality`, `hoster`, `link`, `fileID`)
                                    VALUES (NULL, NULL, NULL, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE link=?;', array('ssss', $host, $links[$i], $fId, $links[$i]));
                    } catch (Exception $exc)
                    {
                        echo $exc;
                        $ok = false;
                    }
                    $i++;
                }
            }
            $type = 'Ajout';
        }
        if ($ok)
            $result[$file->file] = "file/$fId/$file->file,$type";
        else
            $result[$file->file] = "ERROR";
    }
    ksort($result);
    return $result;
}

function addEpisode($folder, $ep, $mq, $hd, $fhd) {
    $series = '../xml/series.xml';
    $xmlQuery = simplexml_import_dom(openXML($series));
    foreach ($xmlQuery->serie as $selected)
    {
        if ($selected->folder == trim($folder))
        {
            $selected->files.=",$ep";
            break;
        }
    }
    saveSimpleXML($xmlQuery, $series);

    $save = "../xml/$folder/$ep.xml";
    $filename = '../xml/template.xml';
    $xml = simplexml_import_dom(openXML($filename));
    $xml->date = time();
    $xml->id = $ep;
    $xmlMQ = $xml->MQ;
    $xmlHD = $xml->HD;
    $xmlFHD = $xml->FHD;
    foreach ($mq as $host => $link)
    {
        $xmlMQ->addChild($host, $link);
    }
    foreach ($hd as $host => $link)
    {
        $xmlHD->addChild($host, $link);
    }
    foreach ($fhd as $host => $link)
    {
        $xmlFHD->addChild($host, $link);
    }
    saveSimpleXML($xml, $save);
    return true;
}

?>
