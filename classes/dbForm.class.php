<?php
/**
 * G�n�rateur de formulaire suivant la base de donn�e
 *@package DBFORM
 *@access public
 *@author Antoine Aflalo (antoineaf@gmail.com)
 *@return string
 *@version 1.0;
 */
class dbForm
{
	protected $db,$table, $key,$name, $value;
	/**
	* Constructeur de la classe
	*
	* @param ezSQL_mysql $db
	*	Le gestionnaire de base de donn�e
	* @param string $table
	*	string contenant le nom de la table
	*/
	function __construct(ezSQL_mysql $db, $table)
	{
		$this->db=$db;
		$this->table=$table;
		$this->name=array();	
		$this->key=array();
		$this->value=array();
		$this->getKeys();
	}
	/**
	* R�cup�re le nom des champs de la table
	*
	* @param none
	*/
	protected function getKeys()
	{
		$sql="SELECT * FROM {$this->table} LIMIT 1";
		$keys=$this->db->get_row($sql);
		$keys=array_keys($keys);
		foreach($keys as $data)
		{
			$this->key[$data]='text';
		}
	}
	/**
	* Ajouter un champs � ignorer
	*
	* @param unknown_type $ignore
	*	Tableau ou string donnant le champs en question
	*/
	function ignore($ignore)
	{
		if(is_array($ignore))
		{
			foreach($ignore as $todo)
				unset($this->key[$todo]);
		}
		else
		{
			unset($this->key[$ignore]);
		}
	}
	/**
	* Ajoute un champ qui ne se trouve pas dans la BD
	*
	* @param string/array $id
	*	id r�el du champ
	* @param string $type
	*	type du champ
	* @param string $nom
	*	nom affich� du champ
	* @param string $place
	*	endroit ou l'on veut ajout� le champ sinon � la fin
	* @param mixed $value
	*	repr�sente la valeur par d�fault du champs
	*/
	function addField($id,$type='text',$nom=NULL,$place=NULL, $value=' ')
	{
		if(! isset($this->key[$id]))
		{
			if($place!=null)
			{
				$sValue=array();
				$sKeys=array();
				foreach($this->key as $keys => $val)
				{
					$sValue[]=$val;
					$sKeys[]=$keys;
					if($keys==$place)
					{
						$sValue[]=$type;
						$sKeys[]=$id;
					}	
				}
				$this->key=array_combine($sKeys,$sValue);
				
			}
			else
				$this->key[$id]=$type;
			if($nom!=null)
				$this->name[$id]=$nom;
			if($value!=' ')
				$this->value[$id]=$value;
		}
		else
			throw new exception('Champ ('.$id.') existe d�j�, pour changer le nom utilisez changeDisplayName pour changer le type changeType et pour d�finir une valeur setValue',4);
			
	}
	
	/**
	 * Permet de d�finir la valeur par d�faut d'un champ
	 *
	 * @param mixed $id le champ
	 * @param mixed $value la valeur
	 * @return void
	 *
	 */
	function setValue($id,$value=' ')
	{
		if(is_array($id))
		{
			foreach($id as $key =>$val)
			{
				if(isset($this->key[$key]))
					$this->value[$key]=$val;
				else
					throw new exception('Champ ('.$key.') non pr�sent ou ignor�',1);
			}	
		}
		else if($value != null)
		{
			if(isset($this->key[$key]))
				$this->value[$id]=$value;
			else
				throw new exception('Champ ('.$id.') non pr�sent ou ignor�',1);
		}
		else
			throw new exception('Si le champ $id n\'est pas un array, $name doit �tre d�fini',2);
		
				
	}
	
	/**
	* D�finit le nom affich� pour un champ
	*
	* @param string/array $id
	*	nom r�el du champ ou array associatif avec nom champ => nom
	* @param string $name
	*	nom affich� du champ
	*/
	function changeDisplayName($id,$name=NULL)
	{
		if(is_array($id))
		{
			foreach($id as $key =>$val)
			{
				if(isset($this->key[$key]))
					$this->name[$key]=$val;
				else
					throw new exception('Champ ('.$key.') non pr�sent ou ignor�',1);
			}	
		}
		else if($name != null)
		{
			if(isset($this->key[$key]))
				$this->name[$id]=$name;
			else
				throw new exception('Champ ('.$id.') non pr�sent ou ignor�',1);
		}
		else
			throw new exception('Si le champ $id n\'est pas un array, $name doit �tre d�fini',2);
	}
	/**
	* D�finit le type pour un champ
	*
	* @param string/array $id
	*	nom du champ ou array associatif avec nom champ => type
	* @param string $type
	*	type du champ
	*/
	function changeType($id,$type=NULL)
	{
		if(is_array($id))
		{
			foreach($id as $key =>$val)
			{
				if(isset($this->key[$key]))
					$this->key[$key]=$val;
				else
					throw new exception('Champ ('.$key.') non pr�sent ou ignor�',1);
			}	
		}
		else if($type !=null)
		{
			if(isset($this->key[$id]))
				$this->key[$id]=$type;
			else
				throw new exception('Champ ('.$id.') non pr�sent ou ignor�',1);
		}
		else
			throw new exception('Si le champ $id n\'est pas un array, $type doit �tre d�fini',3);
			
	}
	function display()
	{
		foreach($this->key as $key => $var)
		{
			if(isset($this->name[$key]))
				echo $this->name[$key],' :	', $var,'<br />'."\n";
			else
				echo $key,' :	', $var,'<br />'."\n";
			if(isset($this->value[$key]))
			{
				echo 'val =>',($this->value[$key])	,'<br />'."\n";			
			}
		}
	}
	
}


?>