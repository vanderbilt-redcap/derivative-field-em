<?php
namespace Vanderbilt\DerivativeFieldExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

// This API key it for the account with email mark.mcever@vanderbilt.edu.
// This could easily be switched to another account (like the datacore email) if need be.
const API_KEY = '8bb9d0cd-d2ee-4a10-af12-e85a87155390';
const DICTIONARY_TYPE = 'medical/v2';
const BACKUP_API_KEY = 'f0898fcd-04a9-44f9-82a3-08af614d31e9';
const BACKUP_DICTIONARY_TYPE = 'collegiate/v1';

class DerivativeFieldExternalModule extends AbstractExternalModule {

    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance) {
		$this->includeJSCode($project_id, $record, $instrument);
	}

	function hook_survey_page($project_id, $record, $instrument) {
		$this->includeJSCode($project_id, $record, $instrument, 'on-surveys');
	}

	function includeJSCode($project_id, $record, $instrument, $enabledSettingName = '') {

		$targetField = $this->getProjectSetting('target-field');
		$sourceFields = $this->getSubSettings('source-fields');
		$buttonHtml = "<button class='btn btn-defaultrc btn-xs fs11' style='color:#800000;margin-left:3px;padding:1px 5px 0;' onclick='populateResponse(); return false;'><i class='fas fa-wand-magic-sparkles' style='margin-right:4px;'></i> Evaluate</button>";

		?>
		<script>
            var targetField = "<?=$targetField?>";
			var buttonHTML = "<?=$buttonHtml?>";
            var ajax_url = "<?php echo $this->getUrl('ajax_process.php'); ?>";
		</script>
        <script src="<?= $this->getUrl("script.js") ?>" type="text/javascript" charset="utf-8"></script>
		<?php
	}

	public function validateSettings($settings){
		$source_fields = $settings['source-field'];
		$target_field = $settings['target-field'];
		$prompt = $settings['prompt'];

		$errorMessages = [];
		for($i=0;$i<count($source_fields);$i++){
			if($source_fields[$i] == ''){
                $errorMessages[] = "Please select #".($i+1)." source field.";
			}
		}
        if ($target_field == '') {
            $errorMessages[] = "Please select target field.";
        }
        if ($prompt == '') {
            $errorMessages[] = "Please enter a prompt text.";
        }

		return implode("\n", $errorMessages);
	}

	public function getDictionaryResponse($word){
		$dir = sys_get_temp_dir() . "/dictionary-audio-url-cache/";

		// We encode the word as an easy way to prevent malicious parameters.
		$path = $dir . md5($word);
		if(file_exists($path)){
			return file_get_contents($path);
		}

		/**
		 * Avoid injection vulnerabilities in error messages.
		 */
		$word = htmlentities($word, ENT_QUOTES);

		foreach([DICTIONARY_TYPE => API_KEY,BACKUP_DICTIONARY_TYPE => BACKUP_API_KEY] as $dictionaryType => $apiKey) {
			$dictionaryApiLink = "http://www.dictionaryapi.com/api/references/" . $dictionaryType . "/xml/" . urlencode($word) . "?key=" . urlencode($apiKey);

			$response = simplexml_load_string(file_get_contents($dictionaryApiLink));

			$entry = @$response->entry;
			$wordFromEntry = @$entry->ew;
			$errorMessage = null;

			if(!$wordFromEntry){
				$errorMessage = "Pronunciation audio for the term '$word' could not be found.";
				$suggestions = @$response->suggestion;
				if($suggestions){
					$errorMessage .= "  Here is the list of suggestions:\n\n";
					foreach($suggestions as $suggestion){
						$errorMessage .= $suggestion . "\n";
					}
				}
			}
	//		else if($entry->ew != $word){
	//			$errorMessage = "Could not find an exact match for '$word'.  The closest entry was '{$entry->ew}'.";
	//		}

			## If a term is found in the medical dictionary, don't bother checking the collegiate dictionary
			if(!$errorMessage) break;
		}

		if($errorMessage){
			$response = ['error' => $errorMessage];
		}
		else{
			$wav = @$entry->vr->sound->wav; // Ex: "dyspnea"

			if(!$wav){
				$wav = @$entry->uro->sound->wav;
			}

			if(!$wav){
				$wav = $entry->sound->wav; // Ex: "shampoo"
			}

			if(!$wav) {
				$response = [
					'error' => "Could not find audio for the term '$word'.  Please check the response in the browser console in case the filename is in an unexpected location.",
					'response' => $entry
				];
			}
			else{
				$response = ['filename' => $wav->__toString()];
			}
		}

		$response = json_encode($response);
		
		file_put_contents($path, $response);

		return $response;
	}
}
