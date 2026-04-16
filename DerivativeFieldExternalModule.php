<?php
namespace Vanderbilt\DerivativeFieldExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

class DerivativeFieldExternalModule extends AbstractExternalModule {
    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance) {
		$this->includeJSCode($project_id, $record, $instrument, false);
	}

	function hook_survey_page($project_id, $record, $instrument) {
        $enabledFlag = $this->getProjectSetting('on-surveys');
		$this->includeJSCode($project_id, $record, $instrument, true);
	}

	function includeJSCode($project_id, $record, $instrument, $is_survey_page = false) {
        ?>
        <script src="<?= $this->getUrl("script.js") ?>" type="text/javascript" charset="utf-8"></script>
        <?php
        $settings = $this->getProjectSetting('settings');
        foreach ($settings as $num => $setting) {
            $onSurvey = $this->getProjectSetting('on-surveys')[$num];
            if ($is_survey_page == true && $onSurvey == false) {
                continue; // Skip this setting if survey page accessed and on-surveys setting not selected
            }
            if ($setting == true) {
                $targetField = $this->getProjectSetting('target-field')[$num];
                $sourceField = $this->getProjectSetting('source-field')[$num];
                $buttonHtml = '<button type="button" class="evaluate-prompt-btn btn btn-defaultrc btn-xs fs11" style="color:#800000;margin-left:3px;padding:1px 5px 0;" this-record="'.$record.'" this-setup="'.$num.'" this-target="'.$targetField.'" this-source="'.$sourceField.'"><i class="fas fa-wand-magic-sparkles" style="margin-right:4px;"></i> Evaluate</button>';
                $infoHtml = "<a href='javascript:;' onclick='showPromptInfo(); return false;'> <i class='fas fa-terminal'></i></a>";
                ?>
                <script>
                    var targetField = "<?=$targetField?>";
                    var buttonHTML = '<?=$buttonHtml?>';
                    var infoHTML = "<?=$infoHtml?>";
                    var num = "<?=$num?>";
                    var ajax_url = "<?php echo $this->getUrl('ajax_process.php'); ?>";
                    insertButton(targetField, buttonHTML);
                </script>
                <?php
            }
        }
	}

	public function validateSettings($settings){
        if ($this->getProjectId() != '') {
            $source_field = $settings['source-field'];
            $target_field = $settings['target-field'];
            $prompt = $settings['prompt'];

            $errorMessages = [];
            if($source_field == ''){
                $errorMessages[] = "Please select a source field.";
            }
            if ($target_field == '') {
                $errorMessages[] = "Please select a target field.";
            }
            if ($prompt == '') {
                $errorMessages[] = "Please enter a prompt text.";
            }

            return implode("\n", $errorMessages);
        }
	}
}
