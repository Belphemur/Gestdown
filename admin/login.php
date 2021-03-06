<?php
function _toAPRMD5($value, $count)
{
    /* 64 characters that are valid for APRMD5 passwords. */
    $APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    $aprmd5 = '';
    $count = abs($count);
    while (-- $count)
    {
        $aprmd5 .= $APRMD5[$value & 0x3f];
        $value >>= 6;
    }
    return $aprmd5;
}

/**
 * Converts hexadecimal string to binary data.
 *
 * @access private
 * @param string $hex  Hex data.
 * @return string  Binary data.
 * @since 1.5
 */
function _bin($hex)
{
    $bin = '';
    $length = strlen($hex);
    for ($i = 0; $i < $length; $i += 2)
    {
        $tmp = sscanf(substr($hex, $i, 2), '%x');
        $bin .= chr(array_shift($tmp));
    }
    return $bin;
}
function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
{
    // Encrypt the password.
    switch ($encryption)
    {
        case 'crypt' :
        case 'crypt-des' :
            if ($seed)
            {
                return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
            } else
            {
                return substr(md5(mt_rand()), 0, 2);
            }
            break;

        case 'crypt-md5' :
            if ($seed)
            {
                return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
            } else
            {
                return '$1$'.substr(md5(mt_rand()), 0, 8).'$';
            }
            break;

        case 'crypt-blowfish' :
            if ($seed)
            {
                return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
            } else
            {
                return '$2$'.substr(md5(mt_rand()), 0, 12).'$';
            }
            break;

        case 'ssha' :
            if ($seed)
            {
                return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
            } else
            {
                return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
            }
            break;

        case 'smd5' :
            if ($seed)
            {
                return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
            } else
            {
                return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
            }
            break;

        case 'aprmd5' :
        /* 64 characters that are valid for APRMD5 passwords. */
            $APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

            if ($seed)
            {
                return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
            } else
            {
                $salt = '';
                for ($i = 0; $i < 8; $i ++)
                {
                    $salt .= $APRMD5 {
                            rand(0, 63)
                            };
                }
                return $salt;
            }
            break;

        default :
            $salt = '';
            if ($seed)
            {
                $salt = $seed;
            }
            return $salt;
            break;
    }
}
function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
{
    // Get the salt to use.
    $salt = getSalt($encryption, $salt, $plaintext);

    // Encrypt the password.
    switch ($encryption)
    {
        case 'plain' :
            return $plaintext;

        case 'sha' :
            $encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext));
            return ($show_encrypt) ? '{SHA}'.$encrypted : $encrypted;

        case 'crypt' :
        case 'crypt-des' :
        case 'crypt-md5' :
        case 'crypt-blowfish' :
            return ($show_encrypt ? '{crypt}' : '').crypt($plaintext, $salt);

        case 'md5-base64' :
            $encrypted = base64_encode(mhash(MHASH_MD5, $plaintext));
            return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;

        case 'ssha' :
            $encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext.$salt).$salt);
            return ($show_encrypt) ? '{SSHA}'.$encrypted : $encrypted;

        case 'smd5' :
            $encrypted = base64_encode(mhash(MHASH_MD5, $plaintext.$salt).$salt);
            return ($show_encrypt) ? '{SMD5}'.$encrypted : $encrypted;

        case 'aprmd5' :
            $length = strlen($plaintext);
            $context = $plaintext.'$apr1$'.$salt;
            $binary = _bin(md5($plaintext.$salt.$plaintext));

            for ($i = $length; $i > 0; $i -= 16)
            {
                $context .= substr($binary, 0, ($i > 16 ? 16 : $i));
            }
            for ($i = $length; $i > 0; $i >>= 1)
            {
                $context .= ($i & 1) ? chr(0) : $plaintext[0];
            }

            $binary = JUserHelper::_bin(md5($context));

            for ($i = 0; $i < 1000; $i ++)
            {
                $new = ($i & 1) ? $plaintext : substr($binary, 0, 16);
                if ($i % 3)
                {
                    $new .= $salt;
                }
                if ($i % 7)
                {
                    $new .= $plaintext;
                }
                $new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
                $binary = JUserHelper::_bin(md5($new));
            }

            $p = array ();
            for ($i = 0; $i < 5; $i ++)
            {
                $k = $i +6;
                $j = $i +12;
                if ($j == 16)
                {
                    $j = 5;
                }
                $p[] = _toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
            }

            return '$apr1$'.$salt.'$'.implode('', $p).JUserHelper::_toAPRMD5(ord($binary[11]), 3);

        case 'md5-hex' :
        default :
            $encrypted = ($salt) ? md5($plaintext.$salt) : md5($plaintext);
            return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;
    }
}

function login()
{
    require_once('conf.php');
    require_once('./functions/dojo-form.php');
    require_once("../classes/Login.class.php");
    global $joomla,$sql_serveur,$sql_login, $sql_pass, $sql_bdd;
    $db = ezDB::getInstance();
    $db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
    
    
    try
    {
        $log = Login::getInstance(604800,'GestDown');
    }
    catch(Exception $e)
    {
        dojoForm($e->getMessage());
        die();
    }
    if(isset($_GET['logout']))
    {
        $log->logout();
        header("Location: index.php");
        die();
    }
    if($log->checkIfLogged())
        return true;
    else
    {
        if(isset($_POST['do_login']))
        {
            try
            {
                if($joomla)
                {
                    $query = 'SELECT `password`'
                            . ' FROM `jos_users`'
                            . ' WHERE username="'.$_POST['userName'].'"';
                    $tmpPass=$db->get_var($query);

                    if($tmpPass)
                    {
                        $parts	= explode( ':', $tmpPass );
                        $crypt	= $parts[0];
                        $salt	= @$parts[1];
                        $testcrypt = getCryptedPassword($_POST['password'], $salt);
                        $log->login($_POST['userName'],$testcrypt.':'.$salt);
                    }
                    else
                    {
                        dojoForm('User not found');
                        die();
                    }
                }
                else
                    $log->login($_POST['userName'],md5($_POST['password']));
            }
            catch(Exception $e)
            {
                dojoForm($e->getMessage());
                die();
            }
        }
        else
        {
            dojoForm('');
            die();
        }
    }
}
function login_chk($a)
{
    return login();
}


?>