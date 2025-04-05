<?php
// import-images.php
require_once('wp-load.php');

$password = '12345'; // Change this password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pass']) && $_POST['pass'] === $password) {

        function register_bulk_uploaded_images() {
            $upload_dir = wp_upload_dir();
            $folder = $upload_dir['basedir'] . '/2025/04'; // Change folder if needed

            $images = glob($folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            $count = 0;

            foreach ($images as $image_path) {
                $filetype = wp_check_filetype(basename($image_path), null);

                $attachment = array(
                    'guid'           => $upload_dir['baseurl'] . '/2025/04/' . basename($image_path),
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace('/\.[^.]+$/', '', basename($image_path)),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                $existing = get_page_by_title($attachment['post_title'], OBJECT, 'attachment');
                if (!$existing) {
                    $attach_id = wp_insert_attachment($attachment, $image_path);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    $count++;
                }
            }

            echo "<p style='color: green; font-weight: bold;'>$count images imported successfully!</p>";
        }

        register_bulk_uploaded_images();
    } else {
        echo "<p style='color: red; font-weight: bold;'>Incorrect password!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bulk Image Importer (Secure)</title>
</head>
<body style="font-family: sans-serif; text-align: center; margin-top: 100px;">
    <h2>Bulk Import Images to Media Library</h2>
    <form method="post">
        <input type="password" name="pass" placeholder="Enter password" required style="padding: 8px; font-size: 16px;">
        <br><br>
        <button type="submit" style="padding: 10px 20px; font-size: 18px;">Import Images</button>
    </form>
</body>
</html>
