<?php

$response = ['status' => 0, 'message'   => ''];

$projectId = $module->getProjectId();
/*$api_keys = $module->getProjectSetting('api-key');
$endpoints = $module->getProjectSetting('endpoint');
$api_versions = $module->getProjectSetting('api-version');*/
if (isset($_POST['action']) && $_POST['action'] == 'process') {
    $api_key = '81ce3b70e4f94439aaaf57c4682ba5f8';
    $endpoint = 'https://vumc-openai-16.openai.azure.com/openai/deployments/gpt-4o-mini/';
    $api_version = '2025-01-01-preview';
    $prompt = nl2br(htmlspecialchars($module->getProjectSetting('prompt')));

    $data = [
        "model" => $api_version,
        "messages" => [
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.2,
        "max_tokens" => 4000,
        "frequency_penalty" => 0,
        "presence_penalty" => 0
    ];
    $data_json = json_encode($data, JSON_UNESCAPED_SLASHES);
    // Build endpoint URL
    $url = $endpoint . "chat/completions";
    $url .= "?api-version=".urlencode($api_version);

    $contentTypes = [
        "application/json"    => "Content-Type: application/json",
        "multipart/form-data" => "Content-Type: multipart/form-data",
    ];

    $headers = [
        $contentTypes["application/json"],
    ];

    if ($api_key != '') { // apiKey may be empty, in case user entered details of locally hosted OpenAI compatible AI service
        // Set this header for "Microsoft Azure OpenAI" service
        $headers[] = "api-key: $api_key";
        // Set this header for OpenAI compatible AI services if entered at settings
        $headers[] = "Authorization: Bearer $api_key";
    }

    $completionDecoded = curlAPIPost($api_key, $endpoint . "chat/completions?api-version=" . $api_version, json_encode($data), $headers);
    //$completionDecoded = json_decode($complete,true);
    if (!is_array($completionDecoded)) {
        $completionDecoded = [];
        $response['errors'] = "ERROR - No response returned from the AI service";
    } elseif (array_key_exists("error", $completionDecoded)) {
        $response['errors'] = "ERROR - Code {$completionDecoded['error']['code']}: {$completionDecoded['error']['message']}";
    }

    $response['status'] = 1;
    if (isset($response['errors']) && $response['errors'] != '') {
        $response['status'] = 0;
    }

    $chatGptResponse = filter_tags(trim($completionDecoded["choices"][0]["message"]["content"] ?? ""));
    $response['message'] = $chatGptResponse;
}
function curlAPIPost($api_key, $url, $data, $headers = [])
{
    if (empty($headers)) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
            'OpenAI-Beta: assistants=v1',
        ];
    }
    $response = sendRequest($url, 'POST', $data, $headers);
    return json_decode($response, true);
}
function sendRequest($url, $method, $post_fields = [], $headers)
{
    $curl_info = [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_POSTFIELDS     => $post_fields,
        CURLOPT_HTTPHEADER     => $headers,
    ];

    if ($post_fields == []) {
        unset($curl_info[CURLOPT_POSTFIELDS]);
    }

    $curl = curl_init();

    curl_setopt_array($curl, $curl_info);

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);

    curl_close($curl);

    return $response;
}

print json_encode(($response));