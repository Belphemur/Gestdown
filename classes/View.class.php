<?php
/**
 * Générateur de vue
 *@package VIEW
 *@access public
 *@author Antoine Aflalo (antoineaf@gmail.com)
 *@return string
 *@version 1.2.0;
 */
class View extends Result
{
    private $_path;    
    
    /**
     * Constructeur de la classe
     *
     * @param String $path
     */
    public function __construct ($path)
    {
        $this->_path = $this->createPath($path);
        Result::__construct();
    }

    /**
     * Nettoie les valeurs en sortie
     *
     * @param string $var
     * @return string
     */
    public function escape ($var)
    {
        return htmlspecialchars ($var,"UTF-8");
    }
    
    /**
     * Cree le chemin d'accee des templates
     *
     * @param string $path
     * @return string
     */
    protected function createPath ($path)
    {
       $dir = rtrim ($path, "\\/" . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
       return $dir;
    }
    
    /**
     * Cree le chemin d'accès complet
     * ver le fichier
     *
     * @param string $file
     * @return string
     */
    protected function createContent ($file)
    {
        $full = $this->_path . $file;
        if (is_readable($full))
           return $this->_path . $file;
        else throw new Exception("<b>{$file}</b> n'est pas present dans {$this->_path}");
    }
    
    /**
     * Rendu d'un fichier
     *
     * @param mixed $file
     * @return string
     */
    public function render ($file)
    {
        $content = $this->createContent ($file);
        
        ob_start();
        include ($content);
        
        return ob_get_clean();
    } 
	 /**
     * Convertit un Result en View
     *
     * @param Result $res
     */
	public function convert(Result $res)
	{
		$this->set_vars($res->get_vars());
	}

    /**
     * Remove all special characted
     * @param $string
     * @return String
     */
    public function clean ($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
}        
?>
