<?php
require 'vendor/autoload.php';

use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;

function create_assessment(
    string $recaptchaKey,
    string $token,
    string $project,
    string $action
): bool {
    $client = new RecaptchaEnterpriseServiceClient();
    $projectName = $client->projectName($project);

    $event = (new Event())
        ->setSiteKey($recaptchaKey)
        ->setToken($token);

    $assessment = (new Assessment())
        ->setEvent($event);

    try {
        $response = $client->createAssessment($projectName, $assessment);

        if ($response->getTokenProperties()->getValid() == false) {
            return false; // Invalid token
        }

        if ($response->getTokenProperties()->getAction() == $action) {
            $score = $response->getRiskAnalysis()->getScore();
            return $score >= 0.5; // Customize score threshold as needed
        } else {
            return false; // Action mismatch
        }
    } catch (exception $e) {
        return false; // Handle error
    }
}

$recaptchaKey = '6LfZtDQqAAAAAN1N3RiSZVGdHGBpV27rD9tRAguI';
$token = $_POST['recaptcha-token'];
$project = 'saptransport-1725297036225';
$action = 'submit';

$isHuman = create_assessment($recaptchaKey, $token, $project, $action);

if ($isHuman) {
    // Proceed with form processing (e.g., sending email, saving to database)
} else {
    // Handle failed verification (e.g., show error message)
    echo "Please verify you are human.";
}
?>
