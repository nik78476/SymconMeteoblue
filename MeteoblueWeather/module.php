<?
class SymconMeteoblue extends IPSModule
{
	
	public function Create()
    {
        //Never delete this line!
        parent::Create();
        
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        
        // VariableProfiles
        $this->createVariableProfileWindDirection();
        $this->createVariableProfileUVIndex();
        
        // Configuration Values
        $this->RegisterPropertyString("MBW_APIKEY", "Your API-Key");
		$this->RegisterPropertyString("MBW_LATITUDE", "47.660" );
        $this->RegisterPropertyString("MBW_LONGITUDE", "9.176");
		$this->RegisterPropertyString("MBW_ASL","402");
		$this->RegisterPropertyInteger("MBW_UPDATEINTERVALL", 3600);
		$this->RegisterPropertyString("MBW_LANGUAGE", "de");
        $this->RegisterPropertyString("MBW_DATE_FORMAT", "d.m.Y");
        $this->RegisterPropertyString("MBW_TEMPERATURE", "C");
        $this->RegisterPropertyInteger("MBW_FORECASTDAYS", "0");
        $this->RegisterPropertyBoolean("MBW_DEBUG", false);
        $this->RegisterPropertyInteger("MBW_IMAGE_HEIGHT", "80");
        $this->RegisterPropertyInteger("MBW_IMAGE_WIDTH", "100");
        $this->RegisterPropertyInteger("MBW_FORECASTPRECISION","0");
        $this->RegisterPropertyInteger("MBW_FONTSIZE","16");
        
        
        // Variables
		$this->RegisterVariableString("MBW_V_LASTUPDATE", "Last Update");
        $this->RegisterVariableString("MBW_V_PICTOCODEURL", "Wetterpictogramm","~HTMLBox",1);
        $this->RegisterVariableString("MBW_V_FORECASTHTML", "Vorhersage","~HTMLBox",1);
        
        $this->RegisterVariableInteger("MBW_V_UVINDEX", "UV Index", "MBW.UVIndex");
        $this->RegisterVariableFloat("MBW_V_TEMPERATURE_MAX", "Temp (max)", "~Temperature");
        $this->RegisterVariableFloat("MBW_V_TEMPERATURE_MIN", "Temp (min)", "~Temperature");
        $this->RegisterVariableFloat("MBW_V_FELTTEMPERATURE_MIN", "Gef. Temp (min)", "~Temperature");
        $this->RegisterVariableFloat("MBW_V_FELTTEMPERATURE_MAX", "Gef. Temp (max)", "~Temperature");
        $this->RegisterVariableInteger("MBW_V_WINDDIRECTION", "Windrichtung","MBW.WindDirection");
        
        $this->RegisterVariableInteger("MBW_V_SEALEVELPRESSUREMIN", "Luftdruck (min)");
        $this->RegisterVariableInteger("MBW_V_SEALEVELPRESSUREMAX", "Luftdruck (max)");
        
        
        $this->RegisterTimer("UpdateSymconMeteoblue", $this->ReadPropertyInteger("MBW_UPDATEINTERVALL") * 1000, 'MBW_Update($_IPS[\'TARGET\']);');
		
    }
    public function Destroy()
    {
        //Never delete this line!!
        parent::Destroy();
    }
    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        
        $this->RegisterHook("/hook/SymconMeteoblue");
		$this->SetTimerInterval("UpdateSymconMeteoblue", $this->ReadPropertyInteger("MBW_UPDATEINTERVALL") * 1000);
        
        $this->Update();
		
    }
    public function Update()
    {
        $myAPIKey = $this->ReadPropertyString("MBW_APIKEY");
        $loggingActive = $this->ReadPropertyBoolean("MBW_DEBUG");
        $forecastPrecision = $this->ReadPropertyInteger("MBW_FORECASTPRECISION");
        $forecastFontSize = $this->ReadPropertyInteger("MBW_FONTSIZE");
        
        if ($loggingActive){
            IPS_LogMessage("SymconMeteoblue", "--------------------------------------");
		}
        
        if($myAPIKey == NULL || $myAPIKey == "Your API-Key"){
            IPS_LogMessage("SymconMeteoblue", "Set your API Key first to aquire data.");
            return;
        }
        
        if ($loggingActive){
            IPS_LogMessage("SymconMeteoblue", "API-Key: " .$myAPIKey);
		}

        $url  = "http://my.meteoblue.com/packages/basic-day?";
        $url .= "apikey=" .$this->ReadPropertyString("MBW_APIKEY");
        $url .= "&lat=" .$this->ReadPropertyString("MBW_LATITUDE");
        $url .= "&lon=" .$this->ReadPropertyString("MBW_LONGITUDE");
        $url .= "&asl=" .$this->ReadPropertyString("MBW_ASL");
        $url .= "&lang=" .$this->ReadPropertyString("MBW_LANGUAGE");
        $url .= "&temperature=" .$this->ReadPropertyString("MBW_TEMPERATURE");
  
        $rawWeatherData = file_get_contents($url);
        $weatherDataJSON = json_decode($rawWeatherData);
		if ($weatherDataJSON == NULL)
		{
			$this->SetStatus(104);
            IPS_LogMessage("SymconMeteoblue", "Error reading external data");
			return;
		}

        $ARRAY_DATA_DAY_TIME = $weatherDataJSON->{'data_day'}->{'time'};
        $ARRAY_DATA_DAY_PICTOCODE = $weatherDataJSON->{'data_day'}->{'pictocode'};
        $ARRAY_DATA_DAY_UVINDEX = $weatherDataJSON->{'data_day'}->{'uvindex'};
        $ARRAY_DATA_DAY_TEMPMAX = $weatherDataJSON->{'data_day'}->{'temperature_max'};
        $ARRAY_DATA_DAY_TEMPMIN = $weatherDataJSON->{'data_day'}->{'temperature_min'};
        $ARRAY_DATA_DAY_TEMPFELTMAX = $weatherDataJSON->{'data_day'}->{'felttemperature_max'};
        $ARRAY_DATA_DAY_TEMPFELTMIN = $weatherDataJSON->{'data_day'}->{'felttemperature_min'};
        $ARRAY_DATA_DAY_WINDDIRECTION = $weatherDataJSON->{'data_day'}->{'winddirection'};
        $ARRAY_DATA_DAY_SEALEVELPRESSUREMIN = $weatherDataJSON->{'data_day'}->{'sealevelpressure_min'};
        $ARRAY_DATA_DAY_SEALEVELPRESSUREMAX = $weatherDataJSON->{'data_day'}->{'sealevelpressure_max'};
        
        
        if ($loggingActive){
            IPS_LogMessage("SymconMeteoblue", "Forecast days: " .$this->ReadPropertyInteger("MBW_FORECASTDAYS"));
		}
        
        // actual weather data (today)
        $this->SetValueInt("MBW_V_UVINDEX", $ARRAY_DATA_DAY_UVINDEX[0]);
        $this->SetValueFloat("MBW_V_TEMPERATURE_MAX", $ARRAY_DATA_DAY_TEMPMAX[0]);
        $this->SetValueFloat("MBW_V_TEMPERATURE_MIN", $ARRAY_DATA_DAY_TEMPMIN[0]);
        $this->SetValueFloat("MBW_V_FELTTEMPERATURE_MAX", $ARRAY_DATA_DAY_TEMPFELTMAX[0]);
        $this->SetValueFloat("MBW_V_FELTTEMPERATURE_MIN", $ARRAY_DATA_DAY_TEMPFELTMIN[0]);
        $pictoCode = str_pad($ARRAY_DATA_DAY_PICTOCODE[0], 2 ,'0', STR_PAD_LEFT);
        $this->SetValueString("MBW_V_PICTOCODEURL","<img src='/hook/SymconMeteoblue/" .$pictoCode ."_iday_monochrome_hollow.svg' width='" .$this->ReadPropertyInteger("MBW_IMAGE_WIDTH") ."' height='" .$this->ReadPropertyInteger("MBW_IMAGE_HEIGHT") ."'>");
        $this->SetValueInt("MBW_V_WINDDIRECTION", $ARRAY_DATA_DAY_WINDDIRECTION[0]);

        $this->SetValueInt("MBW_V_SEALEVELPRESSUREMAX", $ARRAY_DATA_DAY_SEALEVELPRESSUREMAX[0]);
        $this->SetValueInt("MBW_V_SEALEVELPRESSUREMIN", $ARRAY_DATA_DAY_SEALEVELPRESSUREMIN[0]);
        
        
        // forecast weather data
        $forecastdata = "";
        if($this->ReadPropertyInteger("MBW_FORECASTDAYS") > 0){
            
            $forecastdata .= "<table border='0'>";
            
            // day
            $forecastdata .= "<tr>";
            for($i=0; $i <= $this->ReadPropertyInteger("MBW_FORECASTDAYS"); $i++){
                $forecastdata .= "<td align='center'>";
                if( $i <= 2){
                    $forecastdata .= "<font style='font-size: " .$forecastFontSize ."px;'>";
                    $forecastdata .= "<br>" .$this->getDayAsString( $i ) ."<br>";
                    $forecastdata .= "<font/>";
                } 
                //else $forecastdata .= date($this->ReadPropertyString("MBW_DATE_FORMAT"), strtotime($ARRAY_DATA_DAY_TIME[$i]);
                $forecastdata .= "</td>";
            }
            $forecastdata .= "</tr>";
                        
            // pictogram
            $forecastdata .= "<tr>";
            for($i=0; $i <= $this->ReadPropertyInteger("MBW_FORECASTDAYS"); $i++){
                $forecastdata .= "<td>";
                $pictoCode = str_pad($ARRAY_DATA_DAY_PICTOCODE[$i], 2 ,'0', STR_PAD_LEFT);
                
                $forecastdata .= "<img src='/hook/SymconMeteoblue/" .$pictoCode ."_iday_monochrome_hollow.svg' width='" .$this->ReadPropertyInteger("MBW_IMAGE_WIDTH") ."' height='" .$this->ReadPropertyInteger("MBW_IMAGE_HEIGHT") ."'>";
                $forecastdata .= "</td>";
            }
            $forecastdata .= "</tr>";
                
            // temperature min
            $forecastdata .= "<tr>";
            for($i=0; $i <= $this->ReadPropertyInteger("MBW_FORECASTDAYS"); $i++){
                $forecastdata .= "<td align='center'>";
                $forecastdata .= "<font style='font-size: " .$forecastFontSize ."px;'>";
                $forecastdata .= "min. ";
                $forecastdata .= round( $ARRAY_DATA_DAY_TEMPMIN[$i], $forecastPrecision, PHP_ROUND_HALF_DOWN) ."°" .$weatherDataJSON->{'units'}->{'temperature'};
                $forecastdata .= "</font>";
                $forecastdata .= "</td>";
            }
            $forecastdata .= "</tr>";
            
            // temperature max
            $forecastdata .= "<tr>";
            for($i=0; $i <= $this->ReadPropertyInteger("MBW_FORECASTDAYS"); $i++){
                $forecastdata .= "<td align='center'>";
                $forecastdata .= "<font style='font-size: " .$forecastFontSize ."px;'>";
                $forecastdata .= "max. ";
                $forecastdata .= round( $ARRAY_DATA_DAY_TEMPMAX[$i], $forecastPrecision, PHP_ROUND_HALF_DOWN) ."°" .$weatherDataJSON->{'units'}->{'temperature'};
                $forecastdata .= "</font>";
                $forecastdata .= "</td>";
            }
            $forecastdata .= "</tr>";
            
            // forecast text
            $forecastdata .= "<tr>";
            for($i=0; $i <= $this->ReadPropertyInteger("MBW_FORECASTDAYS"); $i++){
                $pictoCode = str_pad($ARRAY_DATA_DAY_PICTOCODE[$i], 2 ,'0', STR_PAD_LEFT);
                $forecastdata .= "<td align='center'>";
                $forecastdata .= "<font style='font-size: " .$forecastFontSize ."px;'>";
                $forecastdata .= $this->getWeatherCondition($pictoCode);
                $forecastdata .= "<font/>";
                $forecastdata .= "</td>";
            }
            $forecastdata .= "</tr>";
            
            $forecastdata .= "</table>";
            if ($loggingActive){
                IPS_LogMessage("SymconMeteoblue", "forecastdata: " .$forecastdata);
            }
        }
        
        $this->SetValueString("MBW_V_FORECASTHTML", $forecastdata);
        
        $date = new DateTime('now');
        $last_update = $date->format("d.m.Y H:m:s");
		$this->SetValueString("MBW_V_LASTUPDATE", $last_update, "");
        $this->SetStatus(102);
        
        if ($loggingActive){
            IPS_LogMessage("SymconMeteoblue", "Weatherdata updated: " .$last_update);
		}
    }

    private function SetValueInt($Ident, $Value){
    	$id = $this->GetIDforIdent($Ident);
    	SetValueInteger($id, $Value);
    	return true;	
  	}
	
	private function SetValueFloat($Ident, $Value){
    	$id = $this->GetIDforIdent($Ident);
    	SetValueFloat($id, $Value);
    	return true;
  	}
   
    private function SetValueString($Ident, $Value){
    	$id = $this->GetIDforIdent($Ident);
    	SetValueString($id, $Value);
    	return true;
  	}
	
	private function RegisterHook($WebHook) {
		// Inspired from module SymconTest/HookServe
		$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}");
		if(sizeof($ids) > 0) {
			$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
			$found = false;
			foreach($hooks as $index => $hook) {
				if($hook['Hook'] == $WebHook) {
					if($hook['TargetID'] == $this->InstanceID)
						return;
					$hooks[$index]['TargetID'] = $this->InstanceID;
					$found = true;
				}
			}
			if(!$found) {
				$hooks[] = Array("Hook" => $WebHook, "TargetID" => $this->InstanceID);
			}
			IPS_SetProperty($ids[0], "Hooks", json_encode($hooks));
			IPS_ApplyChanges($ids[0]);
		}
	}
	
	protected function ProcessHookData() {
			// Inspired from module SymconTest/HookServe
			
			$root = realpath(__DIR__ . "/Images");
			//append index.html
			if(substr($_SERVER['REQUEST_URI'], -1) == "/") {
				$_SERVER['REQUEST_URI'] .= "index.html";
			}
						
			//reduce any relative paths. this also checks for file existance
			$path = realpath($root . "/" . substr($_SERVER['REQUEST_URI'], strlen("/hook/SymconMeteoblue/")));
			//IPS_LogMessage("WebHook path: ", $path);
			if($path === false) {
				http_response_code(404);
				die("File not found!");
			}

			
			if(substr($path, 0, strlen($root)) != $root) {
				http_response_code(403);
				die("Security issue. Cannot leave root folder!");
			}
			header("Content-Type: ".$this->GetMimeType(pathinfo($path, PATHINFO_EXTENSION)));
			readfile($path);
    }
		
    private function GetMimeType($extension) {
			// Inspired from module SymconTest/HookServe
			$lines = file(IPS_GetKernelDirEx()."mime.types");
			foreach($lines as $line) {
				$type = explode("\t", $line, 2);
				if(sizeof($type) == 2) {
					$types = explode(" ", trim($type[1]));
					foreach($types as $ext) {
						if($ext == $extension) {
							return $type[0];
						}
					}
				}
			}
			return "text/plain";
    }
		
    private function getWeatherCondition( $condition ){
			
			$weathercondition = array (
				"00" => "undef.",
                "01" => "Sonnig",
                "02" => "Sonnig, Schauer",
                "03" => "Teilw. bewölkt",
                "04" => "Bedeckt",
                "05" => "Nebel",
                "06" => "Bedeckt, Regen",
                "07" => "Unbeständig, Regen",
                "08" => "Regen, Sturm mögl.",
                "09" => "Bedeckt, Schnee",
                "10" => "Unbest., Schnee",
                "11" => "Bewölkt, Schnee, Regen",
                "12" => "Bedeckt, leichter Regen",
                "13" => "Bedeckt, leichter Schnee",
                "14" => "Teilw. bewölkt m. Regen",
                "15" => "Teilw. bewölkt m. Schnee",
                "16" => "Teilw. bewölkt m. leichtem Regen",
                "17" => "Teilw. bewölkt m. leichtem Schnee"
				);
			return $weathercondition[$condition];
		}
    
    private function getDayAsString( $daycount ){
			
			$days = array (
				"0" => "Heute",
				"1" => "Morgen", 
				"2" => "Übermorgen"
            );
			return $days[$daycount];
		}
    
    private function createVariableProfileWindDirection(){
        $profile = IPS_GetVariableProfile("MBW.WindDirection");
        if ($profile == false){
            IPS_CreateVariableProfile("MBW.WindDirection", 1);
            IPS_SetVariableProfileText("MBW.WindDirection", "", "");
            IPS_SetVariableProfileValues("MBW.WindDirection", 0, 360, 30);
            IPS_SetVariableProfileDigits("MBW.WindDirection", 0);
            IPS_SetVariableProfileIcon("MBW.WindDirection", "WindDirection");
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 0, "N", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 45, "NO", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 90, "O", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 135, "SO", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 180, "S", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 225, "SW", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 270, "W", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 315, "NW", "", -1);
            IPS_SetVariableProfileAssociation("MBW.WindDirection", 360, "n", "", -1);
            
        }
    }
        
    private function createVariableProfileUVIndex(){
        $profile = IPS_GetVariableProfile("MBW.UVIndex");
        if ($profile == false){
            IPS_CreateVariableProfile("MBW.UVIndex", 1);
            IPS_SetVariableProfileText("MBW.UVIndex", "", "");
            IPS_SetVariableProfileValues("MBW.UVIndex", 0, 12, 0);
            IPS_SetVariableProfileDigits("MBW.UVIndex", 0);
            IPS_SetVariableProfileIcon("MBW.UVIndex", "Sun");
            IPS_SetVariableProfileAssociation("MBW.UVIndex", 0, "%.1f", "", -1);
            IPS_SetVariableProfileAssociation("MBW.UVIndex", 3, "%.1f", "", 16314432);
            IPS_SetVariableProfileAssociation("MBW.UVIndex", 6, "%.1f", "", 16283680);
            IPS_SetVariableProfileAssociation("MBW.UVIndex", 8, "%.1f", "", 14155808);
            IPS_SetVariableProfileAssociation("MBW.UVIndex", 11, "%.1f", "", 11010176);
            
        }
    }
}
?>