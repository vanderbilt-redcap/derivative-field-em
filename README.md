## Derivative Fields

### Description
Allows users to specify source field (or multiple fields or infinitely repeating instrument fields), a target field and a prompt. A clickable button will appear with a target field on a form/survey where the prompt results operating on source field(s) and deposits results in target field.

### Project-Level Settings
* **Source Field:** REDCap input fields to utilize as a part of a prompt.
* **Target Field:** REDCap input field where result will be populated.
* **Prompt:** Add Prompt text. Note: Post string will be added to your prompt each time. Example, Final prompt will be "{YOUR_PROMPT}<br>
  Limit your response to what is asked. Do not add any additional content, such as introductory remarks, explanations, etc.!<br>[{COMMA_SEPERATED_VALUES_FROM_SOURCE_FILEDS}]"
* **Display on surveys:** whether to include on Survey page or not. (By default, it will be included on Data Entry page)