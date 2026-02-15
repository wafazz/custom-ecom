<?php

namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';

class MemberController
{
    public function submitLoginRegister()
    {
        if (!isset($_SESSION['referral_url'])) {
            $_SESSION['referral_url'] = $_SERVER['HTTP_REFERER'] ?? '/';
        }

        $redirect = $_SESSION['referral_url'] ?? '/';
        unset($_SESSION['referral_url']);

        if (isset($_POST['loginButton'])) {
            $email = trim($_POST['loginEmail']);
            $password = trim($_POST['loginPassword']);

            $conn = getDbConnection();

            $sql = "SELECT * FROM `members` WHERE `email`=? AND `deleted_at` IS NULL LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if($user['verification_status'] == 'unconfirm') {
                    $_SESSION['login_error'] = "Please verify your email.";
                    header("Location: $redirect");
                    exit;
                }else if($user['status'] == 'inactive') {
                    $_SESSION['login_error'] = "Account is inactive. Please contact support.";
                    header("Location: $redirect");
                    exit;
                }else if($user['status'] == 'banned') {
                    $_SESSION['login_error'] = "Account is banned. Please contact support.";
                    header("Location: $redirect");
                    exit;
                }else if(password_verify($password, $user['password'])) {
                    // Password is correct, set session variables
                    $_SESSION['member_id'] = $user['id'];
                    $_SESSION['member_email'] = $user['email'];

                    header("Location: $redirect");
                    exit;
                } else {
                    // Invalid password
                    $_SESSION['login_error'] = "Invalid email or password.";
                    header("Location: $redirect");
                    exit;
                }
            } else {
                // User not found
                $_SESSION['login_error'] = "Invalid email or password.";
                header("Location: $redirect");
                exit;
            }
        }

        if (isset($_POST['registerButton'])) {

            $email = trim($_POST['registerEmail']);
            $password = $_POST['registerPassword'];
            $passwordConfirm = $_POST['registerPasswordConfirm'];
            $name = trim($_POST['registerName'] ?? '');
            $phone = trim($_POST['registerPhone'] ?? '');

            if ($email === '' || $password === '' || $passwordConfirm === '') {
                $_SESSION['register_error'] = 'All required fields must be filled.';
                header("Location: $redirect");
                exit;
            }

            if ($password !== $passwordConfirm) {
                $_SESSION['register_error'] = 'Passwords do not match.';
                header("Location: $redirect");
                exit;
            }

            $conn = getDbConnection();

            // 1️⃣ Check email
            $sql = "SELECT id FROM members WHERE email = ? AND deleted_at IS NULL LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();
                $conn->close();

                $_SESSION['register_error'] = 'Email already registered.';
                header("Location: $redirect");
                exit;
            }

            $stmt->close();

            // 2️⃣ Create verification code
            $verificationCode = random_int(100000, 999999);
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // 3️⃣ Insert member
            $sql = "INSERT INTO members
            (email, password, name, phone, verification_code, verification_status, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 'unconfirm', 'inactive', NOW(), NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssi",
                $email,
                $hashedPassword,
                $name,
                $phone,
                $verificationCode
            );
            $stmt->execute();

            $memberId = $stmt->insert_id;

            $stmt->close();
            $conn->close();

            // 4️⃣ Session (optional: limited access until verified)
            $_SESSION['member_id'] = $memberId;
            //$_SESSION['member_email'] = $email;
            $_SESSION['member_verified'] = 'unconfirm';

            // 5️⃣ Send verification email
            sendSecurityCode($email, $verificationCode);

            header('Location: /member-verify');
            exit;
        }
    }

    public function verifyMember()
    {
        if (isset($_COOKIE['country'])) {
            $country = intval($_COOKIE['country'] ?? 0);
        } else {
            header("Location: /");
            exit;
        }

        if (!isset($_SESSION['member_id'])) {
            header('Location: /checkout');
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Main";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);
        $successTicket = $_SESSION['success_ticket'] ?? null;
        $errorTicket   = $_SESSION['error_ticket'] ?? null;
        $ticketURL     = $_SESSION['ticketURL'] ?? null;



        require __DIR__ . '/../../view/ecom/e-member-verify-keya88.php';
    }

    public function processVerify()
    {
        if (!isset($_SESSION['member_id'])) {
            header('Location: /');
            exit;
        }

        if (isset($_POST['verifyButton'])) {
            $memberId = $_SESSION['member_id'];
            $codeParts = $_POST['code'] ?? [];
            $enteredCode = implode('', array_map('trim', $codeParts));

            if (strlen($enteredCode) !== 6 || !ctype_digit($enteredCode)) {
                $_SESSION['verify_error'] = 'Please enter a valid 6-digit code.';
                header('Location: /member-verify');
                exit;
            }

            $conn = getDbConnection();

            $sql = "SELECT verification_code FROM members WHERE id = ? AND deleted_at IS NULL LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $memberId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if ($enteredCode === strval($user['verification_code'])) {
                    // Update verification status
                    $updateSql = "UPDATE members SET verification_status = 'confirm', status = 'active', updated_at = NOW() WHERE id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("i", $memberId);
                    $updateStmt->execute();

                    // Clear verification session
                    unset($_SESSION['member_verified']);
                    $_SESSION['member_verified'] = 'confirm';
                    $_SESSION['member_email'] = $user['email'];

                    $_SESSION['verify_success'] = 'Verification successful. Please log in to continue.';
                    header('Location: /checkout');
                    exit;
                } else {
                    $_SESSION['verify_error'] = 'Incorrect verification code.';
                    header('Location: /member-verify');
                    exit;
                }
            } else {
                $_SESSION['verify_error'] = 'User not found.';
                header('Location: /member-verify');
                exit;
            }
        }
    }

    public function logout()
    {
        if (!isset($_SESSION['referral_url'])) {
            $_SESSION['referral_url'] = $_SERVER['HTTP_REFERER'] ?? '/';
        }

        $redirect = $_SESSION['referral_url'] ?? '/';
        unset($_SESSION['referral_url']);
        // Clear all session data
        unset($_SESSION['member_email']);
        unset($_SESSION['member_id']);
        unset($_SESSION['member_verified']);

        // Redirect to homepage or login page
        header("Location: $redirect");
        exit;
    }   
}
