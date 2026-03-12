<?php
namespace Vanderbilt\DerivativeFieldExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

class DerivativeFieldExternalModule extends AbstractExternalModule {
    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance) {
		$this->includeJSCode($project_id, $record, $instrument, true);
	}

	function hook_survey_page($project_id, $record, $instrument) {
        $enabledFlag = $this->getProjectSetting('on-surveys');
		$this->includeJSCode($project_id, $record, $instrument, $enabledFlag);
	}

	function includeJSCode($project_id, $record, $instrument, $enabledFlag) {
        if ($enabledFlag) {
            $targetField = $this->getProjectSetting('target-field');
            //$sourceFields = $this->getSubSettings('source-fields');
            $buttonHtml = "<button class='btn btn-defaultrc btn-xs fs11' style='color:#800000;margin-left:3px;padding:1px 5px 0;' onclick='populateResponse(); return false;'><i class='fas fa-wand-magic-sparkles' style='margin-right:4px;'></i> Evaluate</button>";
            $infoHtml = "<a href='javascript:;' onclick='showPromptInfo(); return false;'> <i class='fas fa-terminal'></i></a>";
            ?>
            <script>
                var targetField = "<?=$targetField?>";
                var buttonHTML = "<?=$buttonHtml?>";
                var infoHTML = "<?=$infoHtml?>";
                var ajax_url = "<?php echo $this->getUrl('ajax_process.php'); ?>";
            </script>
            <script src="<?= $this->getUrl("script.js") ?>" type="text/javascript" charset="utf-8"></script>
            <?php
        }
	}

	public function validateSettings($settings){
        if ($this->getProjectId() != '') {
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
	}
}
