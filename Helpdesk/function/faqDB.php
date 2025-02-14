<?php
session_start();
require '../../database.php';
header('Content-Type: application/json');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'getFAQs':
        $stmt = $conn->prepare("SELECT faq_id, faq_question AS question, faq_content AS solution FROM FAQ");
        $stmt->execute();
        $result = $stmt->get_result();
        $faqs = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'faqs' => $faqs]);
        $stmt->close();
        break;

    case 'addFaq':
        $question = $_POST['question'];
        $solution = $_POST['solution'];
        $stmt = $conn->prepare("INSERT INTO FAQ (faq_question, faq_content) VALUES (?, ?)");
        $stmt->bind_param("ss", $question, $solution);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'FAQ added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'deleteFaq':
        $faq_id = $_POST['faq_id'];
        $stmt = $conn->prepare("DELETE FROM FAQ WHERE faq_id = ?");
        $stmt->bind_param("i", $faq_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'FAQ deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'editFaq':
        $faq_id = $_POST['faq_id'];
        $question = $_POST['question'];
        $solution = $_POST['solution'];
        $stmt = $conn->prepare("UPDATE FAQ SET faq_question = ?, faq_content = ? WHERE faq_id = ?");
        $stmt->bind_param("ssi", $question, $solution, $faq_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'FAQ updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        break;
}

$conn->close();
?>