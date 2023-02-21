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

function registrasi($data){
    global $conn;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);

    // cek username sudah ada atau belum
    $result = mysqli_query($conn, "SELECT username FROM tabel_users WHERE username = '$username'");
    if(mysqli_fetch_assoc($result)){
        echo "<script>
                alert('Username yang dipilih sudah terdaftar');
            </script>";
        return false;
    }

    // cek konfirmasi password
    if($password !== $password2){
        echo "<script>
                alert('Konfirmasi password tidak sesuai!');
            </script>";
        return false;
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);
    
    // tambahkan user baru ke database
    mysqli_query($conn, "INSERT INTO tabel_users VALUES('', '$username', '$password')");

    return mysqli_affected_rows($conn);
}

?>
