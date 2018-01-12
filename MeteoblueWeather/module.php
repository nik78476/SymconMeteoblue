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
        
        $this->Update();
		$this->RegisterHook("/hook/SymconMeteoblue");
		$this->SetTimerInterval("UpdateSymconMeteoblue", $this->ReadPropertyInteger("MBW_UPDATEINTERVALL") * 1000);
    }
    public function Update()
    {
		$weatherDataJSON = $this->QueryWeatherData();
		if ($weatherDataJSON == NULL)
		{
			$this->SetStatus(202);
            IPS_LogMessage($_IPS['SELF'], "Error reading external data");
			return;
		}
        $date = new DateTime('now');
        $last_update = $date->format('Y-m-d H:i:s');
		$this->SetValueString("MBW_V_LASTUPDATE", $last_update, "");
        $this->SetStatus(102);
        
        //$weather = json_decode($raw);
 
        /* Print current temperature in Basel */
        $DATA_DAY_TIME = {$weatherDataJSON->data_day->time}";
        IPS_LogMessage($_IPS['SELF'], "DATA_DAY_TIME" .$DATA_DAY_TIME);
 
        /* Print 7 day max temperature forecast */
        //foreach($weather->forecast as $day) {
        //    echo "Temperature on {$day->date} = {$day->temperature_max}";
        //}
		
		//$this->SetValueString("YWH_IPS_Wetter", $this->GenerateWeatherTable($weatherDataJSON, "<br>") );
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
	
    /*
	private function CreateVarProfileYWHTemp() {
		if (!IPS_VariableProfileExists("YHW.Temp")) {
			IPS_CreateVariableProfile("YHW.Temp", 1);
			IPS_SetVariableProfileValues("YHW.Temp", "-100,0", "100,0", "1,0");
			IPS_SetVariableProfileText("YHW.Temp", "", " °");
			IPS_SetVariableProfileAssociation("YHW.Temp", "-100,0", "%1f", "", -1);

		 }
	}
	
	private function CreateVarProfileYWHTime() {
		if (!IPS_VariableProfileExists("YHW.Time")) {
			IPS_CreateVariableProfile("YHW.Time", 1);
			IPS_SetVariableProfileText("YHW.Time", "", " Uhr");
			IPS_SetVariableProfileAssociation("YHW.Time", "", "%1f", "", -1);
		 }
	}
    */
	
    /*
	private function GenerateWeatherTable($Value, $filler){
    	$weekdays = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"); 
		$forecast = $Value->{'query'}->{'results'}->{'channel'}->{'item'}->{'forecast'};
		
		$sonnenAufgang = $Value->{'query'}->{'results'}->{'channel'}->{'astronomy'}->{'sunrise'};
		$sonnenUntergang = $Value->{'query'}->{'results'}->{'channel'}->{'astronomy'}->{'sunset'};
		
		$luftFeuchtigkeit = $Value->{'query'}->{'results'}->{'channel'}->{'atmosphere'}->{'humidity'} ." %";
		$luftDruck = $Value->{'query'}->{'results'}->{'channel'}->{'atmosphere'}->{'pressure'} ." " .$Value->{'query'}->{'results'}->{'channel'}->{'units'}->{'pressure'};
		$sichtweite = $Value->{'query'}->{'results'}->{'channel'}->{'atmosphere'}->{'visibility'} ." " .$Value->{'query'}->{'results'}->{'channel'}->{'units'}->{'distance'};
		$windGeschwindigkeit = $Value->{'query'}->{'results'}->{'channel'}->{'wind'}->{'speed'} ." " .$Value->{'query'}->{'results'}->{'channel'}->{'units'}->{'speed'};
		
		
		$this->setValueString("YWH_Sonnenaufgang", date("H:i",strtotime($sonnenAufgang)) ." Uhr");
		$this->setValueString("YWH_Sonnenuntergang", date("H:i",strtotime($sonnenUntergang)) ." Uhr");
		
		$this->setValueString("YWH_Luftfeuchtigkeit", $luftFeuchtigkeit );
		//$this->setValueString("YWH_Luftdruck", $luftDruck );
		$this->setValueString("YWH_Sichtweite", $sichtweite );
		$this->setValueString("YWH_WindGeschwindigkeit", $windGeschwindigkeit );
		
		$this->setValueString("YWH_WetterImage", $forecast[0]->code );
		
		
		$temperature = strtoupper($this->ReadPropertyString("YWHTemperature"));
		
		
		
    	if( $Value->query->count > 0 ){
			$date=new DateTime('now'); 
			
			$vorhersage_heute = "";
			$vorhersage_heute = $this->getWeatherCondition($forecast[0]->code);
			
			$variableString = html_entity_decode($vorhersage_heute,ENT_QUOTES ,"ISO-8859-15");
			$this->SetValueString("YWH_Wetter_heute", $variableString);
			$this->SetValueFloat("YWH_Heute_temp_min", $forecast[0]->low );
			$this->SetValueFloat("YWH_Heute_temp_max", $forecast[0]->high );
			
			$HTMLBoxType = $this->ReadPropertyInteger("YWHDisplay");
			
			// build table
			$weatherstring = '<table width="100%">';
			// build header with weekdays



			if( $HTMLBoxType == 1 ){	
				$weatherstring .= '<tr>';
				for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
					$weatherstring .= '<td align="center">'; 
					$day = date("w")+$i;
					$weatherstring .= $weekdays[$day];
					$weatherstring .= '</td>';
				}
				$weatherstring .= '</tr>';
			}
			
				
			// row with weather infos (image + description)	
			$weatherstring .= '<tr>';
			

			for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
				$weatherstring .= '<td align="center">';
				$weatherstring .= $filler;
				$weatherstring .= '<img src="/hook/SymconYahooWeather/' .$forecast[$i]->code .'.png" style="height:' .$this->ReadPropertyInteger("YWHImageZoom") .'%;width:auto;">';

				if( $HTMLBoxType == 1 ){
					$weatherstring .= '<br>';
					$weatherstring .= $this->getWeatherCondition($forecast[$i]->code);
				}
				$weatherstring .= '</td>';
			}

			$weatherstring .= '</tr>';
			
			if( $HTMLBoxType == 1 ){
				// row with weather temperature			
				$weatherstring .= '<tr>';
				for( $i = 0; $i < $this->ReadPropertyInteger("YWHDays"); $i++ ){
					$weatherstring .= '<td align="center">';
					$weatherstring .= 'min ' .$forecast[$i]->low .' &deg;' .$temperature;
					$weatherstring .= '<br>';
					$weatherstring .= 'max ' .$forecast[$i]->high .' &deg;' .$temperature;
					$weatherstring .= '</td>';
				}
				$weatherstring .= '</tr>';
			}
			
			
			// finish table
			$weatherstring .= '</table>';
			
			//IPS_LogMessage("SymconYahooWeather", "weatherstring: ". $weatherstring);
			return $weatherstring;
		} 
		else return "Weather is not available";
  	}
    */
		
	private function QueryWeatherData(){
        /* Download and parse data for Basel (47.5667°/7.6° 263m asl) */
        
        $url  = "http://my.meteoblue.com/packages/basic-day?";
        $url .= "apikey=" .$this->ReadPropertyString("MBW_APIKEY");
        $url .= "&lat=" .$this->ReadPropertyString("MBW_LATITUDE");
        $url .= "&lon=" .$this->ReadPropertyString("MBW_LONGITUDE");
        $url .= "&asl=" .$this->ReadPropertyString("MBW_ASL");
        $url .= "&lang=" .$this->ReadPropertyString("MBW_LANGUAGE");
        $url .= "&temperature=" .$this->ReadPropertyString("MBW_TEMPERATURE");
        
        IPS_LogMessage($_IPS['SELF'], "URL: ". $url);
        
        $rawWeatherData = file_get_contents($url);
        return json_decode($rawWeatherData);
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
}
?>