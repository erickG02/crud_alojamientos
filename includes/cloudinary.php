<?php
function uploadToCloudinary($imageTmpPath, $cloudName, $uploadPreset) {
    $url = "https://api.cloudinary.com/v1_1/$cloudName/image/upload";

    $data = [
        'file' => new CURLFile($imageTmpPath),
        'upload_preset' => $uploadPreset
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return false;
    }

    $result = json_decode($response, true);
    return $result['secure_url'] ?? false;
}
