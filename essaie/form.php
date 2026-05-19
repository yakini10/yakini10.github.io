<?php

require_once "../config.php";

try {

    $db = new PDO(

        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",

        DB_USER,

        DB_PASS

    );

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Erreur DB : " . $e->getMessage());

}

$method = $_SERVER['REQUEST_METHOD'];

/*
|--------------------------------------------------------------------------
| GET DATA
|--------------------------------------------------------------------------
*/

$contentType = $_SERVER["CONTENT_TYPE"] ?? "";

if (strpos($contentType, "application/json") !== false) {

    $data = json_decode(file_get_contents("php://input"), true);

} else {

    $data = $_POST;
}

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

$name = trim($data["name"] ?? "");
$phone = trim($data["phone"] ?? "");
$email = trim($data["email"] ?? "");
$comment = trim($data["comment"] ?? "");

$errors = [];

if ($name === "") {
    $errors[] = "Введите имя";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некорректный email";
}

if ($comment === "") {
    $errors[] = "Введите комментарий";
}

if (!empty($errors)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "errors" => $errors
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| POST
|--------------------------------------------------------------------------
*/

if ($method === "POST") {

    $stmt = $db->prepare("
        INSERT INTO requests(name, phone, email, comment)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $name,
        $phone,
        $email,
        $comment
    ]);

    $id = $db->lastInsertId();

    $login = "user" . $id;

    $password = substr(md5(rand()), 0, 8);

    echo json_encode([

        "success" => true,

        "message" => "Форма успешно отправлена",

        "login" => $login,

        "password" => $password,

        "profile_url" => "/api/form.php?id=" . $id

    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| PUT
|--------------------------------------------------------------------------
*/

if ($method === "PUT") {

    parse_str($_SERVER['QUERY_STRING'], $query);

    $id = $query["id"] ?? null;

    if (!$id) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "ID required"
        ]);

        exit;
    }

    $stmt = $db->prepare("
        UPDATE requests
        SET name=?, phone=?, email=?, comment=?
        WHERE id=?
    ");

    $stmt->execute([
        $name,
        $phone,
        $email,
        $comment,
        $id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Данные обновлены"
    ]);

    exit;
}
