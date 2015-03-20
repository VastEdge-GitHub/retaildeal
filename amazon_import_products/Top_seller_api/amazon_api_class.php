<?php
    
    require_once 'aws_signed_request.php';

    class AmazonProductAPI
    {
        /**
         * Your Amazon Access Key Id
         * @access private
         * @var string
         */
        private $public_key     = "AKIAJI5XSRBH6TEPCM2A";
        
        /**
         * Your Amazon Secret Access Key
         * @access private
         * @var string
         */
        private $private_key    = "62ivalcpr1CSa3+zBIbYepSV7uuDun0cNnrp8wIS";
        
        /**
         * Your Amazon Associate Tag
         * Now required, effective from 25th Oct. 2011
         * @access private
         * @var string
         */
        private $associate_tag  = "your_own_tag";
        
        /**
         * Check if the xml received from Amazon is valid
         * 
         * @param mixed $response xml response to check
         * @return bool false if the xml is invalid
         * @return mixed the xml response if it is valid
         * @return exception if we could not connect to Amazon
         */
        private function verifyXmlResponse1($response)
        {
            if ($response === False)
            {
                throw new Exception("Could not connect to Amazon");
            }
            else
            {
                if (isset($response->Items->Item->ItemAttributes->Title))
                {
                    return ($response);
                }
                else
                {
                    return ($response);
					//throw new Exception("Invalid xml response.");
                }
            }
        }
		private function verifyXmlResponse($response)
        {
            if ($response === False)
            {
                throw new Exception("Could not connect to Amazon");
            }
            else
            {
                if (isset($response->BrowseNodes->BrowseNode->TopSellers->TopSeller->ASIN))
                {
                    return ($response);
                }
                else
                {
                    throw new Exception("Invalid xml response.");
                }
            }
        }
        
        
        /**
         * Query Amazon with the issued parameters
         * 
         * @param array $parameters parameters to query around
         * @return simpleXmlObject xml query response
         */
        private function queryAmazon($parameters)
        {
            return aws_signed_request("com", $parameters, $this->public_key, $this->private_key, $this->associate_tag);
        }
        
        
        /**
         * Return details of products searched by various types
         * 
         * @param string $search search term
         * @param string $category search category         
         * @param string $searchType type of search
         * @return mixed simpleXML object
         */
      		public function searchProducts($sku)
           {            
				$parameters = array(
						  'Operation' => 'ItemLookup',
						  'ItemId' =>    $sku,
						  'ResponseGroup' => 'Large'
						  );
				 $xml_response = $this->queryAmazon($parameters);
				return $xml_response;
				return $this->verifyXmlResponse1($xml_response);
        	}
		 public function topProducts($BrowseNodeID)
        {
            $parameters = array("Operation"	=> "BrowseNodeLookup", 
								"BrowseNodeId"   => $BrowseNodeID,
								"ResponseGroup" => "TopSellers");
            $xml_response = $this->queryAmazon($parameters);
            return $xml_response;
            //return $this->verifyXmlResponse($xml_response);
        }
    }
?>
