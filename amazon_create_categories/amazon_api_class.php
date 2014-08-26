<?php

require_once 'aws_signed_request.php';

class AmazonProductAPI
{
    /**
     * Your Amazon Access Key Id
     * @access private
     * @var string
     */
    private $public_key     = "";
    
    /**
     * Your Amazon Secret Access Key
     * @access private
     * @var string
     */
    private $private_key    = "";
    
    private $media_type     = "";
    
    private $region         = "";
    
    private $out_file_fp    = "";
    
    
    public function __construct($public, $private, $region) {
        $this->public_key   = $public;
        $this->private_key  = $private;
        $this->region       = $region;
    }        
    
    public function getNode($node)
    {
        $parameters = array("Operation"  => "BrowseNodeLookup",
                                            "BrowseNodeId"   => $node,
                                            "ResponseGroup" => "BrowseNodeInfo");
                            
        $xml_response = aws_signed_request($parameters,
                                           $this->public_key,
                                           $this->private_key,
                                           $this->region);
        
        return ($xml_response);
    }
    
    
    public function setMedia($media, $file = "") {
        $media_type = array("display", "csv");
        
        if(!in_array($media,$media_type)) {
            throw new Exception("Invalid Media Type");
            exit();
        }
        
        $this->media_type = $media;
        
        if($media == "csv") {
            $this->out_file_fp = fopen($file,'a+');
        }
    }
    
    
    private function writeOut($level, $name, $id, $parent) {
        if($this->media_type == "display") {
            $spaces = str_repeat( ' ', ( $level * 6 ) );
            echo $spaces . $parent . ' : ' . $name . ' : ' . $id . "\n";   
        } elseif ($this->media_type == "csv") {
            $csv_line = '"' . $parent . '","' . $name . '","' . $id . '"' . "\n";
            fputs($this->out_file_fp, $csv_line);
        } else {
            throw new Exception("Invalid Media Type");
            exit();
        }
    }
    
    
    public function getBrowseNodes($nodeValue, $level = 0)
    {
		global $subcat_array;
        try{$result = $this->getNode($nodeValue);}
        catch(Exception $e){echo $e->getMessage();}

        if(!isset($result->BrowseNodes->BrowseNode->Children->BrowseNode)){return;}
        
        if(count($result->BrowseNodes->BrowseNode->Children->BrowseNode) > 0)
		{
            foreach($result->BrowseNodes->BrowseNode->Children->BrowseNode as $node)
			{
				if(isset($node->IsCategoryRoot))
				{
					$this->getBrowseNodes($node->BrowseNodeId, $level+1);
					break;
				}
				else
				{
					$cat_name = $node->Name;
					$cat_id = $node->BrowseNodeId;
					$data = $cat_name."||".$cat_id;
					//echo "++".$data."++<br />";
					array_push($subcat_array,$data);
				}
				
                //$this->writeOut($level, $node->Name,$node->BrowseNodeId,$result->BrowseNodes->BrowseNode->Name);
                //$this->getBrowseNodes($node->BrowseNodeId, $level+1);
            }
        }
		else{return;}
		return $subcat_array;
		//print_r($array);
    }
    
    
    public function getNodeName($nodeValue)
    {
        try {
            $result = $this->getNode($nodeValue);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        
        if(!isset($result->BrowseNodes->BrowseNode->Name)) return;
        
        return (string)$result->BrowseNodes->BrowseNode->Name;
    }
    
    
    public function getParentNode($nodeValue)
    {
        try {
            $result = $this->getNode($nodeValue);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        
        if(!isset($result->BrowseNodes->BrowseNode->Ancestors->BrowseNode->BrowseNodeId)) return;
        
        $parent_node = array("id" => (string)$result->BrowseNodes->BrowseNode->Ancestors->BrowseNode->BrowseNodeId,
                             "name" => (string)$result->BrowseNodes->BrowseNode->Ancestors->BrowseNode->Name);
        return $parent_node;
    }

}

?>
