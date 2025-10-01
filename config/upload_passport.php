    <?php


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $patient_id = $_POST['patient_id'] ?? '';
    $file = $_FILES['passport'] ?? null;

    if (!$patient_id || !$file) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit;
    }

    // Step 1: Get current image name from backend
    // $fetchUrl = 'https://yemas-consult-base-api.onrender.com/index.php';
    $fetchUrl = 'https://localhost/tele-medicine-base-api/index.php';

    $ch = curl_init($fetchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'action' => 'get_passport_name',
        'patient_id' => $patient_id
    ]);
    $oldResponse = curl_exec($ch);
    curl_close($ch);

    $oldData = json_decode($oldResponse, true);
    $oldFilename = $oldData['passport'] ?? '';

    if ($oldFilename) {
        $oldPath = '../uploaded_files/patient_profile_pix/' . $oldFilename;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    


    // Step 2: Validate image
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mime = mime_content_type($file['tmp_name']);
    $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/heic', 'image/heif'];

    if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedMimes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit;
    }

    // Step 3: Save new image to frontend folder
    $newFileName = uniqid('passport_', true) . '.' . $ext;
    $targetPath = '../uploaded_files/patient_profile_pix/' . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
        exit;
    }

    // Step 4: Tell backend to update DB with new filename
    // $backendUrl = 'https://yemas-consult-base-api.onrender.com/index.php';
    $backendUrl = 'http://localhost/tele-medicine-base-api/index.php';
    $ch = curl_init($backendUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'action' => 'update_passport_name',
        'patient_id' => $patient_id,
        'filename' => $newFileName
    ]);
    $backendResponse = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Final response
    if ($curlError) {
        echo json_encode(['success' => false, 'message' => 'Backend error: ' . $curlError]);
    } else {
        echo $backendResponse;
    }
?>
