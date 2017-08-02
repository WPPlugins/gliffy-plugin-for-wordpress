<?php

/** Information about a diagram in Gliffy 
 * @package Gliffy
 * @subpackage DataContainer
 * */
class GliffyDiagram
{
    /** The id of the diagram.  This is stable and can be used to reference a diagram 
     * @var integer */
    public $id;
    /** The number of versions of this diagram 
     * @var integer */
    public $num_versions;
    /** The name of this diagram 
     * @var string */
    public $name;

    /** True if diagram is public; viewable to the world 
     * @var boolean */
    public $is_public;

    /** True if diagram is private; viewable/editable only by its owner
     * @var boolean */
//    public $is_private;


    public static function from_response_xml( $response  ) { 
        $documents = array();

        foreach ($response->documents->document as $document) { 
            $documentXML = $document;

            $newDocument = new GliffyDiagram( (int)    $documentXML['id'],
                                              (int)    $documentXML['num-versions'],
                                              (string) $documentXML->{'name'},    
                                              $documentXML['is-public'] != null ? true : false
//                                              $documentXML->{'published-date'} != null ? false : true
                                        );
            
            $documents[] = $newDocument;
        }

        return $documents; 
    }



    public function GliffyDiagram($id,$num_versions,$name,$is_public)
    {
        $errors = array();
        if ($name == null) $errors[] = "Diagram Name is required";
        if ($num_versions == null) $errors[] = "Diagram num-versions is required";
        if ($id == null) $errors[] = "Diagram ID is required";

        $this->id = intval($id);
        $this->num_versions = intval($num_versions);
//        $this->is_public = $is_public ? true : false;
        $this->is_public = $is_public;
//        $this->is_private = $is_private ? true : false;

        if ($this->id == 0) $errors[] = "Diagram id $id is not a number";
        if ($this->num_versions == 0) $errors[] = "Diagram num-versions $num_versions is not a number";

        $this->name = $name;

        if (count($errors) > 0) throw new Exception(implode(",",$errors));
    }



}
?>
