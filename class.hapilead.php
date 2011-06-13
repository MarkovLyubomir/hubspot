<?php
    
class HAPILeads
{
    private $HAPIKey;
    private $HubSpotObject;
    private $RequestType;
    private $RequestGetURL;

    private $RequestGUID;
    private $RequestData;
    private $RequestResponse = array();
    
    private $PutGuid;
    private $PutURL;
    private $PutJSON;
    private $PutResponse = array();
    
    private $PostResponse = array();
    private $PostURL;

    //Search parameters
    private $RequestSearch;
    private $RequestSort;
    private $RequestDir;
    private $RequestMax;
    private $RequestOffset;
    private $RequestStartTime;
    private $RequestStopTime;
    private $RequestTimePivot;
    private $RequestExcludeConversionEvents;
    private $RequestOptout;
    private $RequestEligibleForEmail;
    private $RequestBounced;
    private $RequestNotImported;
    
    private $RecordNavMax;
    private $RecordNavOffset;
    private $RecordNavRecords = array();
    private $RecordNavRecordCount;
    private $RecordNavCurrentRecord;
    private $RecordNavOutput = array();
    
    
    private $APIVersion = 'v1';
    private $APIBaseURL = 'https://api.hubapi.com';

     /**
     * Creates new instance of HAPILeads
     *
     * 
     */
    function __construct()
    {
        $this->HubSpotObject = 'leads';
    }
    
    function __destruct()
    {
        return "Destroyed";
    }
    
    /**
     * Sets HAPI Key
     *
     * @param HAPIKey: Set to the HAPI Key issues for the HubSpot portal being accessed
     * 
     */ 
    public function setHAPIKey($HAPIKey)
    {
        $this->HAPIKey = $HAPIKey;
    }

    /**
     * Sets Request Type
     *
     * @param RequestType: Set to 'list' to return a list of objects, leave null if retrieving an object by GUID
     * 
     */ 
    public function setRequestType($RequestType)
    {
        $this->RequestType = $RequestType;
    }

    /**
     * Sets Request GUID
     *
     * @param RequestGUID: Set to the GUID of the object being requested (not used for list requests)
     * 
     */ 
    public function setRequestGUID($RequestGUID)
    {
        $this->RequestGUID = $RequestGUID;
    }
    
     /**
     * Sets Request Search Filter
     *
     * @param RequestSearch: An optional string value that is used to search several basic lead fields: first name, last name, email address, and company name. A more advanced search is coming in the future. The default value for this parameter is an empty string, meaning return all leads.
     * 
     */
    public function setRequestSearch($RequestSearch)
    {
        $this->RequestSearch = $RequestSearch;
    }
    
    /**
     * Sets Request Sort Attribute
     *
     * @param RequestSort: An optional string value that specifies the lead attribute on which you wish to sort.
     * 
     */ 
    public function setRequestSort($RequestSort)
    {
        $this->RequestSort = $RequestSort;
    }
    
    /**
     * Sets Request Dir Attribute
     *
     * @param RequestDir: An optional string value that specifies the sort direction of the returned list of leads. Acceptable values are 'asc' for ascending sort, and 'desc' for descending sort.
     * 
     */ 
    public function setRequestDir($RequestDir)
    {
        $this->RequestDir = $RequestDir;
    }
    
    /**
     * Sets Request Max Attribute
     *
     * @param RequestMax: An optional integer, the maximum number of leads to return. The default number returned is 10. The maximum is 100.
     * 
     */ 
    public function setRequestMax($RequestMax)
    {
        $this->RequestMax = $RequestMax;
    }
    
    /**
     * Sets Request Offset Attribute
     *
     * @param RequestOffset: An optional integer offset into the list of leads matching the search request. Use in combination with max for pagination. The default value for this parameter is zero.
     * 
     */ 
    public function setRequestOffset($RequestOffset)
    {
        $this->RequestOffset = $RequestOffset;
    }
    
    /**
     * Sets Request StartTime Attribute
     *
     * @param RequestStartTime: An optional time (in number of milliseconds since the epoch.) The returned list will have only leads where the value of TimePivot is after this time. The default value for this parameter is the zero, i.e. the epoch, to include all leads.
     * 
     */ 
    public function setRequestStartTime($RequestStartTime)
    {
        $this->RequestStartTime = $RequestStartTime;
    }
    
    /**
     * Sets Request StopTime Attribute
     *
     * @param RequestStopTime: An optional time (in number of milliseconds since the epoch.) The returned list will have only leads where the value of TimePivot is before this time. The default value for this parameter is now to include all leads.
     * 
     */ 
    public function setRequestStopTime($RequestStopTime)
    {
        $this->RequestStopTime = $RequestStopTime;
    }
    
    /**
     * Sets Request TimePivot Attribute
     *
     * @param RequestTimePivot: The field on which the startTime and stopTime paramaters should be applied (default is lastModifiedAt), must be one of the following fields: insertedAt, firstConvertedAt, lastConvertedAt, lastModifiedAt, closedAt
     * 
     */ 
    public function setRequestTimePivot($RequestTimePivot)
    {
        $this->RequestTimePivot = $RequestTimePivot;
    }
    
    /**
     * Sets Request ExcludeConversionEvents Attribute
     *
     * @param RequestExcludeConversionEvents: An optional boolean. Set to true to exclude all items in the leadConversionEvents collection from the API results. This will increase performance for larger lists, with the tradeoff of reduced information being about your leads
     * 
     */ 
    public function setRequestExcludeConversionEvents($RequestExcludeConversionEvents)
    {
        $this->RequestExcludeConversionEvents = $RequestExcludeConversionEvents;
    }
    
    /**
     * Sets Request Optout Attribute
     *
     * @param RequestOptout: An optional boolean. Set to true to include only leads that have unsubscribed from lead nurturing. The default value, false, includes all leads.
     * 
     */ 
    public function setRequestOptout($RequestOptout)
    {
        $this->RequestOptout = $RequestOptout;
    }
    
     /**
     * Sets Request EligibleForEmail Attribute
     * 
     *
     * @param RequestEligibleForEmail: An optional boolean. Set to true to include only leads eligible for email marketing. The default value, false, includes all leads.
     * 
     */ 
    public function setRequestEligibleForEmail($RequestEligibleForEmail)
    {
        $this->RequestEligibleForEmail = $RequestEligibleForEmail;
    }
    
    /**
     * Sets Request Bounced Attribute
     *
     * @param RequestBounced: An optional boolean. Set to true to include only leads that HubSpot has tried to email, and the email bounced. The default value, false, includes all leads.
     * 
     */ 
    public function setRequestBounced($RequestBounced)
    {
        $this->RequestBounced = $RequestBounced;
    }
    
    /**
     * Sets Request NotImported Attribute
     *
     * @param RequestNotImported:  An optional boolean. Set to true to include only web leads. The default value, false, includes both imported and web leads.
     * 
     */ 
    public function setRequestNotImported($RequestNotImported)
    {
        $this->RequestNotImported = $RequestNotImported;
    }   
    
    /**
     * Creates the HubSpot API request URL based on instance properties
     *
     * @return RequestURL (or 0 if URL cannot be created)
     */ 
    public function createGetRequestURL()
    {
        $this->RequestGetURL = $this->APIBaseURL . '/';
        $this->RequestGetURL .= $this->HubSpotObject . '/';
        $this->RequestGetURL .= $this->APIVersion . '/';
        
        if ($this->RequestType == 'list')
        {
            $this->RequestGetURL .= $this->RequestType . '/';
        }
        else if ($this->RequestType =='lead' )
        {
            $this->RequestGetURL .= 'lead/' . $this->RequestGUID;
        }
        else
        {
            return 0;
        }
        
        $this->RequestGetURL .= '?hapikey=' . $this->HAPIKey;
        
        if ($this->RequestSearch <> ''){ $this->RequestGetURL .= '&search=' . $this->RequestSearch; }
        if ($this->RequestSort <> ''){ $this->RequestGetURL .= '&sort=' . $this->RequestSort; }
        if ($this->RequestDir <> ''){ $this->RequestGetURL .= '&dir=' . $this->RequestDir; }
        if ($this->RequestMax <> ''){ $this->RequestGetURL .= '&max=' . $this->RequestMax; }
        if ($this->RequestOffset <> ''){ $this->RequestGetURL .= '&offset=' . $this->RequestOffset; }
        if ($this->RequestStartTime <> ''){ $this->RequestGetURL .= '&startTime=' . $this->RequestStartTime; }
        if ($this->RequestStopTime <> ''){ $this->RequestGetURL .= '&stopTime=' . $this->RequestStopTime; }
        if ($this->RequestTimePivot <> ''){ $this->RequestURLGet .= '&timePivot=' . $this->RequestTimePivot; }
        if ($this->RequestExcludeConversionEvents <> ''){ $this->RequestGetURL .= '&excludeConversionEvents=' . $this->RequestExcludeConversionEvents; }
        if ($this->RequestOptout <> ''){ $this->RequestGetURL .= '&optout=' . $this->RequestOptout; }
        if ($this->RequestEligibleForEmail <> ''){ $this->RequestGetURL .= '&eligibleForEmail=' . $this->RequestEligibleForEmail; }
        if ($this->RequestBounced <> ''){ $this->RequestGetURL .= '&bounced=' . $this->RequestBounced; }
        if ($this->RequestNotImported <> ''){ $this->RequestGetURL .= '&notImported=' . $this->RequestNotImported; }
        
        return $this->RequestGetURL;
    }
    
    /**
     * Executes the HubSpot API request as an HTTP GET via cURL (for retrieving lead data)
     *
     * @return RequestResponse: An array containing Error (cURL Error #), ErrorMessage (cURL Error Text), RecordCount (number of records returned by HAPI) and Data (Data returned by HAPI, json decoded)
     */ 
    public function executeGetRequest()
    {
        $this->createGetRequestURL();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->RequestGetURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $this->RequestResponse['Error'] = curl_errno($ch);
        $this->RequestResponse['ErrorMessage'] = curl_error($ch);
        $output = curl_exec($ch);
        $decodedoutput = json_decode($output);
        $this->RequestResponse['RecordCount'] = count($decodedoutput);
        $this->RequestResponse['Data'] = $decodedoutput;
        curl_close($ch);
        
        return $this->RequestResponse;

    }

     /**
     * Executes the HubSpot API request and returns a single lead record (use to process each lead returned in a list individually)
     *
     * @return RecordNavOutput: An array containing Error (0 = OK, 1 = No more data) and Data (Data returned by HAPI, json decoded)
     */ 
    public function executeGetNextRecord()
    {
        if ($this->RecordNavMax == '')
        {
            $this->RecordNavMax = 20;
        }
        if ($this->RecordNavOffset == '')
        {
            $this->RecordNavOffset = 0;
        }
        if ($this->RecordNavCurrentRecord == '')
        {
            $this->RecordNavCurrentRecord = 0;
        }
        
        $this->RequestType = 'list';
        $this->RequestMax = $this->RecordNavMax;
        $this->RequestOffset = $this->RecordNavOffset;
        
        if ($this->RecordNavRecordCount == '')
        {
            $this->RecordNavRecords = $this->executeGetRequest();
            $this->RecordNavRecordCount = $this->RecordNavRecords['RecordCount'];
            $this->RecordNavOffset = $this->RecordNavOffset + $this->RecordNavRecordCount;
        }
        elseif($this->RecordNavCurrentRecord >= ($this->RecordNavRecordCount))
        {
            $this->RecordNavRecords = $this->executeGetRequest();
            $this->RecordNavCurrentRecord = 0;
            $this->RecordNavRecordCount = $this->RecordNavRecords['RecordCount'];
            $this->RecordNavOffset = $this->RecordNavOffset + $this->RecordNavRecordCount;  
        }

        if ($this->RecordNavRecordCount > 0)
        {
            $this->RecordNavOutput['Data'] = $this->RecordNavRecords['Data'][$this->RecordNavCurrentRecord];
            $this->RecordNavOutput['Error'] = 0;
            $this->RecordNavCurrentRecord++;
            return $this->RecordNavOutput;
        }
        else 
        {
            $this->RecordNavOutput['Data'] = '';
            $this->RecordNavOutput['RecordCount'] = '';
            $this->RecordNavOutput['Error'] = 1;
            return $this->RecordNavOutput;
        }
        
    }
    
     /**
     * Creates the HubSpot API PUT URL based on instance properties
     *
     * @param LeadGuid: The GUID for the lead to be updated
     *
     * @return PutURL
     */ 
    public function createPutRequestURL($LeadGuid)
    {
        $this->PutGuid = $LeadGuid;
        $this->PutURL = $this->APIBaseURL . '/';
        $this->PutURL .= $this->HubSpotObject . '/';
        $this->PutURL .= $this->APIVersion . '/';       
        $this->PutURL .= 'lead/';
        $this->PutURL .= $this->PutGuid . '/';
        $this->PutURL .= '?hapikey=' . $this->HAPIKey;
        
        return $this->PutURL;
    }

     /**
     * Executes the HubSpot API PUT to update lead
     *
     * @param LeadGuid: The GUID for the lead to be updated
     * @param UpdateData: Array of fields to be updated ('fieldName','"value"') where value must be surrounded by " " if required!
     *
     * @return PutResponse: An array containing Error (cURL Error #), ErrorMessage (cURL Error Text), RecordCount (number of records returned by HAPI) and Data (Whatever HubSpot Returns)
     */ 
    public function executeUpdateLead($LeadGuid, $UpdateFields)
    {
        $this->createPutRequestURL($LeadGuid);
        
        $this->PutJSON = '{"id":"' . $this->PutGuid . '"';
        
        foreach ($UpdateFields as $updatename => $updatevalue)
        {
            $this->PutJSON .= ',"' . $updatename . '":' . $updatevalue;
        }
        
        $this->PutJSON .= '}';
              
        $fh = tmpfile();
        fwrite($fh, $this->PutJSON);
        fseek($fh, 0);
        
        $chput = curl_init();
        curl_setopt($chput, CURLOPT_URL, $this->PutURL);
        curl_setopt($chput, CURLOPT_VERBOSE, 1);
        curl_setopt($chput, CURLOPT_PUT, true);
        curl_setopt($chput, CURLOPT_INFILE, $fh);
        curl_setopt($chput, CURLOPT_INFILESIZE, strlen($this->PutJSON)); 
        curl_setopt($chput, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chput, CURLOPT_SSL_VERIFYPEER, 0);
        $this->PutResponse['Data'] = curl_exec($chput);
        $this->PutResponse['Error'] = curl_errno($chput); 
        $this->PutResponse['ErrorMessage'] = curl_error($chput); 
        curl_close($chput);
        
        return $this->PutResponse;
    }
    
    /**
     * Executes the HubSpot API POST to insert a lead
     *
     * @param string $formName Name of lead form to insert to
     * @param string $formURL HubSpot-provided POST URL to submit to
     * @param array $fields Form fields, such as name and contact info
     *
     * @return GetResponse array stuff
     */
    public function executeInsertLead($formName, $formURL, $fields)
    {
        $strPost = "";
        
        // These fields must be submitted with each request
        $fields['UserToken'] = $_COOKIE['hubspotutk'];
        $fields['IPAddress'] = $_SERVER['REMOTE_ADDR'];
        $fields['hapikey'] = $this->HAPIKey;
        
        // Turn $fields into POST-compatible list of parameters
        foreach ($fields as $fieldName => $fieldValue)
        {
            $strPost .= urlencode($fieldName) . '=';
            $strPost .= urlencode($fieldValue);
            $strPost .= '&';
        }
        
        $strPost = rtrim($strPost, '&'); // nuke the final ampersand
        
        // intialize cURL and send POST data
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strPost);
        curl_setopt($ch, CURLOPT_URL, $formURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->PostResponse['Data'] = curl_exec($ch);
        $this->PostResponse['Error'] = curl_errno($ch);
        $this->PostResponse['ErrorMessage'] = curl_error($ch);
        
        curl_close($ch);
        
        return $this->PostResponse;
    }
}


?>
