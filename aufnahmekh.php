<?php
	/**
	* aufnahmekh-json
	*
	* Gets the reception hospital in Linz from a public listing and formats it machine readable.
	*
	* @copyright  Copyright (c) 2019 Matthias Schaffer (https://matthiasschaffer.com)
	* @license    https://spdx.org/licenses/MIT.html   MIT License
	* @link       https://github.com/fellwell5/aufnahmekh-json
	*/
	
	//ini_set('display_errors', 1);
	
	/**
	 * URL-PARAMETER
	 * Show only hospitals for the given date
	 *
	 * @param date $_GET["date"] e.g. 2019-09-28 or 28.09.2019
	 */ 
	/**
	 * URL-PARAMETER
	 * Forces the script to reload the cached json file. Normally it is reloaded once per day.
	 *
	 * @param boolean $get["nocache"] can be empty, e.g. ?nocache&date=xxx
	 */
	function getAufnahmeKH($get) {
		$return = [];
		if(isset($get["date"])){
			$getbydate = strtotime($get["date"]);
			/**
			 * If the date can't be converted to a timestampt, the script throws an error message and exits with http error 400.
			 */
			if(!$getbydate){
				$return['code'] = 400;
				$return['message'] = $get["date"]." seems to be not a valid date.<br>The date should be formatted like this 2019-09-28 or 28.09.2019.";
				return $return;
			}
		}

		/**
		 * If the cache file "aufnahmekh.json" doesn't exist, is not from this day or the nocache parameter was set:
		 * The file will be generated newly.
		 */
		if(!file_exists("aufnahmekh.json") || date("d.m.Y", filemtime("aufnahmekh.json")) != date("d.m.Y") || isset($get["nocache"])){
			@unlink("aufnahmekh.json");

			/**
			 * The webpage from the austrian red cross gets loaded and the data from the selected div (via class selector) is loaded.
			 */
			$DOM = new DOMDocument();
			@$DOM->loadHTMLFile("https://www.roteskreuz.at/ooe/dienststellen/perg/ichbrauchehilfe/aufnahmekrankenhaus/linz/");

			$finder = new DomXPath($DOM);
			$nodes = $finder->query("//div[contains(@class, 'tx-lumophpinclude-pi1')]/div");

			$long2short = array("Barmherzige Brüder Linz" => "BHB", "Elisabethinen Linz - Ordensklinikum" => "ELIS", "Barmherzige Schwestern Linz - Ordensklinikum" => "BHS");
			$Notdienste = array();

			/**
			 * We loop through the divs in the main div.
			 * The data is loaded from the child nodes of the event div.
			 */
			foreach($nodes as $sNodeDetail){
				$from_text = $sNodeDetail->childNodes->item(0)->childNodes->item(0)->nodeValue;
				$from_date = substr($from_text, 9, 6).date("Y")." ".substr($from_text, 16, 5);
				$to_text = $sNodeDetail->childNodes->item(0)->childNodes->item(2)->nodeValue;
				$to_date = substr($to_text, 9, 6).date("Y")." ".substr($to_text, 16, 5);
				$from = strtotime($from_date);
				$to = strtotime($to_date);
				if($from > $to){
					/**
					 * If the from timestamp is bigger than the to timestamp, the year changed over the night.
					 * So we add one year to the to timestamp.
					 */
					$to_date = substr($to_text, 9, 6).(date("Y")+1)." ".substr($to_text, 16, 5);
					$to = strtotime($to_date);
				}

				$name = $sNodeDetail->childNodes->item(1)->childNodes->item(0)->nodeValue;
				$address = $sNodeDetail->childNodes->item(1)->childNodes->item(3)->nodeValue;
				$contact = str_replace("\r\n", "", $sNodeDetail->childNodes->item(1)->childNodes->item(6)->nodeValue);

				$Notdienste[] = array(
					"from_ts" => $from,
					"from_date" => $from_date,
					"to_ts" => $to,
					"to_date" => $to_date,
					"name" => $name,
					"short_name" => (array_key_exists($name, $long2short)) ? $long2short[$name] : $name,
					"address" => $address,
					"contact" => $contact
				);
			}
			$json = json_encode($Notdienste);
			file_put_contents("aufnahmekh.json", $json);
		}else{
			/**
			 * Use cached version
			 */
			$json = file_get_contents("aufnahmekh.json");
		}


		/**
		 * Show only the hospitals for the given date
		 */
		if(isset($get["date"])){
			$Notdienste = array();
			$json = json_decode($json, true);
			foreach($json as &$item){
				if(date("z-Y", $getbydate) != date("z-Y", $item["from_ts"])) continue;
				$Notdienste[] = $item;
			}
			$json = json_encode($Notdienste);
		}
		$return['header'] = "Content-Type: application/json";
		$return['content'] = $json;
		return $return;
	}
