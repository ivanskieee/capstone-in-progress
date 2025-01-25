<?php
include '../database/connection.php';

if (isset($_GET['faculty_id']) && isset($_GET['category'])) {
    $facultyId = $_GET['faculty_id'];
    $category = $_GET['category'];

    if ($category === 'faculty') {
        // Query for faculty ratings data
        $stmt = $conn->prepare("
            SELECT ea.rate AS rating
            FROM evaluation_answers ea
            WHERE ea.faculty_id = :faculty_id AND ea.rate IS NOT NULL
            ORDER BY ea.evaluation_id ASC
        ");
        $stmt->execute(['faculty_id' => $facultyId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create labels as sequential numbers (1, 2, 3, ...)
        $labels = range(1, count($data));
        $ratings = array_column($data, 'rating');

        echo json_encode(['labels' => $labels, 'dataset' => $ratings]);

    } elseif ($category === 'self-faculty') {
        // Query for self faculty evaluation data
        $stmt = $conn->prepare("
            SELECT sfe.average_score AS average_score
            FROM self_faculty_eval sfe
            WHERE sfe.faculty_id = :faculty_id
            ORDER BY sfe.faculty_id ASC
        ");
        $stmt->execute(['faculty_id' => $facultyId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create labels as sequential numbers (1, 2, 3, ...)
        $labels = range(1, count($data));
        $averageScores = array_column($data, 'average_score');

        echo json_encode(['labels' => $labels, 'dataset' => $averageScores]);

    } elseif ($category === 'self-head-faculty') {
        // Query for self head faculty evaluation data
        $stmt = $conn->prepare("
            SELECT she.average_score AS average_score
            FROM self_head_eval she
            WHERE she.faculty_id = :faculty_id
            ORDER BY she.faculty_id ASC
        ");
        $stmt->execute(['faculty_id' => $facultyId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create labels as sequential numbers (1, 2, 3, ...)
        $labels = range(1, count($data));
        $averageScores = array_column($data, 'average_score');

        echo json_encode(['labels' => $labels, 'dataset' => $averageScores]);

    } elseif ($category === 'faculty-to-faculty') {
        // Query for faculty ratings data
        $stmt = $conn->prepare("
            SELECT eaf.rate AS rating
            FROM evaluation_answers_faculty_faculty eaf
            WHERE eaf.faculty_id = :faculty_id AND eaf.rate IS NOT NULL
            ORDER BY eaf.evaluation_id ASC
        ");
        $stmt->execute(['faculty_id' => $facultyId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create labels as sequential numbers (1, 2, 3, ...)
        $labels = range(1, count($data));
        $ratings = array_column($data, 'rating');

        echo json_encode(['labels' => $labels, 'dataset' => $ratings]);

    } elseif ($category === 'faculty-to-head') {
        // Query for faculty ratings data
        $stmt = $conn->prepare("
            SELECT eah.rate AS rating
            FROM evaluation_answers_faculty_dean eah
            WHERE eah.faculty_id = :faculty_id AND eah.rate IS NOT NULL
            ORDER BY eah.evaluation_id ASC
        ");
        $stmt->execute(['faculty_id' => $facultyId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create labels as sequential numbers (1, 2, 3, ...)
        $labels = range(1, count($data));
        $ratings = array_column($data, 'rating');

        echo json_encode(['labels' => $labels, 'dataset' => $ratings]);

    } elseif ($category === 'head-to-faculty') {
        // Query for faculty ratings data
        $stmt = $conn->prepare("
            SELECT eahf.rate AS rating
            FROM evaluation_answers_dean_faculty eahf
            WHERE eahf.faculty_id = :faculty_id AND eahf.rate IS NOT NULL
            ORDER BY eahf.evaluation_id ASC
        ");
        $stmt->execute(['faculty_id' => $facultyId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create labels as sequential numbers (1, 2, 3, ...)
        $labels = range(1, count($data));
        $ratings = array_column($data, 'rating');

        echo json_encode(['labels' => $labels, 'dataset' => $ratings]);

    } else{
        // Invalid category
        echo json_encode(['error' => 'Invalid category']);
    }
} else {
    // Missing parameters
    echo json_encode(['error' => 'Missing parameters']);
}
?>
