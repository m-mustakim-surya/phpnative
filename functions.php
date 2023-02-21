<?php
// koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "mahasiswa");

function query($query){
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data){
    global $conn;
    $nim = $data["nim"];
    $email = $data["email"];
    $jurusan = $data["jurusan"];
    $nama = $data["nama"];

    // upload gambar
    $gambar = upload();
    if(!$gambar){
        return false;
    }

    // query insert data
    $query = "INSERT INTO tabel_mahasiswa VALUES ('', '$gambar', '$nim', '$email', '$jurusan', '$nama')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function upload(){
    $namaFile = $_FILES["gambar"]["name"];
    $ukuranFile = $_FILES["gambar"]["size"];
    $error = $_FILES["gambar"]["error"];
    $tmpName = $_FILES["gambar"]["tmp_name"];

    // cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ["jpg", "jpeg", "png", "svg"];
    $ekstensiGambar = explode(".", $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if(!in_array($ekstensiGambar, $ekstensiGambarValid)){
        echo "<script>
                alert('Yang anda upload bukan gambar');
            </script>";
        return false;
    }

    // generate nama gambar baru
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;

    // lolos pengecekan, gambar siap diupload
    move_uploaded_file($tmpName, "img/" . $namaFileBaru);
    return $namaFileBaru;
}


function hapus($id){
    global $conn;
    mysqli_query($conn, "DELETE FROM tabel_mahasiswa WHERE id = $id");
    
    return mysqli_affected_rows($conn);
}

function edit($data){
    global $conn;
    $id = $data["id"];
    $nim = $data["nim"];
    $email = $data["email"];
    $jurusan = $data["jurusan"];
    $nama = $data["nama"];
    $gambarLama = $data["gambarLama"];
    
    // cek apakah user pilih gambar baru atau tidak
    if($_FILES["gambar"]["error"] === 4){
        $gambar = $gambarLama;
    } else {
        $gambar = upload();
    }


    // query insert data
    $query = "UPDATE tabel_mahasiswa SET
                gambar = '$gambar',
                nim = '$nim',
                email = '$email',
                jurusan = '$jurusan',
                nama = '$nama'
            WHERE id = $id
                ";
    mysqli_query($conn, $query);
    
    return mysqli_affected_rows($conn);
}

?>
