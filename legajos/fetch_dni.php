<?php
header('Content-Type: application/json');

if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];
    $token = 'apis-token-10494.xPmDfTMFsB8-CpaRSPYMt-B2-pVVcBCn'; // Tu token aquí

    // Iniciar llamada a API
    $curl = curl_init();

    // Configurar cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $token
        ),
    ));

    // Ejecutar cURL
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Cerrar cURL
    curl_close($curl);

    // Manejar la respuesta
    if ($httpCode == 200) {
        echo $response; // Retornar la respuesta de la API
    } else {
        echo json_encode(['error' => 'Error fetching data, HTTP status code: ' . $httpCode]);
    }
} else {
    echo json_encode(['error' => 'DNI parameter is missing']);
}
?>
