<?php
include "model/account.php";
include "model/bds.php";
include "model/loai_bds.php";
include "model/pdo.php";
include "model/tuvan.php";
include "model/tintuc.php";
include "model/danhmuc_tintuc.php";
include "mail/sendMail.php";
// include "model/user.php";
$ds_bds = loadall_bds();
$bds_new = loadall_bds_home();
$loaibds = loadAll_danhmuc();
$danhmuctt = loadAll_danhmuctintuc();
session_start();

if (isset($_GET['act']) && ($_GET['act'] != "")) {
    $act = $_GET['act'];
    switch ($act) {
        case 'batdongsanchitiet':
            if (isset($_GET['idbds']) && ($_GET['idbds'] > 0)) {
                $url_bds = $_SERVER['REQUEST_URI'];
                $id = $_GET['idbds'];
                tang_view($id);
                $onebds = loadone_bds($id);
                extract($onebds);
                $anhmota = load_anhmota($id);
                $oneuser = loadOne_user($id_user);
                $bds_cungloai = load_bds_cungloai($id, $id_loaibds);
                include "view/bds_chitiet.php";
            } else {
                include "view/batdongsan.php";
            }
            break;

        case 'batdongsan':
            if (isset($_POST['kyw']) && ($_POST['kyw'] != "")) {
                $kyw = $_POST['kyw'];
            } else {
                $kyw = "";
            }
            if (isset($_GET['id_loaibds']) && ($_GET['id_loaibds'] > 0)) {
                $id_loaibds = $_GET['id_loaibds'];
            } else {
                $id_loaibds = 0;
            }
            $ds_bds = loadall_bds($kyw, $id_loaibds);
            include 'view/batdongsan.php';
            break;
        case 'home':
            include 'view/home.php';
            break;
        case 'detail':
            // code  vao day

            include 'view/detail.php';
            break;

        case 'hotdeals':
            if (isset($_POST['location']) && ($_POST['location'] != "")) {
                $kyw = $_POST['location'];
            } else {
                $kyw = "";
            }
            // code  vao day
            // $id_loaibds = $_POST['loaibds'];
            $dstop5 = loadAll_bds_top5();
            include 'view/hotdeals.php';
            break;

        case 'features':
            include 'view/features.php';
            break;

        case 'tintucchitiet':
            if (isset($_GET['idtt']) && ($_GET['idtt'] > 0)) {
                $id = $_GET['idtt'];
                $onett = loadOne_tintuc($id);
                extract($onett);

                $tintuc_cungloai = load_tintuc_cungloai($id, $id_danhmuctt);
                include "view/tintucchitiet.php";
            } else {
                include "view/blog.php";
            }
            break;


        case 'blog':
            // code  vao day
            if (isset($_POST['kyw']) && ($_POST['kyw'] != "")) {
                $kyw = $_POST['kyw'];
            } else {
                $kyw = "";
            }
            if (isset($_GET['id_tintuc']) && ($_GET['id_tintuc'] > 0)) {
                $id_dm_tintuc = $_GET['id_tintuc'];
            } else {
                $id_dm_tintuc = 0;
            }

            $ds_tt = loadAll_tintuc($kyw, $id_dm_tintuc);
            include 'view/blog.php';
            break;

        case 'contact':
            // code  vao day
            require 'view/account/validate.php';
            if (isset($_POST['submit']) && ($_POST['submit'])) {
                if (empty($_POST['name'])) {
                    $error['name'] = "B???n ch??a nh???p v??o h??? t??n";
                }
                if (empty($_POST['email'])) {
                    $error['email'] = "B???n ch??a nh???p v??o email";
                } else if (!is_email($_POST['email'])) {
                    $error['email'] = "?????nh d???ng email kh??ng ????ng";
                }
                if (empty($_POST['message'])) {
                    $error['message'] = "B???n ch??a nh???p v??o l???i nh???n";
                }

                if (empty($error)) {
                    $thongbao = "B???n ???? g???i y??u c???u li??n h??? th??nh c??ng !";
                }
            }
            include 'view/contact.php';
            break;
        case 'listtuvan':
            $id_nhanvien = $_SESSION['user']['id'];
            $listtuvan = load_Bds_Tuvan_Nhanvien($id_nhanvien);
            include 'view/batdongsantuvan.php';
            break;

        case 'tuvan':

            if (isset($_POST['tuvan']) && $_POST['tuvan']) {
                $id_user = $_POST['id_login'];
                $note_user = $_POST['note_user'];
                $thongbao = "";
                $error = array();
                $id_bds = $_POST['id_bds'];
                date_default_timezone_set("Asia/Ho_Chi_Minh");
                $time_yeucau = date('h:i:sa d/m/Y');

                if (empty($note_user)) {
                    $linkbdschitiet = 'index.php?act=batdongsanchitiet&idbds=' . $id_bds;
                    header('location:' . $linkbdschitiet);
                }

                else {
                    insert_bds_tuvan($id_user, $note_user, $id_bds, $time_yeucau);
                    $linkbdschitiet = 'index.php?act=batdongsanchitiet&idbds=' . $id_bds;
                    header('location:' . $linkbdschitiet);
                }
            }
            break;
        case 'dangky':
            require 'view/account/validate.php';
            if (isset($_POST['dangky']) && ($_POST['dangky'])) {
                $email = $_POST['email'];
                $hoten = $_POST['hoten'];
                $user = $_POST['user'];
                $tel = $_POST['tel'];
                $pass = md5($_POST['pass']);
                $repass = md5($_POST['repass']);
                $checkemail = check_email($email);

                if (empty($hoten)) {
                    $error['hoten'] = "Kh??ng ???????c b??? tr???ng h??? t??n";
                }

                if (empty($user)) {
                    $error['user'] = "Kh??ng ???????c b??? tr???ng t??n ????ng nh???p";
                }

                if (empty($email)) {
                    $error['email'] = "Kh??ng ???????c b??? tr???ng email";
                } else if (!is_email($email)) {
                    $error['email'] = "?????nh d???ng email kh??ng ????ng";
                } else if ($checkemail) {
                    $error['email'] = "Email ???? ???????c d??ng ????? ????ng k?? t??i kho???n kh??c. Vui l??ng ch???n m???t email kh??c";
                }

                if (empty($pass)) {
                    $error['pass'] = "B???n ch??a nh???p v??o m???t kh???u";
                }

                if (empty($repass)) {
                    $error['repass'] = "Kh??ng ???????c b??? tr???ng nh???p l???i m???t kh???u";
                } else if ($repass != $pass) {
                    $error['repass'] = "Nh???p l???i m???t kh???u kh??ng ch??nh x??c";
                }

                if (empty($tel)) {
                    $error['tel'] = "Kh??ng ???????c b??? tr???ng s??? ??i???n tho???i";
                } else if (!is_tel($tel)) {
                    $error['tel'] = "?????nh d???ng s??? ??i???n tho???i kh??ng ph?? h???p";
                }

                if (empty($error)) {
                    $thongbao = "";
                    if ($pass == $repass) {
                        insert_account($email, $hoten, $tel, $user, $pass);
                        $thongbao = "????ng k?? th??nh c??ng. ????ng nh???p ????? s??? d???ng ch???c n??ng !";
                    } else {
                        $thongbao = "Nh???p l???i m???t kh???u kh??ng ????ng. Vui l??ng nh???p l???i ch??nh x??c ????? ????ng k??!";
                    }
                }
            }
            include "view/account/register.php";
            break;
        case 'dangnhap':
            
            $url_bds = $_GET;
            if (isset($_GET['url'])) {
                setcookie('url_bds', $_GET['url'], time() + 3600);
                setcookie('id_bds', $_GET['idbds'], time() + 3600);
            }
            if (isset($_POST['dangnhap'])) {
                if (empty($_POST['user'])) {
                    $error['user'] = 'Kh??ng ???????c b??? tr???ng t??n ????ng nh???p';
                }

                if (empty($_POST['pass'])) {
                    $error['pass'] = 'Kh??ng ???????c b??? tr???ng m???t kh???u';
                }

                if (empty($error)) {
                    $user = isset($_POST['user']) ? $_POST['user'] : '';
                    $pass = isset($_POST['pass']) ? md5($_POST['pass']) : '';

                    $checkuser = checkuser($user, $pass);
                    if (isset($_COOKIE['url_bds']) && ($_COOKIE['id_bds'])) {
                        if (is_array($checkuser)) {
                            $_SESSION['user'] = $checkuser;
                            if ($_SESSION['user']['id_role'] == 1) {
                                header('location:admin/index.php');
                            }else {
                                header('location:' . $_COOKIE['url_bds'] . '&idbds=' . $_COOKIE['id_bds']);
                                setcookie("url_bds", "", time() - 3600);
                                setcookie("id_bds", "", time() - 3600);
                            }
                        } else {
                            $thongbao = "T??i kho???n kh??ng t???n t???i vui l??ng ki???m tra ho???c ????ng k?? m???i";
                        }
                    } else {

                        if (is_array($checkuser)) {

                            $_SESSION['user'] = $checkuser;
                            if ($_SESSION['user']['id_role'] == 1) {
                                header('location:admin/index.php');
                            }else if($_SESSION['user']['id_role'] == 2){
                                header('location:index.php?act=listtuvan');
                            }  else {
                                header('location:index.php');
                            }
                        } else {
                            $thongbao = "T??i kho???n kh??ng t???n t???i vui l??ng ki???m tra ho???c ????ng k?? m???i";
                        }
                    }
                }
            }
            include "view/account/login.php";
            break;
        case 'edit_taikhoan':
            if (isset($_POST['capnhat']) && ($_POST['capnhat'])) {
                $user = $_POST['user'];
                $pass = md5($_POST['pass']);
                $email = $_POST['email'];
                $address = $_POST['address'];
                $tel = $_POST['tel'];
                $id = $_POST['id'];

                update_taikhoan($id, $user, $pass, $email, $address, $tel);
                $_SESSION['user'] = checkuser($user, $pass);
                // header('Location: index.php?act=edit_taikhoan');
            }
            include "view/account/edit_taikhoan.php";
            break;
        case 'quenmk':
            require 'view/account/validate.php';
            if (isset($_POST['gui'])) {
                if (isset($_SESSION['user'])) {
                    $test = $_SESSION['user'];
                    extract($_SESSION['user']);
                }
                $error = array();
                $thongbao = "";
                $email = $_POST['email'];
                $checkemail = check_email($email);
                if ($email == '') {
                    $thongbao = 'Kh??ng ???????c ????? tr???ng email';
                } else if (!is_email($email)) {
                    $thongbao = "?????nh d???ng email kh??ng ????ng";
                } else if (!$checkemail) {
                    $thongbao = "T??i kho???n kh??ng t???n t???i";
                }

                if (empty($error)) {
                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    function generate_string($input, $strength = 16)
                    {
                        $input_length = strlen($input);
                        $random_string = '';
                        for ($i = 0; $i < $strength; $i++) {
                            $random_character = $input[mt_rand(0, $input_length - 1)];
                            $random_string .= $random_character;
                        }
                        return $random_string;
                    }
                    $password = generate_string($permitted_chars, 8);
                    //    $code = substr(rand(0, 999999) . 0, 6);
                    $title = "L???y l???i m???t kh???u";
                    $content = "M???t kh???u m???i c???a b???n l??: <span style = 'color:grren'>" . $password . "</span>";
                    guiMailBDS($title, $content, $email);
                    if ($checkemail) {
                        $passkh = md5($password);
                        update_mk($passkh, $email);
                        $thongbao = "M???t m???t kh???u m???i ???? ???????c g???i cho b???n. Vui l??ng ki???m tra email !";
                    }
                }
            }
            include "view/account/quenmk.php";
            break;
        case 'exit':
            session_unset();
            include "view/home.php";
            break;
    }
} else {
    include "view/home.php";
}
