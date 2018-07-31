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
        $this->RegisterPropertyString("MBW_APIKEY", "");
		$this->RegisterPropertyString("MBW_LATITUDE", "47.660" );
        $this->RegisterPropertyString("MBW_LONGITUDE", "9.176");
		$this->RegisterPropertyString("MBW_ASL","402");
		$this->RegisterPropertyInteger("MBW_UPDATEINTERVALL", 100);
		$this->RegisterPropertyString("MBW_LANGUAGE", "de");
        $this->RegisterPropertyString("MBW_TEMPERATURE", "C");
        $this->RegisterPropertyInteger("MBW_FORECASTDAYS", "1");
        
        // Variables
		$this->RegisterVariableString("MBW_V_LASTUPDATE", "Last Update");
        $this->RegisterVariableString("MBW_V_PICTOCODEURL", "Wetterpictogramm", "~HMTLBox");
        $this->RegisterVariableInteger("MBW_V_UVINDEX", "UV Index", "MBW.UVIndex");
        $this->RegisterVariableFloat("MBW_V_TEMPERATURE_MAX", "Temp (max)", "~Temperature");
        $this->RegisterVariableFloat("MBW_V_TEMPERATURE_MIN", "Temp (min)", "~Temperature");
        $this->RegisterVariableFloat("MBW_V_FELTTEMPERATURE_MIN", "Gef. Temp (min)", "~Temperature");
        $this->RegisterVariableFloat("MBW_V_FELTTEMPERATURE_MAX", "Gef. Temp (max)", "~Temperature");
        $this->RegisterVariableInteger("MBW_V_WINDDIRECTION", "Windrichtung","MBW.WindDirection");
        
        
        /*
		$this->RegisterVariableString("Wetter", "Wetter","~HTMLBox",1);
		$this->RegisterVariableString("YWH_IPS_Wetter", "Wetterdarstellung IPSView","~HTMLBox",1);
		
		// Vorhersage für heute als Variablen
		$this->RegisterVariableString("YWH_Wetter_heute", "Wettervorhersage (heute)");
		$this->RegisterVariableFloat("YWH_Heute_temp_min", "Temp (min)","~Temperature");
		$this->RegisterVariableFloat("YWH_Heute_temp_max", "Temp (max)","~Temperature");
		
		$this->RegisterVariableString("YWH_Sonnenaufgang", "Sonnenaufgang (heute)");
		$this->RegisterVariableString("YWH_Sonnenuntergang", "Sonnenuntergang (heute)");
		
		$this->RegisterVariableString("YWH_Luftfeuchtigkeit", "Luftfeuchtigkeit (heute)");
		$this->RegisterVariableString("YWH_Luftdruck", "Luftdruck (heute)");
		$this->RegisterVariableString("YWH_Sichtweite", "Sichtweite (heute)");
		$this->RegisterVariableString("YWH_WindGeschwindigkeit", "Windgeschwindigkeit (heute)");
		
		$this->RegisterVariableString("YWH_WetterImage", "WetterImage (heute)");
        
        
        $weatherstring .= '<img src="/hook/SymconMeteoblue/' .$forecast[$i]->code .'.png" style="height:' .$this->ReadPropertyInteger("YWHImageZoom") .'%;width:auto;">';
        
        <img src="/hook/SymconMeteoblue/wi-cloud-down.svg" width="200" height="160">
        
        */
        
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
        
        
        //http://my.meteoblue.com/packages/basic-day?//apikey=936c81471c23&lat=47.7154&lon=9.0715&asl=403&tz=Europe%2FBerlin&city=Allensbach
        
        $url  = "http://my.meteoblue.com/packages/basic-day?";
        $url .= "apikey=" .$this->ReadPropertyString("MBW_APIKEY");
        $url .= "&lat=" .$this->ReadPropertyString("MBW_LATITUDE");
        $url .= "&lon=" .$this->ReadPropertyString("MBW_LONGITUDE");
        $url .= "&asl=" .$this->ReadPropertyString("MBW_ASL");
        $url .= "&lang=" .$this->ReadPropertyString("MBW_LANGUAGE");
        $url .= "&temperature=" .$this->ReadPropertyString("MBW_TEMPERATURE");
        
        //$url = "http://ip-symcon.familie-froehlich.org/data.json";
  
        $rawWeatherData = file_get_contents($url);
        $weatherDataJSON = json_decode($rawWeatherData);
		if ($weatherDataJSON == NULL)
		{
			$this->SetStatus(104);
            IPS_LogMessage($_IPS['SELF'], "Error reading external data");
			return;
		}
        
        /*
        IPS_LogMessage($_IPS['SELF'], "URL-DATA: " .$url);
        
        time": ["2018-07-31", "2018-08-01", "2018-08-02", "2018-08-03", "2018-08-04", "2018-08-05", "2018-08-06"], 
		"pictocode": [2, 8, 2, 1, 1, 8, 8], 
		"uvindex": [4, 8, 8, 8, 8, 8, null], 
		"temperature_max": [34.05, 33.27, 32.01, 33.28, 33.77, 31.81, 31.25], 
		"temperature_min": [18.43, 19.98, 19.38, 19.21, 19.41, 21.02, 18.14], 
		"temperature_mean": [27.05, 26.30, 25.64, 26.50, 26.84, 26.06, 24.76], 
		"felttemperature_max": [38.83, 36.80, 34.78, 37.08, 37.46, 35.64, 32.91], 
		"felttemperature_min": [18.53, 20.36, 21.63, 21.23, 21.46, 24.16, 19.78], 
		"winddirection": [0, 45, 45, 45, 45, 135, 270], 
		"precipitation_probability": [11, 38, 29, 14, 15, 41, 30], 
		"rainspot": ["0000000000000000000000000000000000000000000000001", "1212110111122100011100001110100001110111111111111", "0000000000000000000000000000000000000100000112210", "0000000000000000000000000000000000000000000000000", "0000000000000000000000000000000000000000000000000", "1112111222210111111002122219222011101211000001100", "0001000001100000111000001000001221000002000000000"], 
		"predictability_class": [5, 3, 4, 5, 5, 3, 3], 
		"predictability": [91, 55, 68, 89, 81, 50, 48], 
		"precipitation": [0.00, 0.40, 0.00, 0.00, 0.00, 2.32, 0.40], 
		"snowfraction": [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00], 
		"sealevelpressure_max": [1018, 1020, 1023, 1023, 1021, 1021, 1019], 
		"sealevelpressure_min": [1016, 1017, 1020, 1019, 1018, 1018, 1015], 
		"sealevelpressure_mean": [1016, 1018, 1021, 1021, 1020, 1019, 1017], 
		"windspeed_max": [1.60, 3.12, 2.62, 3.12, 2.59, 1.90, 3.35], 
		"windspeed_mean": [1.03, 1.85, 1.72, 2.04, 1.34, 0.83, 1.67], 
		"windspeed_min": [0.00, 0.57, 0.08, 1.01, 0.09, 0.00, 0.04], 
		"relativehumidity_max": [76, 75, 97, 92, 93, 88, 84], 
		"relativehumidity_min": [40, 44, 46, 50, 46, 44, 44], 
		"relativehumidity_mean": [58, 63, 70, 70, 68, 64, 65], 
		"convective_precipitation": [0.00, 0.40, 0.00, 0.00, 0.00, 2.32, 0.40], 
		"precipitation_hours": [0.00, 1.00, 0.00, 0.00, 0.00, 2.00, 1.00], 
		"humiditygreater90_hours": [0.00, 0.00, 5.00, 2.00, 1.00, 0.00, 0.00]
        */
        
        $ARRAY_DATA_DAY_TIME = $weatherDataJSON->{'data_day'}->{'time'};
        $ARRAY_DATA_DAY_PICTOCODE = $weatherDataJSON->{'data_day'}->{'pictocode'};
        $ARRAY_DATA_DAY_UVINDEX = $weatherDataJSON->{'data_day'}->{'uvindex'};
        $ARRAY_DATA_DAY_TEMPMAX = $weatherDataJSON->{'data_day'}->{'temperature_max'};
        $ARRAY_DATA_DAY_TEMPMIN = $weatherDataJSON->{'data_day'}->{'temperature_min'};
        $ARRAY_DATA_DAY_TEMPFELTMAX = $weatherDataJSON->{'data_day'}->{'felttemperature_max'};
        $ARRAY_DATA_DAY_TEMPFELTMIN = $weatherDataJSON->{'data_day'}->{'felttemperature_min'};
        $ARRAY_DATA_DAY_WINDDIRECTION = $weatherDataJSON->{'data_day'}->{'winddirection'};
        
		$this->SetValueInt("MBW_V_UVINDEX", $ARRAY_DATA_DAY_UVINDEX[0]);
        $this->SetValueFloat("MBW_V_TEMPERATURE_MAX", $ARRAY_DATA_DAY_TEMPMAX[0]);
        $this->SetValueFloat("MBW_V_TEMPERATURE_MIN", $ARRAY_DATA_DAY_TEMPMIN[0]);
        $this->SetValueFloat("MBW_V_FELTTEMPERATURE_MAX", $ARRAY_DATA_DAY_TEMPFELTMAX[0]);
        $this->SetValueFloat("MBW_V_FELTTEMPERATURE_MIN", $ARRAY_DATA_DAY_TEMPFELTMIN[0]);
        $this->SetValueString("MBW_V_PICTOCODEURL"","<img src='https://www.meteoblue.com/website/images/picto/" .$ARRAY_DATA_DAY_PICTOCODE[0] ."_iday_monochrome_hollow.svg">");
        
        $this->SetValueInt("MBW_V_WINDDIRECTION", $ARRAY_DATA_DAY_WINDDIRECTION[0]);

        $date = new DateTime('now');
        $last_update = $date->format('Y-m-d H:i:s');
		$this->SetValueString("MBW_V_LASTUPDATE", $last_update, "");
        $this->SetStatus(102);
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
				"0" => "Tornado",
				"1" => "Tropischer Sturm", 
				"2" => "Orkan", 
				"3" => "Heftiges Gewitter", 
				"4" => "Gewitter", 
				"5" => "Regen und Schnee", 
				"6" => "Regen und Eisregen", 
				"7" => "Schnee und Eisregen", 
				"8" => "Gefrierender Nieselregen", 
				"9" => "Nieselregen", 
				"10" => "Gefrierender Regen", 
				"11" => "Schauer", 
				"12" => "Schauer", 
				"13" => "Schneeflocken", 
				"14" => "Leichte Schneeschauer", 
				"15" => "St&uuml;rmiger Schneefall", 
				"16" => "Schnee", 
				"17" => "Hagel", 
				"18" => "Eisregen", 
				"19" => "Staub", 
				"20" => "Neblig", 
				"21" => "Dunst", 
				"22" => "Staubig", 
				"23" => "St&uuml,rmisch", 
				"24" => "Windig", 
				"25" => "Kalt", 
				"26" => "Bew&ouml;lkt", 
				"27" => "Gr&ouml;&szlig;tenteils bew&ouml;lkt (nachts)", 
				"28" => "Gr&ouml;&szlig;tenteils bew&ouml;lkt (tags&uuml;ber)", 
				"29" => "Teilweise bew&ouml;lkt (nachts)", 
				"30" => "Teilweise bew&ouml;lkt (tags&uuml;ber)", 
				"31" => "Klar (nachts)", 
				"32" => "Sonnig", 
				"33" => "Sch&ouml;n (nachts)", 
				"34" => "Sch&ouml;n (tags&uuml;ber)", 
				"35" => "Regen und Hagel", 
				"36" => "Hei&szlig;", 
				"37" => "Einzelne Gewitter", 
				"38" => "Vereinzelte Gewitter", 
				"39" => "Vereinzelte Gewitter", 
				"40" => "Vereinzelte Schauer", 
				"41" => "Starker Schneefall", 
				"42" => "Vereinzelte Schneeschauer", 
				"43" => "Starker Schneefall", 
				"44" => "Teilweise bew&ouml;lkt", 
				"45" => "Donnerregen", 
				"46" => "Schneeschauer", 
				"47" => "Einzelne Gewitterschauer",
				);
			return $weathercondition[$condition];
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