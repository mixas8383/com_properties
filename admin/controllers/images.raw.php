<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_properties
 * @copyright	Copyright (C) 2006 - 2016 Fabio Esteban Uzeltinger.
 * @email		fabiouz@gmail.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class PropertiesControllerImages extends JControllerLegacy
{
		function __construct()
	{
		parent::__construct();









	}

function &getModel($name = 'Image', $prefix = 'PropertiesModel', $config = '')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

function save_images_files()
	{
	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');
	$model = $this->getModel('images');	
	$user = JFactory::getUser();
	$idproduct = $this->input->get('idproduct');
	$path_images = JPATH_SITE.'/images/properties/';
	$path_image = JPATH_SITE.'/images/properties/images/';
	$path = JPATH_SITE.'/'.'images'.'/'.'properties'.'/'.'images'.'/'.$idproduct;
	$paththumbs = JPATH_SITE.'/'.'images'.'/'.'properties'.'/'.'images'.'/'.'thumbs'.'/'.$idproduct;				
	
	if(!JFolder::exists($path_images))
		{
		JFolder::create($path_images,0755);
		}
	if(!JFolder::exists($path_image))
		{
		JFolder::create($path_image,0755);
		}
	if(!JFolder::exists($path))
		{
		JFolder::create($path,0755);
		}
	if(!JFolder::exists($paththumbs))
		{
		JFolder::create($paththumbs,0755);
		}
//require('011');
	$files        = $this->input->files->get('Filedata', '', 'array');
//	print_r($files);
	
	$id_imagen = 0;		
	foreach ($files as &$file)
		{
		$file['name']     = JFile::makeSafe($file['name']);
		$fileExt = JFile::getExt($file['name']);		
		
		$postI['id'] = '';				
		$postI['name'] = 'reemplazar';
		$postI['alias'] = '';
		$postI['parent'] = $idproduct;
		$postI['published'] = 1;
		$postI['ordering'] = 0;
		$postI['type'] = $fileExt;
		$postI['name_image'] = '';
		$postI['date'] = date('Y-m-d H:i:s');
		$postI['uid'] = $user->id;		

		if ($imageSaved = $model->store($postI)) 
			{
			$fileName = $imageSaved->id.'_'.$idproduct.'.'.$fileExt;
			$postI['id'] = $imageSaved->id;
			$postI['name'] = $fileName;
			
			$model->store($postI);
			}else{

			}	
				
		$filePath = $path . '/' . $fileName;	
	
		if (!JFile::upload($file['tmp_name'], $filePath))
			{				// 
			echo 'Error in upload';
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'), 'error');
				return false;
			}		
		$imagenGuardada = $filePath;
		$thumb=	$paththumbs.'/'.$fileName;
		$this->CambiarTamano($imagenGuardada,200,150,$thumb);		
		}
$msg = 'Images saved';
$this->setRedirect( 'index.php?option=com_properties&view=product&layout=edit&id='.$idproduct, $msg );
	}
	
	
	
			
	
	function CambiarTamano($imagenGuardada,$max_width,$max_height,$peque)
{


$InfoImage=getimagesize($imagenGuardada);               
                $width=$InfoImage[0];
                $height=$InfoImage[1];
				$type=$InfoImage[2];
$max_height = $max_width;

	$x_ratio = $max_width / $width;
	$y_ratio = $max_height / $height;
	
if (($x_ratio * $height) < $max_height) {
		$tn_height = ceil($x_ratio * $height);
		$tn_width = $max_width;
	} else {
		$tn_width = ceil($y_ratio * $width);
		$tn_height = $max_height;
	}
$width=$tn_width;
$height	=$tn_height;



		 
switch($type)
                  {
                    case 1: //gif
                     {
                          $img = imagecreatefromgif($imagenGuardada);
                          $thumb = imagecreatetruecolor($width,$height);
                        imagecopyresampled($thumb,$img,0,0,0,0,$width,$height,imagesx($img),imagesy($img));
                        ImageGIF($thumb,$peque,100);
						
                        break;
                     }
                    case 2: //jpg,jpeg
                     {					 
                          $img = imagecreatefromjpeg($imagenGuardada);
                          $thumb = imagecreatetruecolor($width,$height);
                         imagecopyresampled($thumb,$img,0,0,0,0,$width,$height,imagesx($img),imagesy($img));
                         ImageJPEG($thumb,$peque);
                        break;
                     }
                    case 3: //png
                     {
                          $img = imagecreatefrompng($imagenGuardada);
                          $thumb = imagecreatetruecolor($width,$height);
                        imagecopyresampled($thumb,$img,0,0,0,0,$width,$height,imagesx($img),imagesy($img));
                        ImagePNG($thumb,$peque);
                        break;
                     }
                  } // switch				  

}	
	
}