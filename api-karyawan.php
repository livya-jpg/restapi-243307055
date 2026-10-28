
<?php
require_once "connection.php";

$function = $_GET['function'] ?? '';

switch ($function) {
    case 'getKaryawan':
        getKaryawan();
        break;

    case 'insertKaryawan':
        insertKaryawan();
        break;

    case 'getKaryawanById':
        getKaryawanById();
        break;

    case 'updateKaryawan':
        updateKaryawan();
        break;

    case 'deleteKaryawan':
        deleteKaryawan();
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 400,
            'message' => 'Function tidak valid'
        ]);
}

function getKaryawan() {
    global $connect;

    $query = $connect->query("SELECT * FROM tbl_karyawan");
    $data = [];

    while ($row = mysqli_fetch_object($query)) {
        $data[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 200,
        'message' => 'Success',
        'data' => $data
    ]);
}

function insertKaryawan() {
    global $connect;

    $nama = $_POST['nama'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $handphone = $_POST['handphone'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    if (
        $nama === '' ||
        !in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'], true) ||
        $handphone === '' ||
        $alamat === ''
    ) {
        $response = [
            'status' => 405,
            'message' => 'Parameter tidak lengkap atau tidak valid'
        ];
    } else {
        $stmt = $connect->prepare(
            "INSERT INTO tbl_karyawan
            (nama, jenis_kelamin, handphone, alamat)
            VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssss", $nama, $jenis_kelamin, $handphone, $alamat
        );

        if ($stmt->execute()) {
            $response = [
                'status' => 201,
                'message' => 'Insert Success',
                'id' => $connect->insert_id
            ];
        } else {
            $response = [
                'status' => 400,
                'message' => 'Insert Failed'
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function getKaryawanById() {
    global $connect;

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        $response = [
            'status' => 400,
            'message' => 'ID tidak valid'
        ];
    } else {
        $stmt = $connect->prepare(
            "SELECT * FROM tbl_karyawan WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ];
        } else {
            http_response_code(404);
            $response = [
                'status' => 404,
                'message' => 'No Data Found'
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function updateKaryawan() {
    global $connect;

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $nama = $_POST['nama'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $handphone = $_POST['handphone'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    if (
        !$id || $id < 1 ||
        $nama === '' ||
        !in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'], true) ||
        $handphone === '' ||
        $alamat === ''
    ) {
        $response = [
            'status' => 405,
            'message' => 'Parameter tidak lengkap atau tidak valid'
        ];
    } else {
        $stmt = $connect->prepare(
            "UPDATE tbl_karyawan
            SET nama = ?, jenis_kelamin = ?, handphone = ?, alamat = ?
            WHERE id = ?"
        );
        $stmt->bind_param(
            "ssssi", $nama, $jenis_kelamin, $handphone, $alamat, $id
        );

        if ($stmt->execute()) {
            $response = [
                'status' => 200,
                'message' => 'Update Success'
            ];
        } else {
            $response = [
                'status' => 400,
                'message' => 'Update Failed'
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function deleteKaryawan() {
    global $connect;

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        $response = [
            'status' => 400,
            'message' => 'ID tidak valid'
        ];
    } else {
        $stmt = $connect->prepare(
            "DELETE FROM tbl_karyawan WHERE id = ?"
        );
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $response = [
                'status' => 200,
                'message' => 'Delete Success'
            ];
        } else {
            $response = [
                'status' => 404,
                'message' => 'Data tidak ditemukan atau gagal dihapus'
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
