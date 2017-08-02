<?php

/** Represents information about a folder in Gliffy 
 * @package Gliffy
 * @subpackage DataContainer
 * */
class GliffyFolder
{
    /** Name of the folder, used to refer to the folder 
     * @var string */
    public $name;

    /** Path of this folder that can be used in URLs
     * @var string
     */
    public $path;

    /** If true, this is the default folder for the account it is in 
     * @var boolean */
    public $default;

    /** Array of GliffyFolder objects reprenseting the children of this folder 
     * @var array
     * */
    public $children;

    // Creates a folder object from an xml folder, including and child folders
    public static function from_response_xml( $response  ) { 
        return GliffyFolder::from_folder_xml( $response->folders->folder );
    } 


    // Creates a folder object from an xml folder, including and child folders
    public static function from_folder_xml( $folder  ) { 
        $newFolder = new GliffyFolder( (string)   $folder->name,
                                       (bool) $folder['is-default'],
                                       (string) $folder->path );

        foreach( $folder->folder as $childFolder ) {
            $newFolder->children[] = GliffyFolder::from_folder_xml( $childFolder );
        } 
       

        return $newFolder;
    }

    public function GliffyFolder($name,$default,$path=null)
    {
        $errors = array();
        if ($name == null) $errors[] = "Folder name is required";
        if (count($errors) > 0) throw new Exception(implode(",",$errors));
        $this->name = $name;
        $this->default = $default;
        $this->path = $path;
        $this->children = array();
    }



}
?>
