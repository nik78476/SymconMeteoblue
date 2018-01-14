<?
class SymconMeteoblue extends IPSModule
{
	
	public function Create()
    {
        //Never delete this line!
        parent::Create();
        
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        
        $this->RegisterPropertyString("MBW_APIKEY", "41f2dd49fb6a");
		$this->RegisterPropertyString("MBW_LATITUDE", "47.660" );
        $this->RegisterPropertyString("MBW_LONGITUDE", "9.176");
		$this->RegisterPropertyString("MBW_ASL","402");
		$this->RegisterPropertyInteger("MBW_UPDATEINTERVALL", 100);
		$this->RegisterPropertyString("MBW_LANGUAGE", "de");
        $this->RegisterPropertyString("MBW_TEMPERATURE", "C");
        $this->RegisterPropertyInteger("MBW_FORECASTDAYS", "1");
        
		$this->RegisterVariableString("MBW_V_LASTUPDATE", "Last Update");
        $this->RegisterVariableInteger("MBW_V_UVINDEX", "UV Index");
        $this->RegisterVariableFloat("MBW_V_TEMPERATURE_MAX", "Temp (max)", "~Temperature");
        $this->RegisterVariableFloat("MBW_V_TEMPERATURE_MIN", "Temp (min)", "~Temperature");
        
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
            IPS_LogMessage($_IPS['SELF'], "Error reading external data");
			return;
		}
        
        /* Print current temperature in Basel */
        $ARRAY_DATA_DAY_TIME = $weatherDataJSON->{'data_day'}->{'time'};
        $ARRAY_DATA_DAY_PICTOCODE = $weatherDataJSON->{'data_day'}->{'pictocode'};
        $ARRAY_DATA_DAY_UVINDEX = $weatherDataJSON->{'data_day'}->{'uvindex'};
        
        $ARRAY_DATA_DAY_TEMPMAX = $weatherDataJSON->{'data_day'}->{'temperature_max'};
        $ARRAY_DATA_DAY_TEMPMIN = $weatherDataJSON->{'data_day'}->{'temperature_min'};
        
		$this->SetValueInt("MBW_V_UVINDEX", $ARRAY_DATA_DAY_UVINDEX[0]);
        $this->SetValueFloat("MBW_V_TEMPERATURE_MAX", $ARRAY_DATA_DAY_TEMPMAX[0]);
        $this->SetValueFloat("MBW_V_TEMPERATURE_MIN", $ARRAY_DATA_DAY_TEMPMIN[0]);

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

    private function getDEMOWeatherData()
        $demodata = "{\"metadata\": 
        {
            \"name\": \"\", 
            \"latitude\": 47.56, 
            \"longitude\": 7.57, 
            \"height\": 279, 
            \"timezone_abbrevation\": \"CET\", 
            \"utc_timeoffset\": 1.00, 
            \"modelrun_utc\": \"2018-01-12 12:00\", 
            \"modelrun_updatetime_utc\": \"2018-01-12 20:01\"
        }, 
        \"units\": 
        {
            \"time\": \"YYYY-MM-DD hh:mm\", 
            \"predictability\": \"percent\", 
            \"precipitation_probability\": \"percent\", 
            \"pressure\": \"hPa\", 
            \"relativehumidity\": \"percent\", 
            \"temperature\": \"C\", 
            \"winddirection\": \"degree\", 
            \"precipitation\": \"mm\", 
            \"windspeed\": \"ms-1\"
        }, 
        \"data_day\": 
        {
            \"time\": [\"2018-01-13\", \"2018-01-14\", \"2018-01-15\", \"2018-01-16\", \"2018-01-17\", \"2018-01-18\", \"2018-01-19\"], 
            \"pictocode\": [4, 1, 4, 6, 8, 12, 11], 
            \"uvindex\": [1, 1, 1, 1, 1, null, null], 
            \"temperature_max\": [3.88, 4.27, 6.87, 7.40, 6.29, 9.17, 3.90], 
            \"temperature_min\": [-0.63, -1.16, -1.55, 5.86, 3.13, 3.29, 0.60], 
            \"temperature_mean\": [1.19, 0.70, 2.49, 6.61, 4.27, 5.82, 2.38], 
            \"felttemperature_max\": [-0.46, 0.54, 2.44, 1.38, 0.17, 2.10, -1.01], 
            \"felttemperature_min\": [-4.71, -6.08, -5.51, -1.39, -2.42, -1.77, -3.77], 
            \"winddirection\": [90, 135, 90, 225, 270, 270, 270], 
            \"precipitation_probability\": [0, 0, 32, 89, 100, 88, 87], 
            \"rainspot\": [\"0000000000000000000000000000000000000000000000000\", \"0000000000000000000000000000000000000000000000000\", \"1190000000000000000000100000001000011110012110111\", \"3333333333333333333333333333333333333333333333333\", \"3333333333333333333333333333333333333333333333333\", \"3333333333333333333333333333333333333333333333333\", \"3333333333333333333333333333333333333233333323333\"], 
            \"predictability_class\": [4, 5, 3, 3, 3, 2, 1], 
            \"predictability\": [78, 88, 56, 42, 43, 34, 17], 
            \"precipitation\": [0.00, 0.00, 0.00, 14.02, 13.29, 9.11, 13.35], 
            \"snowfraction\": [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.51], 
            \"sealevelpressure_max\": [1026, 1022, 1022, 1008, 1009, 1010, 1010], 
            \"sealevelpressure_min\": [1020, 1019, 1009, 996, 995, 999, 1004], 
            \"sealevelpressure_mean\": [1023, 1020, 1017, 1002, 1001, 1003, 1006], 
            \"windspeed_max\": [3.91, 3.82, 5.12, 8.44, 6.82, 9.19, 4.98], 
            \"windspeed_mean\": [2.43, 2.57, 2.61, 6.65, 5.65, 6.28, 3.95], 
            \"windspeed_min\": [0.67, 1.18, 1.34, 5.44, 4.47, 4.36, 3.04], 
            \"relativehumidity_max\": [92, 91, 91, 84, 90, 93, 93], 
            \"relativehumidity_min\": [64, 59, 60, 67, 76, 75, 76], 
            \"relativehumidity_mean\": [81, 77, 77, 78, 85, 83, 88], 
            \"convective_precipitation\": [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00], 
            \"precipitation_hours\": [0.00, 0.00, 0.00, 14.00, 14.00, 10.00, 10.00], 
            \"humiditygreater90_hours\": [2.00, 1.00, 1.00, 0.00, 0.00, 2.00, 8.00]
        }
    }";
    return $demodata;
    }
}
?>