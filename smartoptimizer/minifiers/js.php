<?php
function write_file($filename,$data="")
{
    $f = @fopen($filename,"w");
    if(!$f) return false;
    @fwrite($f,$data);
    @fclose($f);
    return true;
}
function load_file($filename,$all=true)
{
    if(!@is_readable($filename)) return false;

    $f = @fopen($filename,"r");
    $data = @fread($f,$all ? filesize($filename) : $all);
    @fclose($f);
    return $data;
}
/**
 * SmartOptimizer JavaScript Minifier
 */
function minify_js_old($str)
{
    $res = '';
    $maybe_regex = true;
    $i=0;
    $current_char = '';
    while ($i+1<strlen($str))
    {
        if ($maybe_regex && $str[$i]=='/' && $str[$i+1]!='/' && $str[$i+1]!='*' && @$str[$i-1]!='*')
        {//regex detected
            if (strlen($res) && $res[strlen($res)-1] === '/') $res .= ' ';
            do
            {
                if ($str[$i] == '\\')
                {
                    $res .= $str[$i++];
                } elseif ($str[$i] == '[')
                {
                    do
                    {
                        if ($str[$i] == '\\')
                        {
                            $res .= $str[$i++];
                        }
                        $res .= $str[$i++];
                    } while ($i<strlen($str) && $str[$i]!=']');
                }
                $res .= $str[$i++];
            } while ($i<strlen($str) && $str[$i]!='/');
            $res .= $str[$i++];
            $maybe_regex = false;
            continue;
        } elseif ($str[$i]=='"' || $str[$i]=="'")
        {//quoted string detected
            $quote = $str[$i];
            do
            {
                if ($str[$i] == '\\')
                {
                    $res .= $str[$i++];
                }
                $res .= $str[$i++];
            } while ($i<strlen($str) && $str[$i]!=$quote);
            $res .= $str[$i++];
            continue;
        } elseif ($str[$i].$str[$i+1]=='/*' && @$str[$i+2]!='@')
        {//multi-line comment detected
            $i+=3;
            while ($i<strlen($str) && $str[$i-1].$str[$i]!='*/') $i++;
            if ($current_char == "\n") $str[$i] = "\n";
            else $str[$i] = ' ';
        } elseif ($str[$i].$str[$i+1]=='//')
        {//single-line comment detected
            $i+=2;
            while ($i<strlen($str) && $str[$i]!="\n") $i++;
        }



        $LF_needed = false;
        if (preg_match('/[\n\r\t ]/', $str[$i]))
        {
            if (strlen($res) && preg_match('/[\n ]/', $res[strlen($res)-1]))
            {
                if ($res[strlen($res)-1] == "\n") $LF_needed = true;
                $res = substr($res, 0, -1);
            }
            while ($i+1<strlen($str) && preg_match('/[\n\r\t ]/', $str[$i+1]))
            {
                if (!$LF_needed && preg_match('/[\n\r]/', $str[$i])) $LF_needed = true;
                $i++;
            }
        }

        if (strlen($str) <= $i+1) break;

        $current_char = $str[$i];

        if ($LF_needed) $current_char = "\n";
        elseif ($current_char == "\t") $current_char = " ";
        elseif ($current_char == "\r") $current_char = "\n";

        // detect unnecessary white spaces
        if ($current_char == " ")
        {
            if (strlen($res) &&
                    (
                    preg_match('/^[^(){}[\]=+\-*\/%&|!><?:~^,;"\']{2}$/', $res[strlen($res)-1].$str[$i+1]) ||
                            preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-1].$str[$i+1]) // for example i+ ++j;
            )) $res .= $current_char;
        } elseif ($current_char == "\n")
        {
            if (strlen($res) &&
                    (
                    preg_match('/^[^({[=+\-*%&|!><?:~^,;\/][^)}\]=+\-*%&|><?:,;\/]$/', $res[strlen($res)-1].$str[$i+1]) ||
                            (strlen($res)>1 && preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-2].$res[strlen($res)-1])) ||
                            preg_match('/^(\+\+)|(--)$/', $current_char.$str[$i+1]) ||
                            preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-1].$str[$i+1])// || // for example i+ ++j;
            )) $res .= $current_char;
        } else $res .= $current_char;

        // if the next charachter be a slash, detects if it is a divide operator or start of a regex
        if (preg_match('/[({[=+\-*\/%&|!><?:~^,;]/', $current_char)) $maybe_regex = true;
        elseif (!preg_match('/[\n ]/', $current_char)) $maybe_regex = false;

        $i++;
    }
    if ($i<strlen($str) && preg_match('/[^\n\r\t ]/', $str[$i])) $res .= $str[$i];
    return $res;
}
/**
 * SmartOptimizer JavaScript Minifier by Balor, using Closure Compiler.
 */
function minify_js($str)
{
    global $settings;
    if($_SERVER["SERVER_NAME"]=='127.0.0.1')
        return minify_js_old($str);

    $hash=crc32($str);
    $urlPath=$settings['cacheDir']."js/JS_$hash";
    $filePath='./'.$urlPath;
    if(file_exists($filePath))
        return load_file($filePath);

    touch($filePath);
    chmod($filePath, 0777);
    write_file($filePath, $str);
    $url='http://'.$_SERVER["HTTP_HOST"].substr($_SERVER['PHP_SELF'],0,-9).$urlPath;
    $post_data = "compilation_level=SIMPLE_OPTIMIZATIONS&output_format=text&output_info=compiled_code&code_url=$url";

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, "http://closure-compiler.appspot.com/compile");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($curl);
    curl_close($curl);
    write_file($filePath, $res);
    chmod($filePath,0655);
    return $res;



}
?>
