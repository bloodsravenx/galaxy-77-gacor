    <?php
    // Include the configuration
    include 'config.php';
    include '../../function/connect.php';
    $cuk = mysqli_query($koneksi, "SELECT * FROM tb_web");
    $cek_web = mysqli_fetch_array($cuk);
    $urlweb = $cek_web['url'];

    // Function to create a user
    function createUser($userLogin)
    {
        global $apiConfig, $hallInfo;

        // Create User Request Data
        $requestData = array(
            'cmd' => 'userCreate',
            'hallId' => $hallInfo['hallId'],
            'userLogin' => $userLogin,
        );

        // Generate Signature
        $dataToSign = array_intersect_key($requestData, array_flip(['cmd', 'hallId', 'userLogin']));
        ksort($dataToSign, SORT_STRING);
        $dataToSign['hallKey'] = $hallInfo['hallKey'];

        $input = implode(':', $dataToSign);
        $signature = base64_encode(md5($input, true));

        // Add the signature to the request data
        $requestData['sign'] = $signature;

        // Convert request data to JSON
        $requestBody = json_encode($requestData);
        
        // Perform cURL request
        return performCurlRequest($apiConfig['url'], $apiConfig['method'], $requestBody);
    }

    // Function to request deposit
    function transactionIN($userLogin, $amount)
    {
        global $apiConfig, $hallInfo;

        $requestData = array(
            'cmd' => 'userCash',
            'hallId' => $hallInfo['hallId'],
            'userLogin' => $userLogin,
            'operation' => 'in',
            'cash' => $amount,
        );

        // Generate Signature
        $dataToSign = array_intersect_key($requestData, array_flip(['cmd', 'hallId', 'userLogin', 'operation', 'cash']));
        ksort($dataToSign, SORT_STRING);
        $dataToSign['hallKey'] = $hallInfo['hallKey'];

        $input = implode(':', $dataToSign);
        $signature = base64_encode(md5($input, true));

        // Add the signature to the request data
        $requestData['sign'] = $signature;

        // Convert request data to JSON
        $requestBody = json_encode($requestData);

        // Perform cURL request
        return performCurlRequest($apiConfig['url'], $apiConfig['method'], $requestBody);
    }
    function TransactionOUT($userLogin, $amount)
    {
        global $apiConfig, $hallInfo;

        $requestData = array(
            'cmd' => 'userCash',
            'hallId' => $hallInfo['hallId'],
            'userLogin' => $userLogin,
            'operation' => 'out',
            'cash' => $amount,
        );

        // Generate Signature
        $dataToSign = array_intersect_key($requestData, array_flip(['cmd', 'hallId', 'userLogin', 'operation', 'cash']));
        ksort($dataToSign, SORT_STRING);
        $dataToSign['hallKey'] = $hallInfo['hallKey'];

        $input = implode(':', $dataToSign);
        $signature = base64_encode(md5($input, true));

        // Add the signature to the request data
        $requestData['sign'] = $signature;

        // Convert request data to JSON
        $requestBody = json_encode($requestData);

        // Perform cURL request
        return performCurlRequest($apiConfig['url'], $apiConfig['method'], $requestBody);
    }

    // Function to get user balance
    function getUserBalance($userLogin)
    {
        global $apiConfig, $hallInfo;

        // Get User Balance Data
        $requestData = array(
            'cmd' => 'userInfo',
            'hallId' => $hallInfo['hallId'],
            'userLogin' => $userLogin,
        );

        // Generate Signature
        $dataToSign = array_intersect_key($requestData, array_flip(['cmd', 'hallId', 'userLogin']));
        ksort($dataToSign, SORT_STRING);
        $dataToSign['hallKey'] = $hallInfo['hallKey'];

        $input = implode(':', $dataToSign);
        $signature = base64_encode(md5($input, true));

        // Add the signature to the request data
        $requestData['sign'] = $signature;

        // Convert request data to JSON
        $requestBody = json_encode($requestData);

        // Perform cURL request
        return performCurlRequest($apiConfig['url'], $apiConfig['method'], $requestBody);
    }

    // Function to get games list
    function getGamesList()
    {
        global $apiConfig, $hallInfo;

        // Games List Request Data
        $requestData = array(
            'cmd' => 'gamesList',
            'hall' => $hallInfo['hallId'],
            'key' => $hallInfo['hallKey'],
        );

        // Convert request data to JSON
        $requestBody = json_encode($requestData);

        // Perform cURL request
        $response = performCurlRequest($apiConfig['url_gamelist'], $apiConfig['method'], $requestBody);

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Check if the response is valid
        if ($decodedResponse && isset($decodedResponse['gameTitles']) && isset($decodedResponse['games'])) {
            return $decodedResponse;
        } else {
            return false;
        }
    }

    function openGame($userLogin, $gameId)
    {
        global $apiConfig, $hallInfo, $urlweb;

        // Open Game Request Data
        $requestData = array(
            'cmd' => 'openGame',
            'hall' => $hallInfo['hallId'],
            'domain' => $urlweb, // Replace with your actual domain
            'exitUrl' => $urlweb, // Replace with your close.php URL
            'language' => 'en',
            'key' => $hallInfo['hallKey'],
            'login' => $userLogin,
            'gameId' => $gameId,
            'demo' => '0',
        );

        // Convert request data to JSON
        $requestBody = json_encode($requestData);

        // Perform cURL request
        $response = performCurlRequest($apiConfig['url_gamelist'] . 'openGame/', $apiConfig['method'], $requestBody);

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Check if the response is valid
        if ($decodedResponse && isset($decodedResponse['status']) && $decodedResponse['status'] === 'success') {
            return $decodedResponse['content']['game'];
        } else {
            return false;
        }
    }

    // Function to redirect the user to the game URL
    function redirectToGame($gameData)
    {
        if ($gameData && isset($gameData['url'])) {
            header('Location: ' . $gameData['url']);
            exit;
        }
    }


    // Function to get session list
    function getSessionList($userLogin, $dateFrom, $dateTo)
    {
        global $apiConfig, $hallInfo;

        // Session List Request Data
        $requestData = array(
            'cmd' => 'sessionList',
            'hallId' => $hallInfo['hallId'],
            'userLogin' => $userLogin,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,

        );

        // Generate Signature
        $dataToSign = array_intersect_key($requestData, array_flip(['cmd', 'hallId', 'userLogin', 'dateFrom', 'dateTo']));
        ksort($dataToSign, SORT_STRING);
        $dataToSign['hallKey'] = $hallInfo['hallKey'];
        $input = implode(':', $dataToSign);
        $signature = base64_encode(md5($input, true));

        // Add the signature to the request data
        $requestData['sign'] = $signature;

        // Convert request data to JSON
        $requestBody = json_encode($requestData);

        // Perform cURL request
        return performCurlRequest($apiConfig['url_gamelist']  . 'session/', $apiConfig['method'], $requestBody);
    }



    // Function to perform cURL request
    function performCurlRequest($url, $method, $requestBody)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($requestBody),
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        return $response;
    }
