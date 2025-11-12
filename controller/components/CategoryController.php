<?php
/**
 * Category Controller for BackOffice
 * Author: Hichem Challakhi
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../model/config/data-base.php';
include_once '../../model/Innovation/Category.php';

$database = new Database();
$db = $database->getConnection();
$category = new Category($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            // Read single category
            $category->id = $_GET['id'];
            
            if($category->readOne()) {
                $category_arr = array(
                    "id" => $category->id,
                    "nom" => $category->nom,
                    "description" => $category->description,
                    "date_creation" => $category->date_creation
                );
                http_response_code(200);
                echo json_encode($category_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Category not found."));
            }
        } else {
            // Read all categories
            $stmt = $category->read();
            $num = $stmt->rowCount();

            if($num > 0) {
                $categories_arr = array();
                $categories_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $category_item = array(
                        "id" => $id,
                        "nom" => $nom,
                        "description" => $description,
                        "date_creation" => $date_creation
                    );
                    array_push($categories_arr["records"], $category_item);
                }

                http_response_code(200);
                echo json_encode($categories_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("records" => array()));
            }
        }
        break;

    case 'POST':
        // Create category
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nom)) {
            $category->nom = $data->nom;
            $category->description = isset($data->description) ? $data->description : "";
            $category->date_creation = date('Y-m-d H:i:s');

            if($category->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Category created successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create category."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create category. Data is incomplete."));
        }
        break;

    case 'PUT':
        // Update category
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->id) && !empty($data->nom)) {
            $category->id = $data->id;
            $category->nom = $data->nom;
            $category->description = isset($data->description) ? $data->description : "";

            if($category->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Category updated successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update category."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update category. Data is incomplete."));
        }
        break;

    case 'DELETE':
        // Delete category
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->id)) {
            $category->id = $data->id;

            if($category->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Category deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete category."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to delete category. Data is incomplete."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
