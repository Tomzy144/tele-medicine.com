<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if file was uploaded without errors
    if (isset($_FILES['NIN']) && $_FILES['NIN']['error'] == 0) {
        $file = $_FILES['NIN'];
        $fileName = basename($file['name']);
        $targetDir =  "../uploaded_files/nin_slips/"; // Change this to your desired folder
        $targetFile = $targetDir . $fileName;

        // Optional: Check the file type (accept only images)
        $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
            exit;
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            echo json_encode(['success' => true, 'message' => 'NIN file uploaded successfully!', 'fileName' => $fileName]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move the uploaded file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or file upload error.']);
    }
}
